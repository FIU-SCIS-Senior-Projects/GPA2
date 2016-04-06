<?php
include_once '../common_files/dbconnector.php';
class OverallDashboardController
{
    protected $userID;
    protected $username;
    protected $log;

    public function __construct($userID, $username)
    {
        $this->userID = $userID;
        $this->username = $username;
        $this->log = new ErrorLog();
    }

    public function getMajorBuckets()
    {
        $dbc = new DatabaseConnector();

        $params = array($this->userID);
        $buckets = $dbc->select("SELECT description, allRequired FROM MajorBucket WHERE majorID in (SELECT majorID
          FROM StudentMajor WHERE userID = ?) and parentID IS NULL", $params);

        $output = array();
        foreach ($buckets as $bucket)
        {
            $this->log->toLog(0, __METHOD__, "bucket: description: $bucket[0]
                required: $bucket[1] ");

            if($bucket[1] == '1')
                $allRequired = 'YES';
            else
                $allRequired = 'NO';

            array_push($output, array('+', $bucket[0], $allRequired));
        }

        echo json_encode($output);
        return $output;
    }

    public function getProgramInfo()
    {
        $gpa = $this->calculateGpa();

        $curr = $this->getCurrProgram();

        $programs  = $this->getGradPrograms();

        $output = array();
        array_push($output, $gpa, $curr, $this->username, $programs);

        echo json_encode($output);
        return $output;
    }

    private function calculateGpa()
    {
        $dbc = new DatabaseConnector();
        $gradePoints = 0;
        $creditHours = 0;

        $params = array($this->userID);
        $grades = $dbc->select("SELECT StudentCourse.grade, CourseInfo.credits FROM StudentCourse inner join CourseInfo
                on StudentCourse.courseInfoID = CourseInfo.courseInfoID  AND StudentCourse.grade Not in (Select grade
                from StudentCourse WHERE grade = 'ND' or grade = 'IP') AND StudentCourse.userID in (select userID From
                Users Where userID = ?)", $params);

        foreach ($grades as $grade)
        {
            $this->log->toLog(0, __METHOD__, "grades: grade: $grade[0] credits: $grade[1] ");

            switch($grade[0])
            {
                case 'A':
                    $gradePoints = $gradePoints + (4 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'A-':
                    $gradePoints = $gradePoints + (3.7 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'B+':
                    $gradePoints = $gradePoints + (3.3 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'B':
                    $gradePoints = $gradePoints + (3.0 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'B-':
                    $gradePoints = $gradePoints + (2.7 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'C+':
                    $gradePoints = $gradePoints + (2.3 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'C':
                    $gradePoints = $gradePoints + (2.0 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'C-':
                    $gradePoints = $gradePoints + (1.7 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'D+':
                    $gradePoints = $gradePoints + (1.3 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'D':
                    $gradePoints = $gradePoints + (1.0 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'D-':
                    $gradePoints = $gradePoints + (.7 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                case 'F':
                    $gradePoints = $gradePoints + (0 * $grade[1]);
                    $creditHours = $creditHours + (1 * $grade[1]);
                    break;
                default:
                    $gradePoints = $gradePoints + (0 * $grade[1]);
                    $creditHours = $creditHours + (0 * $grade[1]);
            }
        }

        if($creditHours == 0)
        {
            $this->log->toLog(0, __METHOD__, "gpa: null");
            return 'null';
        }

        $gpa = round($gradePoints / $creditHours, 2,PHP_ROUND_HALF_UP);
        $this->log->toLog(0, __METHOD__, "gpa: $gpa");
        return $gpa;
    }

    private function getCurrProgram()
    {
        $dbc = new DatabaseConnector();

        $params = array($this->userID);
        $currProgram = $dbc->select("SELECT majorName FROM Major Where majorID in (Select majorID From StudentMajor where userID = ?)", $params);

        if (count($currProgram) == 0)
        {
            $this->log->toLog(0, __METHOD__, "program: null");
            return 'null';
        }
        $x = $currProgram[0][0];
        $this->log->toLog(0, __METHOD__, "program: $x");
        return $currProgram[0][0];
    }

    private function getGradPrograms()
    {
        $dbc = new DatabaseConnector();

        $params = array();
        $gradPrograms = $dbc->select("SELECT graduateProgram, requiredGPA FROM GraduatePrograms", $params);

        foreach ($gradPrograms as $gradProgram)
        {
            $this->log->toLog(0, __METHOD__, "program: $gradProgram[0] gpa: $gradProgram[1]");
        }

        return $gradPrograms;
    }

    public function getGraphData() {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT StudentCourse.grade, CourseInfo.credits, StudentCourse.semester, StudentCourse.year FROM CourseInfo INNER JOIN StudentCourse ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE StudentCourse.userID = ? AND NOT StudentCourse.grade = 'IP' AND NOT StudentCourse.grade = 'ND' AND NOT StudentCourse.grade = 'DR' AND NOT StudentCourse.grade = 'TR' ORDER BY StudentCourse.year, CASE WHEN StudentCourse.semester LIKE 'Fall' THEN 1 WHEN StudentCourse.semester LIKE 'Spring' THEN 2 ELSE 3 END ";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0)
        {
            $this->log->toLog(3, __METHOD__, "No Data for graph");
            echo json_encode('No data for graph');
            return;
        }

        $averages = []; //this array will have each GPA for each semester
        $xaxis = []; //label for the x-axis
        $tmpSemester = $output[0][2]; //get the first Semester in the array
        $tmpYear = $output[0][3]; //get the first Year in the array

        $gradeValue = 0; //char of grade (i.e. A, A-, B+ ...)
        $totalGradePoints = 0; //total grade points achieved by student previous to current semester
        $allCourseCredits = 0; //total credits taken by student previous to current semester

        //for loop, go through entire array
        for($i = 0, $c = count($output); $i < $c; $i++) {
            $courseGrade = $output[$i][0];
            $courseCredits = $output[$i][1];
            $semester = $output[$i][2];
            $year = $output[$i][3];

            //store values into an array until semester or year are different
            //tempSemester, tempYear
            if($semester != $tmpSemester || $year != $tmpYear) {
                //calculate GPA
                $gpa = $totalGradePoints / $allCourseCredits; // GPA = total grade (quality) points / total credits
                array_push($averages, $gpa);

                //at the same time, store each different new semester and year as string in another array
                //$term - first two letters of Semester and last two numbers or year (i.e. Sum'16, Spr'16, Fall'16)
                if($tmpSemester == 'Fall') {
                    $term = "Fall'" . substr($tmpYear,2,2);
                }
                else if ($tmpSemester == 'Spring') {
                    $term = "Spr'" . substr($tmpYear,2,2);
                }
                else {
                    $term = "Sum'" . substr($tmpYear,2,2);
                }
                array_push($xaxis, $term);

                //updated tmpSemester and tmpYear
                $tmpSemester = $semester;
                $tmpYear = $year;
            }

            switch ($courseGrade) {
                case 'A':
                    $gradeValue = 4.00;
                    break;
                case 'A-':
                    $gradeValue = 3.67;
                    break;
                case 'B+':
                    $gradeValue = 3.33;
                    break;
                case 'B':
                    $gradeValue = 3.00;
                    break;
                case 'B-':
                    $gradeValue = 2.67;
                    break;
                case 'C+':
                    $gradeValue = 2.33;
                    break;
                case 'C':
                    $gradeValue = 2.00;
                    break;
                case 'C-':
                    $gradeValue = 1.67;
                    break;
                case 'D+':
                    $gradeValue = 1.33;
                    break;
                case 'D':
                    $gradeValue = 1.00;
                    break;
                case 'D-':
                    $gradeValue = 0.67;
                    break;
                case 'F':
                    $gradeValue = 0.00;
                    break;
                case 'F0*':
                    $gradeValue = 0.00;
                    break;
                case 'P':
                    $gradeValue = 3.00;
                    break;
                default:
                    $this->log->toLog(2, __METHOD__, "grade returned from query is not a char");
                    break;
            }

            $totalGradePoints += ($gradeValue * $courseCredits);
            $allCourseCredits += $courseCredits;

            if($i == $c - 1) { //if its the last time through the FOR loop .. get the average and store it in the array
                //calculate GPA
                $gpa = $totalGradePoints / $allCourseCredits; // GPA = total grade (quality) points / total credits
                array_push($averages, $gpa);

                //at the same time, store each different new semester and year as string in another array
                if($semester == 'Fall') {
                    $term = "Fall'" . substr($year,2,2);
                }
                else if ($semester == 'Spring') {
                    $term = "Spr'" . substr($year,2,2);
                }
                else {
                    $term = "Sum'" . substr($year,2,2);
                }
                array_push($xaxis, $term);
            }
        }

        for($i = 0, $c = count($xaxis); $i < $c; $i++) {
            array_push($return, array($xaxis[$i], $averages[$i]));
        }

        echo json_encode($return);
        return $return;
    }

    public function checkWeightAndRelevance() {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT CourseInfo.courseID, StudentCourse.weight, StudentCourse.relevance FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE userID = ? AND (grade = 'IP' OR grade = 'ND')";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0)
        {
            toLog(2, __METHOD__, "No courses exist for this user");
            echo json_encode([]);
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {

            if($output[$i][1] == "") {
                $this->log->toLog(3, __METHOD__, "weight is null");
            }
            if($output[$i][2] == "") {
                $this->log->toLog(3, __METHOD__, "relevance is null");
            }

            $courseID = $output[$i][0];
            $courseWeight = $output[$i][1];
            $courseRelevance = $output[$i][2];

            array_push($return, array($courseID, $courseWeight, $courseRelevance));
        }
        echo json_encode($return);
    }

    public function getGPA() {
        $gpa = $this->calculateGpa();

        $output = array($gpa);
        echo json_encode($output);
    }

    public function findChildBuckets() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['bucket'])) {
            $bucket = $_POST['bucket'];
        }
        else {
            $bucket = "";
            $this->log->toLog(3, __METHOD__, "bucket is null");
        }

        $params = array($this->userID, $bucket);
        $buckets = $dbc->select("SELECT description, allRequired, parentID FROM MajorBucket WHERE  majorID in
                (SELECT majorID FROM StudentMajor WHERE userID = ?) and parentID in (select bucketID
                FROM MajorBucket where description = ?)", $params);

        foreach ($buckets as $childBucket) {
            $this->log->toLog(0, __METHOD__, "description: $childBucket[0]
                allReq: $childBucket[1] parentID: $childBucket[2]");
        }

        if(count($buckets) > 0)
            $result = array('success' => true);
        else
            $result = array('success' => false);

        echo json_encode($result);
    }

    public function getMajorBucketsChildBuckets()
    {
        $dbc = new DatabaseConnector();

        if (isset($_POST['bucket'])) {
            $bucket = $_POST['bucket'];
        }
        else {
            $bucket = "";
            $this->log->toLog(3, __METHOD__, "bucket is null");
        }

        $params = array($this->userID, $bucket, $this->userID);
        $buckets = $dbc->select("SELECT description, allRequired FROM MajorBucket WHERE  majorID in (SELECT majorID
                    FROM StudentMajor WHERE userID = ?) and parentID in (Select bucketID From
                    MajorBucket Where description = ? AND majorID in (select majorID FROM StudentMajor
                    WHERE userID = ?))", $params);

        $output = array();
        foreach ($buckets as $childBucket) {
            $this->log->toLog(0, __METHOD__, "description: $childBucket[0]
                allReq: $childBucket[1]");

            if ($childBucket[1] == 1)
                $allR = "YES";
            else
                $allR = "NO";

            array_push($output, array('+', $childBucket[0], $allR));
        }

        echo json_encode($output);
    }

    public function getMajorBucketsCourseNeeded()
    {
        $dbc = new DatabaseConnector();

        if (isset($_POST['bucket'])) {
            $bucket = $_POST['bucket'];
        }
        else {
            $bucket = "";
            $this->log->toLog(3, __METHOD__, "bucket is null");
        }

        $params = array($bucket, $this->userID, $this->userID, $this->userID);
        $courses = $dbc->select("SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.weight,
          StudentCourse.relevance, StudentCourse.courseInfoID, StudentCourse.selected FROM StudentCourse INNER JOIN
          CourseInfo ON CourseInfo.courseInfoID in (Select courseInfoID From MajorBucketRequiredCourses where bucketID
          in (Select bucketID FROM MajorBucket where description = ? AND  majorID in (Select majorID From StudentMajor
          where userID = ?)))AND CourseInfo.courseInfoID in (SELECT courseInfoID From StudentCourse Where userID = ?
          AND grade = 'ND' )  AND StudentCourse.courseInfoID = CourseInfo.courseInfoID And StudentCourse.userID = ?",
          $params);

        $output = array();
        foreach ($courses as $course) {
            $this->log->toLog(0, __METHOD__, "courseID: $course[0],
            credits: $course[1], weight: $course[2], relevance: $course[3], courseInfoID: $course[4],
            selected: $course[5]");

            if ($course[5] == 1) {
                $box = '<input id = "' . $course[0] . 'check" type="checkbox" name="myCheckbox"
                    checked disabled="disabled" /> <a style ="color:blue;">toggle</a>';
                array_push($output, array($course[0], $course[1], $course[2], $course[3], $box));
            }
            else {
                array_push($output, array($course[0], $course[1], $course[2], $course[3], '<input id = "' . $course[0]
                    . 'check" type="checkbox" name="myCheckbox" /> <a  style ="color:blue;">toggle</a>'
                ));
            }
        }

        echo json_encode($output);
    }

    public function getMajorBucketsCourse() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['bucket'])) {
            $bucket = $_POST['bucket'];
        }
        else {
            $bucket = "";
            $this->log->toLog(3, __METHOD__, "bucket is null");
        }

        $params = array($bucket, $this->userID, $this->userID);
        $courses = $dbc->select("SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade
                    FROM CourseInfo INNER JOIN StudentCourse ON  CourseInfo.courseInfoID = StudentCourse.courseInfoID
                    AND StudentCourse.courseInfoID in (Select  courseInfoID From MajorBucketRequiredCourses where
                    bucketID in (Select bucketID FROM MajorBucket where description = ? AND majorID
                    in (Select majorID From StudentMajor where userID = ?)))AND userID = ? AND NOT StudentCourse.grade
                    in (SELECT grade FROM StudentCourse WHERE grade = 'ND')", $params);

        $output = array();

        foreach ($courses as $course) {

            $this->log->toLog(0, __METHOD__, "courseID: $course[0],
            credits: $course[1], grade: $course[2]");

            array_push($output, array($course[0], $course[1], $course[2]));
        }

        echo json_encode($output);
    }

    public function deleteCourseNeeded(){
        $dbc = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
            $this->log->toLog(3, __METHOD__, "course is null");
        }

        $params = array($courseID);
        $courseInfoID = $dbc->select("SELECT courseInfoID FROM CourseInfo WHERE  courseID = ?", $params);

        $params = array($this->userID, $courseInfoID[0]);

        $dbc->query("DELETE FROM StudentCourse WHERE userID = ? AND courseInfoID = ?", $params);

        $output = array('success' => true);
        echo json_encode($output);

    }

    public function addCourse() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
            $this->log->toLog(3, __METHOD__, "course is null");
        }

        $params = array($courseID);
        $courseInfoID = $dbc->select("SELECT courseInfoID FROM CourseInfo WHERE  courseID = ?", $params);

        $params = array($courseInfoID[0][0], $this->userID);
        $dbc->query("UPDATE StudentCourse SET grade ='IP' WHERE courseInfoID = ? AND userID = ?", $params);

        $output = array('success' => true);
        echo json_encode($output);
    }

    public function editStudent()
    {
        $dbc = new DatabaseConnector();

        $type = $_SESSION['type'];

        if ($type == 1){
            $params = array();

            $users = $dbc->select("SELECT userName, lastName, firstName, email FROM Users WHERE type = '0' ", $params);
            $output = array();
            foreach ($users as $user) {
                $this->log->toLog(3, __METHOD__, "userName: $user[0], lname: $user[1], fname: $user[2], email: $user[3]");
                array_push($output, array($user[0], $user[1], $user[2], $user[3]));
            }
            echo json_encode($output);
        }
        else {
            $list = array(
                'message' => "false",
                'why' => "You do not have permission to access this page."
            );
            $listArray = json_encode($list);
            echo $listArray;
        }
    }

    public function modCourse() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
            $this->log->toLog(3, __METHOD__, "course is null");
        }
        if (isset($_POST['modifiedGrade'])) {
            $modifiedGrade = $_POST['modifiedGrade'];
        } else {
            $modifiedGrade = "";
            $this->log->toLog(3, __METHOD__, "grade is null");
        }

        $params = array($modifiedGrade, $courseID, $this->userID);
        $dbc->query("UPDATE StudentCourse SET grade = ? WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo
          WHERE courseID =  ?) AND userID = ?", $params);

        $output = array('success' => true);
        echo json_encode($output);
    }

    public function modWeight() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
            $this->log->toLog(3, __METHOD__, "course is null");
        }
        if (isset($_POST['modifiedWeight'])) {
            $modifiedWeight = $_POST['modifiedWeight'];
        } else {
            $modifiedWeight = "";
            $this->log->toLog(3, __METHOD__, "weight is null");
        }
        if (isset($_POST['modifiedRelevance'])) {
            $modifiedRelevance = $_POST['modifiedRelevance'];
        } else {
            $modifiedRelevance = "";
            $this->log->toLog(3, __METHOD__, "relevance is null");
        }

        $params = array($courseID);
        $courseInfoID = $dbc->select("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = ?", $params);

        $params = array($modifiedWeight, $modifiedRelevance, $courseInfoID[0][0], $this->userID);
        $dbc->query("UPDATE StudentCourse SET weight = ?, relevance = ? WHERE courseInfoID = ? AND userID =?", $params);

        $result = array('success' => true);
        echo json_encode($result);
    }

    public function deleteItem() {
        $dbc = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
            $this->log->toLog(3, __METHOD__, "course is null");
        }

        $params = array($courseID, $this->userID);
        $dbc->query("UPDATE StudentCourse SET grade ='ND' WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo
          WHERE courseID =  ?) AND userID = ?", $params);

        $result = array('success' => true);
        echo json_encode($result);
    }
}