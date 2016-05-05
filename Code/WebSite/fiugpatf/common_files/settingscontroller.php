<?php
include_once 'dbconnector.php';
include_once 'toLog.php';

class SettingsController
{
    protected $user;
    protected $userName;
    protected $log;

    public function __construct($user, $userName)
    {
        $this->user = $user;
        $this->userName = $userName;
        $this->log = new ErrorLog();
    }

    public function importAudit()
    {
        $username = $this->userName;

        if (!file_exists($username)) {
            mkdir($username, 0777);
            $loc = $_FILES['file']['tmp_name'];
            shell_exec('qpdf --password="" --decrypt ' . $loc . ' ' . $username . '/unencrypted.pdf');
            $courseInfo = shell_exec('python PDFWhatIfParser.py ' . $username . '/unencrypted.pdf');
            echo 'python PDFWhatIfParser.py ' . $username . '/unencrypted.pdf';

            $this->log->toLog(0, __METHOD__, "$courseInfo");
            $allData = explode("!!!!", $courseInfo);
            $majorData = explode("\n", $allData[0]);
            $gpa = explode("\n", $allData[1]);
            $courses = explode("\n", $allData[2]);
            $uccComplete = explode("\n", $allData[3]);

            if ($this->checkFirstTime()) {
                $this->insertMajor($majorData);
                $this->insertCourses($courses);
                $this->instantiateNeeded($courses, $uccComplete[1]);
            } else {
                $this->update($courses);
            }

            $this->insertGPA($gpa);

            shell_exec('rm -rf ' . $username);

            $this->log->toLog(1, __METHOD__, "GPA Audit Imported");
        }
    }

    public function testStub($courses, $ucc)
    {
        if ($this->checkFirstTime()) {
            $this->insertCourses($courses);
            $this->instantiateNeeded($courses, $ucc);
        } else {
            $this->update($courses);
        }
    }

    private function insertMajor($majorData)
    {
        $dbc = new DatabaseConnector();
        foreach ($majorData as $maj) {
            if ($maj != "") {
                $stmt = "INSERT INTO StudentMajor (userID, majorID) VALUES (?, (SELECT majorID from Major WHERE majorName = ?))";
                $params = array($this->user, $maj);
                $dbc->query($stmt, $params);
            }
        }
    }

    private function insertGPA($gpa) {
        $dbc = new DatabaseConnector();

        foreach( $gpa as $g) {
            if ($g != "") {
                $params = array($g, $this->user);
                $dbc->query("UPDATE Users set gpa = ? Where userID =?", $params);

                $this->log->toLog(0, __METHOD__, "gpa updated: $g");
            }
        }
    }

    private function insertCourses($courses)
    {
        $conn = new DatabaseConnector();

        foreach ($courses as $course) {
            if ($course == "")
                continue;

            $courseDetails = explode("$$&&", $course);
            $semester = $courseDetails[0];
            $year = $courseDetails[1];
            $courseID = $courseDetails[2];
            $courseName = $courseDetails[3];
            $grade = $courseDetails[4];
            $credits = $courseDetails[5];
            $ctype = $courseDetails[6];

            if ($ctype == 'TR' or $ctype == 'OT')
                continue;

            //check if course is in database
            $params = array($courseID);
            $courseInfoID = $conn->select("SELECT courseInfoID FROM CourseInfo WHERE courseID = ?", $params);

            // if course is not in database then insert
            if (count($courseInfoID) == 0) {
                $params = array($courseID, $courseName, $credits);
                $conn->query("INSERT INTO CourseInfo (courseID, courseName, credits) VALUES (?, ?, ?)", $params);
            }

            //insert course
            $params = array($grade, $semester, $year, $courseID, $this->user);
            $conn->query("INSERT INTO StudentCourse (grade, weight, relevance, semester, year,
           courseInfoID, selected, userID) VALUES (?, 0, 0, ?, ?, (SELECT CourseInfoID FROM CourseInfo
           WHERE courseID = ?), 0, ?)", $params);
            $this->log->toLog(0, __METHOD__, "Course: $courseID inserted for user: $this->user");
        }
    }

    public function instantiateNeeded($courses, $uccComplete)
    {
        $conn = new DatabaseConnector();

        $takenCourses = array();
        foreach ($courses as $course) {
            if ($course == "")
                continue;
            $courseDetails = explode("$$&&", $course);
            array_push($takenCourses, array($courseDetails[2], $courseDetails[4]));
        }

        $param = array($this->user);
        $buckets = $conn->select("SELECT MajorBucket.bucketID, MajorBucket.allRequired, MajorBucket.quantityNeeded,
                          MajorBucket.quantification, MajorBucket.description FROM MajorBucket WHERE MajorBucket.parentID IS NULL AND
                          MajorBucket.majorID IN (SELECT StudentMajor.majorID FROM StudentMajor
                          WHERE StudentMajor.userID = ?)", $param);

        foreach ($buckets as $bucket) {
            if ($bucket[4] == 'UCC' and $uccComplete)
                continue;

            $this->checkBucket($takenCourses, $bucket);
        }
    }

    public function checkBucket($takenCourses, $bucket)
    {
        $conn = new DatabaseConnector();

        $params = array($bucket[0]);
        $childBuckets = $conn->select("SELECT bucketID, allRequired, quantityNeeded, quantification, description
        FROM MajorBucket WHERE MajorBucket.parentID = ?", $params);

        if (count($childBuckets) > 0) {
            foreach ($childBuckets as $childBucket)
                $this->checkBucket($takenCourses, $childBucket, $this->user);
        } else {
            $bucketCourses = $conn->select("SELECT CourseInfo.courseID, CourseInfo.credits, CourseInfo.courseInfoID,
            MajorBucketRequiredCourses.minimumGrade FROM CourseInfo INNER JOIN MajorBucketRequiredCourses on
            CourseInfo.courseInfoID = MajorBucketRequiredCourses.courseInfoID
            WHERE MajorBucketRequiredCourses.bucketID = ?", $params);

            $counter = 0;
            $coursesNotTaken = array();
            $bucketCompleted = false;

            foreach ($bucketCourses as $bucketCourse) {
                $passed = false;

                $keys = $this->search($takenCourses, '0', $bucketCourse[0]);

                foreach ($keys as $key) {
                    $grade = $this->convertGrade($key[1]);
                    $minGrade = $this->convertGrade($bucketCourse[3]);

                    if ($minGrade > $grade)
                        continue;

                    if ($bucket[3] == "credits")
                        $counter += $bucketCourse[1];
                    else
                        $counter++;
                    $passed = true;
                    break;
                }

                if (!$passed)
                    array_push($coursesNotTaken, $bucketCourse[2]);

                if ($counter >= $bucket[2]) {
                    $bucketCompleted = true;
                    $this->log->toLog("0", __METHOD__, "Bucket: $bucket[0] Completed");
                    break;
                }
            }

            if (!$bucketCompleted) {
                $this->log->toLog("0", __METHOD__, "Bucket: $bucket[0] not completed");
                foreach ($coursesNotTaken as $courseNotTaken) {
                    $params = array($courseNotTaken, $this->user);
                    $conn->query("INSERT INTO StudentCourse (grade, weight, relevance, semester, year, courseInfoID,
                selected, userID) VALUES ('ND', 0, 0, '', '', ?, 0, ?)", $params);
                }
            }
        }
    }

    public function checkFirstTime()
    {
        $conn = new DatabaseConnector();

        $params = array($this->user);

        $output = $conn->select("SELECT * FROM StudentCourse WHERE StudentCourse.userID = ?", $params);

        if (count($output) > 0)
            return false;
        else
            return true;
    }

    private function convertGrade($grade)
    {
        switch ($grade) {
            case 'A':
                return 4.0;
                break;
            case 'A-':
                return 3.7;
                break;
            case 'B+':
                return 3.3;
                break;
            case 'B':
                return 3.0;
                break;
            case 'B-':
                return 2.7;
                break;
            case 'C+':
                return 2.3;
                break;
            case 'C':
                return 2.0;
                break;
            case 'C-':
                return 1.7;
                break;
            case 'D+':
                return 1.3;
                break;
            case 'D':
                return 1.0;
                break;
            case 'D-':
                return .7;
                break;
            case 'F':
                return 0;
                break;
            case 'IP':
                return 5;
                break;

        }
    }

    public function update($courses)
    {
        $conn = new DatabaseConnector();

        foreach ($courses as $course) {
            if ($course == "")
                continue;

            $courseDetails = explode("$$&&", $course);
            $semester = $courseDetails[0];
            $year = $courseDetails[1];
            $courseID = $courseDetails[2];
            $courseName = $courseDetails[3];
            $grade = $courseDetails[4];
            $credits = $courseDetails[5];
            $ctype = $courseDetails[6];

            if ($ctype == 'TR' or $ctype == 'OT')
                continue;

            //check if course was already taken
            $params = array($grade, $semester, $year, $this->user, $courseID);
            $out = $conn->select("SELECT * FROM StudentCourse WHERE grade = ? and semester = ? and year = ?
          and userID = ? and courseInfoID = (SELECT courseInfoID FROM CourseInfo Where courseID = ?)", $params);

            if (count($out) > 0)
                continue;

            //check if course is IP or ND
            $params = array($this->user, $courseID);
            $out = $conn->select("SELECT grade, weight, relevance, studentCourseID, semester, year, courseInfoID FROM
          StudentCourse WHERE userID = ? and (grade = 'ND' OR grade = 'IP') AND courseInfoID = (SELECT courseInfoID FROM CourseInfo
          Where courseID = ?)", $params);

            if (count($out) > 0) {
                $params = array($grade, $out[0][3]);
                $conn->query("UPDATE StudentCourse SET grade = ? WHERE studentCourseID = ?", $params);

                $params = array($courseID);
                $minGrade = $conn->select("SELECT minimumGrade FROM MajorBucketRequiredCourses
              WHERE courseInfoID = (SELECT courseInfoID FROM CourseInfo Where courseID = ?)", $params);

                if ($this->convertGrade($grade) < $this->convertGrade($minGrade[0])) {
                    $params = array($out[1], $out[2], $out[6], $this->user);
                    $conn->query("INSERT INTO StudentCourse (grade, weight, relevance, semester, year, courseInfoID,
                selected, userID) VALUES ('ND', ?, ?, '', '', ?, 0, ?)", $params);
                    $this->log->toLog(0, __METHOD__, "Updated course $courseID");
                    continue;
                }

                continue;
            }

            $params = array($courseID);
            $out = $conn->select("SELECT courseInfoID FROM CourseInfo WHERE courseID = ?", $params);
            $this->log->toLog(0, __METHOD__, "Insert course $courseID");

            if (count($out) == 0) {
                $params = array($courseID, $courseName, $credits);
                $conn->query("INSERT INTO CourseInfo (courseID, courseName, credits)	VALUES (?, ?, ?)", $params);
            }

            $params = array($courseID);
            $courseInfoID = $conn->select("SELECT courseInfoID FROM CourseInfo WHERE courseID = ?", $params);


            $params = array($grade, $semester, $year, $courseInfoID[0][0], $this->user);

            $conn->query("INSERT INTO StudentCourse (grade, weight, relevance, semester, year, courseInfoID,
                selected, userID) VALUES (?, 0, 0, ?, ?, ?, 0, ?)", $params);
        }
    }

    public function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }
        return $results;
    }

    public function prepareTable()
    {
        $db = new DatabaseConnector();
        header('Content-type: application/json');

        $params = array($this->user);
        $stmt = $db->select("SELECT type FROM Users WHERE userID=?", $params);

        $output = array();
        array_push($output, array("Change Password", ""));
        array_push($output, array("Change Major", ""));
        array_push($output, array("Change Themes", ""));
        array_push($output, array("Export Data", '<button type="button" id="ExportButton">Export Data</button>'));
        array_push($output, array("Import Data", '<input type="file" id="ImportFile">'));
        array_push($output, array("Delete Data", '<button type="button" id="DeleteButton">Delete Data</button>'));
        array_push($output, array("Import GPA Audit (PDF)", '<form id="PDFimport" action="router.php"
        enctype="multipart/form-data" method="post"><input type="file" name="file" id="Whatif"><input type="hidden"
        name="action" value="importAudit"></form>'));

        if ($stmt[0][0] == 1) {
            array_push($output, array("Import Requirments", '<form id="Reqimport" action="router.php"
            enctype="multipart/form-data" method="post" datatype="json"><input type="file" name="file" id="ImportReqirments"><input
            type="hidden" name="action" value="importReq"></form>'));
        }

        echo json_encode($output);
    }

    public function importReq($fileName)
    {
        $file = file_get_contents($fileName);
        libxml_use_internal_errors(true);
        $adminData = simplexml_load_string($file);

        if ($adminData === false) {
            $response_array[0] = 'error';
            echo json_encode($response_array);
            return;
        }

        $db = new DatabaseConnector();
        foreach ($adminData->children() as $details) {
            if ($details->getName() == 'programName')
                $majorName = $details;
            elseif ($details->getName() == "minGPA")
                $minGPA = $details;
            elseif ($details->getName() == "activeDate") {
                $activeDate = $details;

                $params = array($majorName, $activeDate, $minGPA);
                $db->query("INSERT INTO Major (majorName, majorID, activeDate, minGPA) VALUES (?, NULL, ? ,?)", $params);

                $params = array($majorName, $activeDate);
                $stmt = $db->select("SELECT majorID FROM Major WHERE majorName = ? and activeDate = ?", $params);
                $majorID = $stmt[0][0];
                $this->log->toLog(0, __METHOD__, "program imported: name:$majorName, activeDate:$activeDate, minGPA:$minGPA ");
            } elseif ($details->getName() == 'bucket') {
                $this->importBucket($details, null, $majorID);
            }
        }

        $this->log->toLog(1, __METHOD__, "Curriculum:$majorName imported successfully");
        $response_array[0] = 'success';
        echo json_encode($response_array);
    }

    public function importBucket($bucket, $parentID, $majorID)
    {
        $db = new DatabaseConnector();

        foreach ($bucket->children() as $details) {
            if ($details->getName() == 'data') {

                if ($parentID == null) {
                    $params = array($majorID, $details->description, $details->allRequired,
                        $details->quantity, $details->quantification);
                    $db->query("INSERT INTO MajorBucket (majorID, description, allRequired,
                          quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, null) ON DUPLICATE
                          KEY UPDATE allRequired = VALUES (allRequired), quantification=VALUES(quantification),
                          parentID=VALUES(parentID)", $params);
                    $this->log->toLog(0, __METHOD__, "bucket imported majorID: $majorID, description: $details->description, req:$$details->allRequired, quantity:$details->quantity, quantification$$details->quantification");
                } else {
                    $params = array($majorID, $details->description, $details->allRequired, $details->quantity,
                        $details->quantification, $parentID);
                    $db->query("INSERT INTO MajorBucket (majorID, description, allRequired,
                        quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY
                        UPDATE allRequired=VALUES(allRequired), quantification=VALUES(quantification),
                        parentID=VALUES(parentID)", $params);
                    $this->log->toLog(0, __METHOD__, "bucket imported - majorID: $majorID, start:$details->dateStart, end:$details->dateEnd, description: $details->description, req:$details->allRequired, quantity:$details->quantity, quantification$details->quantification, parent: $parentID");
                }

                $params = array($majorID, $details->description);
                $stmt = $db->select("SELECT bucketID FROM MajorBucket Where majorID = ? and description = ?", $params);

                $bucketID = $stmt[0][0];
            } elseif ($details->getName() == "course") {
                $params = array($details->courseID, $details->courseName, $details->credits);
                $db->query("INSERT INTO CourseInfo (courseID, courseName, credits) VALUES (?, ?, ?)
                      ON DUPLICATE KEY UPDATE courseName=VALUES(courseName), credits=VALUES(credits)", $params);

                $this->log->toLog(0, __METHOD__, "course imported ID: $details->courseID, $details->courseName, credits: $details->credits");

                $params = array($details->courseID, $bucketID, $details->minGrade);
                $db->query("INSERT INTO MajorBucketRequiredCourses (courseInfoID, bucketID, minimumGrade) VALUES
                      ((SELECT courseInfoID FROM CourseInfo WHERE courseID = ?), ?, ?) ON DUPLICATE KEY UPDATE
                      courseInfoID = VALUES (courseInfoID), bucketID=VALUES(bucketID), minimumGrade=VALUES(minimumGrade)", $params);

                $this->log->toLog(0, __METHOD__, "bucketreq imported ID: $details->courseID, bucket:$bucketID, minGrade: $details->minGrade");
            } elseif ($details->getName() == 'bucket') {
                $this->importBucket($details, $bucketID, $majorID);
            }
        }
    }

    public function getGrad()
    {
        $this->log->toLog(0, __METHOD__, "STARTED!!");
        $db = new DatabaseConnector();

        $params = array();
        $programs = $db->select("Select * FROM GraduatePrograms", $params);

        $output = array();
        $ct = count($programs);

        $this->log->toLog(0, __METHOD__, "$ct");
        for ($i = 0; $i < count($programs); $i++) {
            array_push($output, array(
                "name" => $programs[$i][0],
                "GPA" => $programs[$i][1],
                "id" => $programs[$i][2]));
            $name = $programs[$i][0];
            $this->log->toLog(0, __METHOD__, "major: $name");
        }

        echo json_encode($output);
    }

    public function getCurr()
    {
        $db = new DatabaseConnector();

        $params = array();
        $programs = $db->select("Select * FROM Major", $params);

        $output = array();
        foreach ($programs as $program) {
            array_push($output, array(
                "name" => $program[0],
                "GPA" => $program[3],
                "id" => $program[1],
                "date" => $program[2]));
            $this->log->toLog(0, __METHOD__, "major: $program[0]");
        }
        echo json_encode($output);
    }

    public function getBuckets()
    {
        $db = new DatabaseConnector();

        $params = array();
        $buckets = $db->select("Select DISTINCT description FROM MajorBucket", $params);

        $output = array();
        foreach ($buckets as $bucket) {
            array_push($output, array(
                "name" => $bucket[0]));
            $this->log->toLog(0, __METHOD__, "bucket: $bucket[0]");
        }
        echo json_encode($output);
    }

    public function getCourses()
    {
        $db = new DatabaseConnector();

        $params = array();
        $courses = $db->select("SELECT * FROM CourseInfo", $params);

        $output = array();
        foreach ($courses as $course) {
            array_push($output, array(
                "name" => $course[1],
                "courseID" => $course[0],
                "id" => $course[3],
                "credits" => $course[2],
                "description" => $course[4]));
            $this->log->toLog(0, __METHOD__, "course: $course[1] ");
        }
        echo json_encode($output);
    }

    public function getStudents()
    {
        $db = new DatabaseConnector();

        $params = array();
        $students = $db->select("SELECT * FROM Users where type = 0", $params);

        $output = array();
        foreach ($students as $student) {
            array_push($output, array(
                "name" => "$student[3] $student[4]",
                "id" => $student[6],
                "username" => $student[1]));
            $this->log->toLog(0, __METHOD__, "student: $student[3] $student[4] ");
        }
        echo json_encode($output);
    }

    public function updateGrad($list)
    {
        $db = new DatabaseConnector();

        if ($list['id'] == -1) {
            $params = array($list['name'], $list['GPA']);
            $db->query("INSERT INTO GraduatePrograms (graduateProgram, requiredGPA) Values(?, ?)", $params);
            $this->log->toLog(1, __METHOD__, "Grad Program Added: Name: $params[0], reqGPA: $params[1]");
        }
        elseif ($list['del'] == 1) {
            $params = array($list['id']);
            $db->query("DELETE FROM GraduatePrograms WHERE graduateProgramID = ?", $params);
            $params = array($list['name'], $list['GPA']);
            $this->log->toLog(1, __METHOD__, "Grad Program Deleted: Name: $params[0], reqGPA: $params[1]");
        }
        else {
            $params = array($list['name'], $list['GPA'], $list['id']);
            $db->query("UPDATE GraduatePrograms SET graduateProgram = ?, requiredGPA = ?
              WHERE graduateProgramID = ?", $params);
            $this->log->toLog(1, __METHOD__, "Grad Program Updated: Name: $params[0], reqGPA: $params[1]");
        }

        echo json_encode("success");
    }

    public function updateCurr($list)
    {
        $db = new DatabaseConnector();

        if ($list['del'] == 1) {
            $params = array($list['id']);
            $db->query("DELETE FROM Major WHERE majorID = ?", $params);
            $params = array($list['name']);
            $this->log->toLog(1, __METHOD__, "Curriculum Program Deleted: Name: $params[0]");
        }
        else {
            $params = array($list['name'], $list['date'], $list['GPA'], $list['id']);
            $db->query("UPDATE Major SET majorName = ?, activeDate = ?, minGPA = ? WHERE majorID = ?", $params);
            $this->log->toLog(1, __METHOD__, "Grad Program Updated: Name: $params[0], reqGPA: $params[1]");
        }

        echo json_encode("success");
    }

    public function updateCourses($list)
    {
        $db = new DatabaseConnector();

        if ($list['id'] == -1) {
            $params = array($list['courseID'], $list['name'], $list['credits'], $list['description']);
            $db->query("INSERT INTO CourseInfo (courseID, courseName, credits, courseDescription)
              Values(?, ?, ?, ?)", $params);
            $this->log->toLog(1, __METHOD__, "Course Added: CourseID: $params[0], name: $params[1]");
        }
        elseif ($list['del'] == 1) {
            $params = array($list['id']);
            $db->query("DELETE FROM CourseInfo WHERE courseInfoID = ?", $params);
            $params = array($list['courseID'], $list['name']);;
            $this->log->toLog(1, __METHOD__, "Course Deleted: CourseID: $params[0], Name: $params[1]");
        }
        else {
            $params = array($list['courseID'], $list['name'], $list['credits'], $list['description'], $list['id']);
            $db->query("UPDATE CourseInfo SET courseID = ?, courseName = ?, credits = ?, courseDescription = ?
              WHERE courseInfoID = ?", $params);
            $this->log->toLog(1, __METHOD__, "Course Updated: CourseID: $params[0], Name: $params[1]");
        }

        echo json_encode("success");
    }

    public function getStudentCourses($list)
    {
        $db = new DatabaseConnector();

        $params = array($list['id']);
        $courses = $db->select("SELECT courseID, courseName, grade, studentCourseID FROM CourseInfo INNER JOIN
          StudentCourse on CourseInfo.courseInfoID = StudentCourse.courseInfoID WHERE userID = ?", $params);

        $output = array();
        foreach ($courses as $course) {
            array_push($output, array(
                "courseID" => $course[1],
                "courseName" => $course[0],
                "grade" => $course[2],
                "id" => $course[3]));
            $this->log->toLog(0, __METHOD__, "student course: courseID: $course[0] grade: $course[2]");
        }
        echo json_encode($output);
    }

    public function updateStudents($list)
    {
        $db = new DatabaseConnector();

        if ($list['del'] == 1) {
            $params = array($list['id']);
            $db->query("DELETE FROM StudentCourse WHERE studentCourseID = ?", $params);
        }
        else {
            $params = array($list['grade'], $list['id']);
            $db->query("UPDATE StudentCourse SET grade = ? WHERE studentCourseID = ?", $params);
        }

        echo json_encode("success");
    }

    public function resetPass($list)
    {
        $db = new DatabaseConnector();

        $userName = $list['username'];
        $password = $userName . "123!";
        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        $params = array($hash_password, $list['id']);
        $db->query("UPDATE Users set password = ? WHERE userID = ?", $params);

        $params = array($list['id']);
        $this->log->toLog(2, __METHOD__, "Password reset for user $params[0]");
    }

}