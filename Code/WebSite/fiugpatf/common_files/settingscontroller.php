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
      //$this->log = new ErrorLog();
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

         toLog(1, "Info", "settings.php/gpaImport", "GPA Audit Imported");
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
         toLog(0, "DEBUG", "SC/insertCourses", "Course: $courseID inserted for user: $this->user");
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
               toLog("0", "DEBUG", "SC/checkBucket", "Bucket: $bucket[0] Completed");
               break;
            }
         }

         if (!$bucketCompleted)
         {
            toLog("0", "DEBUG", "SC/checkBucket", "Bucket: $bucket[0] not completed");
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
               toLog(0, "DEBUG", "SC/update", "Updated course $courseID");
               continue;
            }

            continue;
         }

         $params = array($courseID);
         $out = $conn->select("SELECT courseInfoID FROM CourseInfo WHERE courseID = ?", $params);
         toLog(0, "DEBUG", "SC/update", "Insert course $courseID");

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
}
