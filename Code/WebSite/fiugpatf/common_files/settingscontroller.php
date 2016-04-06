<?php
include_once'dbconnector.php';
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

      if(!file_exists($username))
      {
         mkdir($username, 0777);
         $loc = $_FILES['file']['tmp_name'];
         shell_exec('qpdf --password="" --decrypt ' . $loc . ' ' . $username . '/unencrypted.pdf');
         $courseInfo = shell_exec('python PDFWhatIfParser.py ' . $username . '/unencrypted.pdf');
         echo 'python PDFWhatIfParser.py ' . $username . '/unencrypted.pdf';

         $allData = explode("!!!!", $courseInfo);
         $majorData = explode("\n", $allData[0]);
         $courses = explode("\n", $allData[1]);
         $uccComplete = explode("\n", $allData[2]);

         if ($this->checkFirstTime())
         {
            $this->insertMajor($majorData);
            $this->insertCourses($courses);
            $this->instantiateNeeded($courses, $uccComplete[1]);
         }
         else
         {
            $this->update($courses);
         }

         shell_exec('rm -rf ' . $username);

         $this->log->toLog(1, __METHOD__ , "GPA Audit Imported");
      }
   }

   public function testStub($courses, $ucc)
   {
      if ($this->checkFirstTime())
      {
         $this->insertCourses($courses);
         $this->instantiateNeeded($courses, $ucc);
      }
      else
      {
         $this->update($courses);
      }
   }

   private function insertMajor($majorData)
   {
      $dbc = new DatabaseConnector();
      foreach ($majorData as $maj) {
         if($maj != "")
         {
            $stmt = "INSERT INTO StudentMajor (userID, majorID) VALUES (?, (SELECT majorID from Major WHERE majorName = ?))";
            $params = array($this->user, $maj);
            $dbc->query($stmt, $params);
         }
      }
   }

   private function insertCourses($courses)
   {
      $conn = new DatabaseConnector();

      foreach ($courses as $course)
      {
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
         if (count($courseInfoID) == 0)
         {
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
      foreach ($courses as $course)
      {
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

      foreach ($buckets as $bucket)
      {
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

      if (count($childBuckets) > 0)
      {
         foreach($childBuckets as $childBucket)
            $this->checkBucket($takenCourses, $childBucket, $this->user);
      }
      else
      {
         $bucketCourses = $conn->select("SELECT CourseInfo.courseID, CourseInfo.credits, CourseInfo.courseInfoID,
            MajorBucketRequiredCourses.minimumGrade FROM CourseInfo INNER JOIN MajorBucketRequiredCourses on
            CourseInfo.courseInfoID = MajorBucketRequiredCourses.courseInfoID
            WHERE MajorBucketRequiredCourses.bucketID = ?", $params);

         $counter = 0;
         $coursesNotTaken = array();
         $bucketCompleted = false;

         foreach ($bucketCourses as $bucketCourse)
         {
            $passed = false;

            $keys = $this->search($takenCourses, '0', $bucketCourse[0]);

            foreach ($keys as $key)
            {
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

            if ($counter >= $bucket[2])
            {
               $bucketCompleted = true;
               $this->log->toLog("0", __METHOD__, "Bucket: $bucket[0] Completed");
               break;
            }
         }

         if (!$bucketCompleted)
         {
            $this->log->toLog("0", __METHOD__, "Bucket: $bucket[0] not completed");
            foreach ($coursesNotTaken as $courseNotTaken)
            {
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
      switch($grade)
      {
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

      foreach($courses as $course)
      {
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

         if (count($out) > 0)
         {
            $params = array($grade, $out[0][3]);
            $conn->query("UPDATE StudentCourse SET grade = ? WHERE studentCourseID = ?", $params);

            $params = array($courseID);
            $minGrade = $conn->select("SELECT minimumGrade FROM MajorBucketRequiredCourses
              WHERE courseInfoID = (SELECT courseInfoID FROM CourseInfo Where courseID = ?)", $params);

            if ($this->convertGrade($grade) < $this->convertGrade($minGrade[0]))
            {
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

         if (count($out) == 0)
         {
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
      foreach ($adminData->children() as $details)
      {
         if ($details->getName() == 'programName') {
            $params = array($details);
            $db->query("INSERT INTO Major (majorName, majorID) VALUES (?, NULL)", $params);

            $stmt = $db->select("SELECT majorID FROM Major WHERE majorName = ?", $params);
            $majorID = $stmt[0][0];
            $this->log->toLog(0, __METHOD__, "programName: $details");
         }
         elseif ($details->getName() == "minGPA")
            $this->log->toLog(0, __METHOD__, "minGPA: $details->minGPA");
         elseif ($details->getName() == 'bucket') {
            $this->importBucket($details, null, $majorID);
         }
      }
      /*$params = array($adminData->programName);
      $db->query("INSERT INTO Major (majorName, majorID) VALUES (?, NULL)", $params);

      $stmt = $db->select("SELECT majorID FROM Major WHERE majorName = ?", $params);
      $majorID = $stmt[0][0];*/

      /*foreach ($adminData->children() as $table_data) {
         $this->log->toLog(0, __METHOD__, "made it this far");
         foreach ($table_data->children() as $rows) {
            if ($table_data['name'] == 'MajorBucket') {
               if ($rows->field[7] == "null") {
                  $majorID = $rows->field[0];

                  $params = array($rows->field[0], $rows->field[1], $rows->field[2], $rows->field[3],
                      $rows->field[4], $rows->field[5], $rows->field[6]);
                  $db->query("INSERT INTO MajorBucket (majorID, dateStart, dateEnd, description, allRequired,
                          quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, ?, ?, null) ON DUPLICATE
                          KEY UPDATE dateStart=VALUES(dateStart), dateEnd=VALUES(dateEnd), allRequired = VALUES
                          (allRequired), quantification=VALUES(quantification), parentID=VALUES(parentID)", $params);

                  $this->log->toLog(0, __METHOD__, "Major Bucket imported: $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6]");
               }
               else {
                  $params = array($rows->field[0], $rows->field[7]);
                  $stmt = $db->select("SELECT bucketID FROM MajorBucket WHERE majorID = ? and
                          description = ?", $params);

                  $parentID = $stmt[0][0];

                  $params = array($rows->field[0], $rows->field[1], $rows->field[2], $rows->field[3],
                      $rows->field[4], $rows->field[5], $rows->field[6], $parentID);

                  $db->query("INSERT INTO MajorBucket (majorID, dateStart, dateEnd, description, allRequired,
                        quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY
                        UPDATE dateStart=VALUES(dateStart), dateEnd=VALUES(dateEnd), allRequired=VALUES(allRequired),
                        quantification=VALUES(quantification), parentID=VALUES(parentID)", $params);

                  $this->log->toLog(0, __METHOD__, "Major Bucket imported: $params[0], $params[1], $params[2], $params[3], $params[4], $params[5], $params[6], $parentID");
               }
            }
            else if ($table_data['name'] == 'CourseInfo') {
               $params = array($rows->field[0], $rows->field[1], $rows->field[2]);
               $db->query("INSERT INTO CourseInfo (courseID, courseName, credits) VALUES (?, ?, ?)
                      ON DUPLICATE KEY UPDATE courseName=VALUES(courseName), credits=VALUES(credits)", $params);

               $this->log->toLog(0, __METHOD__, "Course Imported: $params[0], $params[1], $params[2],");
            }
            else if ($table_data['name'] == 'MajorBucketRequiredCourses') {
               $params = array($rows->field[0], $majorID, $rows->field[1], $rows->field[2]);
               $db->query("INSERT INTO MajorBucketRequiredCourses (courseInfoID, bucketID, minimumGrade) VALUES
                      ((SELECT courseInfoID FROM CourseInfo WHERE courseID = ?), (SELECT bucketID FROM MajorBucket
                      WHERE majorID = ? and description = ?), ?) ON DUPLICATE KEY UPDATE courseInfoID = VALUES
                      (courseInfoID), bucketID=VALUES(bucketID), minimumGrade=VALUES(minimumGrade)", $params);

               $this->log->toLog(0, __METHOD__, "Major Bucket imported: $params[0], $params[1], $params[2]");
            }
         }
      }*/

      $response_array[0] = 'success';
      echo json_encode($response_array);
   }

   public function importBucket($bucket, $parentID, $majorID){
      $count = $bucket->count();
      $allRequired = 0;
      $db = new DatabaseConnector();

      if ($bucket->course[0] != null)
         $quantification = 'courses';
      else
         $quantification = 'buckets';

      foreach ($bucket->children() as $details)
      {
         if ($details->getName() == 'data') {
            if ($count-1 == $quantification)
               $allRequired = 1;

            if ($parentID == null) {
               $params = array($majorID, $details->dateStart, $details->dateEnd, $details->description, $allRequired,
                   $details->quantity, $quantification);
               $db->query("INSERT INTO MajorBucket (majorID, dateStart, dateEnd, description, allRequired,
                          quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, ?, ?, null) ON DUPLICATE
                          KEY UPDATE dateStart=VALUES(dateStart), dateEnd=VALUES(dateEnd), allRequired = VALUES
                          (allRequired), quantification=VALUES(quantification), parentID=VALUES(parentID)", $params);
               $this->log->toLog(0, __METHOD__, "bucket imported majorID: $majorID, start:$details->dateStart, end:$details->dateEnd, description: $details->description, req:$allRequired, quantity:$details->quantity, quantification$quantification");
            }
            else {
               $params = array($majorID, $details->dateStart, $details->dateEnd, $details->description, $allRequired,
                   $details->quantity, $quantification, $parentID);
               $db->query("INSERT INTO MajorBucket (majorID, dateStart, dateEnd, description, allRequired,
                        quantityNeeded, quantification, parentID) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY
                        UPDATE dateStart=VALUES(dateStart), dateEnd=VALUES(dateEnd), allRequired=VALUES(allRequired),
                        quantification=VALUES(quantification), parentID=VALUES(parentID)", $params);
               $this->log->toLog(0, __METHOD__, "bucket imported - majorID: $majorID, start:$details->dateStart, end:$details->dateEnd, description: $details->description, req:$allRequired, quantity:$details->quantity, quantification$quantification, parent: $parentID");
            }

            $params = array($majorID, $details->description);
            $stmt = $db->select("SELECT bucketID FROM MajorBucket Where majorID = ? and description = ?", $params);

            $bucketID = $stmt[0][0];
         }
         elseif ($details->getName() == "course"){
            $params = array($details->courseID, $details->courseName, $details->credits);
            $db->query("INSERT INTO CourseInfo (courseID, courseName, credits) VALUES (?, ?, ?)
                      ON DUPLICATE KEY UPDATE courseName=VALUES(courseName), credits=VALUES(credits)", $params);

            $this->log->toLog(0, __METHOD__, "course imported ID: $details->courseID, $details->courseName, credits: $details->credits");

            $params = array($details->courseID, $bucketID, $details->minGrade);
            $db->query("INSERT INTO MajorBucketRequiredCourses (courseInfoID, bucketID, minimumGrade) VALUES
                      (?, ?, ?) ON DUPLICATE KEY UPDATE courseInfoID = VALUES
                      (courseInfoID), bucketID=VALUES(bucketID), minimumGrade=VALUES(minimumGrade)", $params);

            $this->log->toLog(0, __METHOD__, "bucketreq imported ID: $details->courseID, bucket:$bucketID, minGrade: $details->minGrade");
         }
         elseif ($details->getName() == 'bucket') {
            $this->importBucket($details, $bucketID, $majorID);
         }
      }
   }
}
