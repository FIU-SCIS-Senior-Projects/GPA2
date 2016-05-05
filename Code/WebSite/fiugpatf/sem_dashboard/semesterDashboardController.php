<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/10/16
 * Time: 7:09 PM
 */

class SemesterDashboardController
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

    function currentAssessments() {
        $db = new DatabaseConnector();
        $user = $this->userID;
        $return = [];

        $stmt = "SELECT CourseInfo.courseID, CourseInfo.courseName, CourseInfo.credits FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE grade = 'IP' AND userID = ? ";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        for($i = 0; $i < count($output); $i++) {

            $courseID = $output[$i][0];
            $courseName = $output[$i][1];
            $credit = $output[$i][2];

            $this->log->toLog(0, __METHOD__, "ID: $courseID, Course Name: $courseName, Credits: $credit");

            $grade = $this->getGrade($user, $courseID);
            if($grade != 'No Grades') {
                array_push($return, array("id"=>$courseID, "name"=>$courseName, "credits"=>$credit, "grade"=>round($grade, 2)));
            }
            else {
                array_push($return, array("id"=>$courseID, "name"=>$courseName, "credits"=>$credit, "grade"=>$grade));
            }
        }

        echo json_encode($return);
        return $return;

    }

    function getGrade($user, $course) {
        $dbc = new DatabaseConnector();

        $stmt = "SELECT assessmentName, percentage FROM AssessmentType WHERE studentCourseID in (SELECT studentCourseID
        FROM StudentCourse WHERE grade = 'IP' AND userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($user, $course);
        $output = $dbc->select($stmt, $params);

        $average = 0;
        $totalPer = 0;
        for($i = 0; $i < count($output); $i++) {
            $assessmentName = $output[$i][0];
            $per = $output[$i][1];

            $this->log->toLog(0, __METHOD__, "Assessment Name: $assessmentName, Percent: $per%");

            $grade = $this->averageAssess($assessmentName, $user, $course);
            if($grade != " ") {
                $average += $grade * $per;
                $totalPer += $per;
            }
        }

        if($totalPer == 0) {
            return "No Grades";
        }
        else {
            return $average/$totalPer;
        }
    }

    function averageAssess($category, $user, $course) {

        $dbc = new DatabaseConnector();
        $stmt = "SELECT grade FROM Assessment WHERE  assessmentTypeID in (select assessmentTypeID FROM AssessmentType WHERE AssessmentName = ?) AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($category, $user, $course);
        $output = $dbc->select($stmt, $params);

        $runAvg = 0;
        $count = 0;
        for($i = 0; $i < count($output); $i++) {
            $aGrade = $output[$i][0];
            $runAvg += $aGrade;
            $count++;

            $this->log->toLog(0, __METHOD__, "Assessment Name: $category, Grade: $aGrade");
        }

        if($count != 0) {
            $this->log->toLog(1, __METHOD__, "Value returned");
            return round($runAvg / $count, 2);
        }
        else {
            $this->log->toLog(1, __METHOD__, "No value returned");
            return " ";
        }
    }

    function courseLegend() {
        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT CourseInfo.courseID, CourseInfo.courseInfoID FROM Assessment, StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE Assessment.studentCourseID = StudentCourse.studentCourseID AND StudentCourse.grade = 'IP' AND userID = ? ORDER BY dateEntered";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        for($i = 0, $c = count($output); $i < $c; $i++) {
            $new =  $output[$i][0];
            if (!isset($arr[$new])) {
                $arr[$new] = 1;
                $this->log->toLog(0, __METHOD__, "Course ID: $new");
                array_push($return, $new);
            }
        }

        echo json_encode($return);
        return $return;
    }

    function getGraphData() {
        $db = new DatabaseConnector();

        $stmt = "SELECT b.assessmentTypeID, b.percentage, a.grade, a.dateEntered, a.studentCourseID FROM Assessment as a,
          AssessmentType as b WHERE  a.studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ?)
          AND b.assessmentTypeID = a.assessmentTypeID ORDER BY dateEntered, grade";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        $allAssessments = []; //array that will hold all of the assessment info fetched from DB
        //$allAssessments[i][0] -> $ID or assessmentTypeID
        //$allAssessments[i][1] -> $per or percent
        //$allAssessments[i][2] -> $grade
        //$allAssessments[i][3] -> $date or dateEntered
        //$allAssessments[i][4] -> $course or studentCourseID
        $trackCourse = [];

        for($i = 0, $c = count($output); $i < $c; $i++) {
            $ID = $output[$i][0];
            $per = $output[$i][1];
            $grade = $output[$i][2];;
            $date = $output[$i][3];;
            $course = $output[$i][4];;

            $this->log->toLog(0, __METHOD__, "Assessment ID: $ID, Percent: $per, Grade: $grade, Date Entered: $date, Course ID: $course");

            array_push($allAssessments, array($ID, $per, $grade, $date, $course));
            if (!isset($arr[$course])) {
                $arr[$course] = 1;
                array_push($trackCourse, $course);
            }
        }


        $year = substr($allAssessments[0][3], 0, 4);
        $semester = $this->term(substr($allAssessments[0][3], 5, 2)); //fall, spring or summer
        $currTimePeriod = $this->timePeriod($semester, $allAssessments[0][3]); //temp time period
        $timePeriodSize = $this->checkSize($semester); //check how many segments for x-axis

        $this->log->toLog(0, __METHOD__, "Year: $year, Semester: $semester, Time Period: $currTimePeriod, TP Size: $timePeriodSize");

        $tempArray = [];
        $arrayCourse = [];

        for ($i = 0, $c = count($allAssessments); $i < $c; $i++) {
            $assessmentTimePeriod = $this->timePeriod($semester, $allAssessments[$i][3]);
            $lastAssessment = count($allAssessments) - 1;

            if ($assessmentTimePeriod == $currTimePeriod) {
                //$arrayCourse stores array of courseID, assessmentTypeID, percent, grade
                array_push($arrayCourse, array($allAssessments[$i][4], $allAssessments[$i][0], $allAssessments[$i][1], $allAssessments[$i][2]));

                if ($i == $lastAssessment) {
                    //gradesReturned gets the average for the grades thus far for each course
                    //returns array = [[course1, grade], [course2, grade], [course3, grade]
                    $gradesReturned = $this->gradeUpTo($arrayCourse);

                    for ($j = 0, $d = count($trackCourse); $j < $d; $j++) { // traverse each gradeReturned and add to tempArray
                        $currTrackCourse = $trackCourse[$j];

                        foreach ($gradesReturned as list($cx, $gx)) {
                            if ($currTrackCourse == $cx) {
                                // tempArray = [[time period, course, grade], [time period, course, grade]]
                                array_push($tempArray, array($currTimePeriod, $cx, $gx));
                                break;
                            }
                        }
                    }
                    $currTimePeriod = $assessmentTimePeriod; //update to new time period
                }
            } else {
                //gradesReturned gets the average for the grades thus far for each course
                //returns array [[course1, grade], [course2, grade], [course3, grade]]
                $gradesReturned = $this->gradeUpTo($arrayCourse);


                for ($j = 0, $d = count($trackCourse); $j < $d; $j++) { // traverse each gradeReturned and add to tempArray
                    $currTrackCourse = $trackCourse[$j];

                    foreach ($gradesReturned as list($cx, $gx)) {

                        if ($currTrackCourse == $cx) {
                            // tempArray = [[time period, course, grade], [time period, course, grade]]
                            array_push($tempArray, array($currTimePeriod, $cx, $gx));
                            break;
                        }
                    }
                }
                $currTimePeriod = $assessmentTimePeriod; //update to new time period
            }
        }

        $allPoints = [];
        $label = [];
        $return = [];
        for ($q = 0, $c = count($trackCourse); $q < $c; $q++) { // go through every ID
            $plots = [];
            $currTrackCourse = $trackCourse[$q];
            $found = false;
            $y = 0;
            $currAverage = 100;

            //while - y <= checkSize()
            while ($y <= $timePeriodSize) {
                foreach ($tempArray as list($tp, $ci, $ag)) { //tp - time period, ci - course id, ag - average grade
                    if ($currTrackCourse == $ci && $y == $tp) {
                        if (!isset($arr[$y])) {
                            $arr[$y] = 1;
                            array_push($label, $this->dateOfTerm($semester, $tp, $year));
                        }
                        array_push($plots, $ag);
                        $currAverage = $ag;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $this->log->toLog(0, __METHOD__, "Current Average: $currAverage");
                    $y++;
                } else {
                    $this->log->toLog(0, __METHOD__, "Current Average: $currAverage");
                    if (!isset($arr[$y])) {
                        $arr[$y] = 1;
                        array_push($label, $this->dateOfTerm($semester, $y, $year));
                    }
                    array_push($plots, $currAverage);
                    $y++;
                }
            }
            //$allPlots for all the courses
            array_push($allPoints, $plots);

        }

        //array[0] - labels, the rest are the data for the remaining courses
        array_push($return, $label, $allPoints);

        echo json_encode($return);
        return $return;
    }

    function dateOfTerm($term, $timePeriod, $year) {

        if($term == 'fall') {
            $fallStart = date('Y-m-d', strtotime('third monday of august' . $year));

            if($timePeriod == 0) {
                return date('m-d', strtotime('third monday of august' . $year));;
                /*return strtotime('third monday of august' . $year) * 1000;*/
            }
            else if($timePeriod == 1) {
                return date('m-d', strtotime($fallStart . '+ 3 weeks'));
                /*return strtotime($fallStart . '+ 3 weeks') * 1000;*/
            }
            else if($timePeriod == 2) {
                return date('m-d', strtotime($fallStart . '+ 6 weeks'));
                /*return strtotime($fallStart . '+ 6 weeks') * 1000;*/
            }
            else if($timePeriod == 3) {
                return date('m-d', strtotime($fallStart . '+ 9 weeks'));
                /*return strtotime($fallStart . '+ 9 weeks') * 1000;*/
            }
            else if($timePeriod == 4) {
                return date('m-d', strtotime($fallStart . '+ 12 weeks'));
                /*return strtotime($fallStart . '+ 12 weeks') * 1000;*/
            }
            else if($timePeriod == 5) {
                return date('m-d', strtotime($fallStart . '+ 15 weeks'));
                /*return strtotime($fallStart . '+ 15 weeks') * 1000;*/
            }
            else if($timePeriod == 6) {
                return date('m-d', strtotime($fallStart . '+ 18 weeks'));
                /*return strtotime($fallStart . '+ 18 weeks') * 1000;*/
            }
            else {
                return 'summer';
            }
        }
        else if($term == 'spring') {
            $springStart = date('Y-m-d', strtotime('second monday of january' . $year));

            if($timePeriod == 0) {
                return date('m-d', strtotime('second monday of january' . $year));;
                /*return strtotime('second monday of january' . $year) * 1000;*/
            }
            else if($timePeriod == 1) {
                return date('m-d', strtotime($springStart . '+ 3 weeks'));
                /*return strtotime($springStart . '+ 3 weeks') * 1000;*/
            }
            else if($timePeriod == 2) {
                return date('m-d', strtotime($springStart . '+ 6 weeks'));
                /*return strtotime($springStart . '+ 6 weeks') * 1000;*/
            }
            else if($timePeriod == 3) {
                return date('m-d', strtotime($springStart . '+ 9 weeks'));
                /*return strtotime($springStart . '+ 9 weeks') * 1000;*/
            }
            else if($timePeriod == 4) {
                return date('m-d', strtotime($springStart . '+ 12 weeks'));
                /*return strtotime($springStart . '+ 12 weeks') * 1000;*/
            }
            else if($timePeriod == 5) {
                return date('m-d', strtotime($springStart . '+ 15 weeks'));
                /*return strtotime($springStart . '+ 15 weeks') * 1000;*/
            }
            else if($timePeriod == 6) {
                return date('m-d', strtotime($springStart . '+ 18 weeks'));
                /*return strtotime($springStart . '+ 18 weeks') * 1000;*/
            }
            else {
                return 'summer';
            }
        }
        else {
            return '';
        }
    }

    function term($month) {
        if($month == '06' || $month == '07') {
            return 'summer';
        }
        else if($month == '08' || $month == '09' || $month == '10' || $month == '11' || $month == '12') {
            return 'fall';
        }
        else if($month == '01' || $month == '02' || $month == '03' || $month == '04' || $month == '05') {
            return 'spring';
        }
        else {
            return '';
        }
    }

    function timePeriod($t, $d) {
        $year = substr($d, 0, 4);

        if($t == 'fall') {
            $fallStart = date('Y-m-d', strtotime('third monday of august' . $year));
            $tp6 = date('Y-m-d', strtotime($fallStart . '+ 18 weeks'));
            $tp5 = date('Y-m-d', strtotime($fallStart . '+ 15 weeks'));
            $tp4 = date('Y-m-d', strtotime($fallStart . '+ 12 weeks'));
            $tp3 = date('Y-m-d', strtotime($fallStart . '+ 9 weeks'));
            $tp2 = date('Y-m-d', strtotime($fallStart . '+ 6 weeks'));
            $tp1 = date('Y-m-d', strtotime($fallStart . '+ 3 weeks'));

            if(($d >= $fallStart) && ($d <= $tp1)) {
                return 1;
            }
            else if(($d > $tp1) && ($d <= $tp2)) {
                return 2;
            }
            else if(($d > $tp2) && ($d <= $tp3)) {
                return 3;
            }
            else if(($d > $tp3) && ($d <= $tp4)) {
                return 4;
            }
            else if(($d > $tp4) && ($d <= $tp5)) {
                return 5;
            }
            else if(($d > $tp5) && ($d < $tp6)) {
                return 6;
            }
            else {
                return 'summer';
            }
        }
        else if($t == 'spring') {
            $springStart = date('Y-m-d', strtotime('second monday of january' . $year));
            $tp6 = date('Y-m-d', strtotime($springStart . '+ 18 weeks'));
            $tp5 = date('Y-m-d', strtotime($springStart . '+ 15 weeks'));
            $tp4 = date('Y-m-d', strtotime($springStart . '+ 12 weeks'));
            $tp3 = date('Y-m-d', strtotime($springStart . '+ 9 weeks'));
            $tp2 = date('Y-m-d', strtotime($springStart . '+ 6 weeks'));
            $tp1 = date('Y-m-d', strtotime($springStart . '+ 3 weeks'));

            if(($d >= $springStart) && ($d <= $tp1)) {
                return 1;
            }
            else if(($d > $tp1) && ($d <= $tp2)) {
                return 2;
            }
            else if(($d > $tp2) && ($d <= $tp3)) {
                return 3;
            }
            else if(($d > $tp3) && ($d <= $tp4)) {
                return 4;
            }
            else if(($d > $tp4) && ($d <= $tp5)) {
                return 5;
            }
            else if(($d > $tp5) && ($d < $tp6)) {
                return 6;
            }
            else {
                return 'summer';
            }
        }
        else {
            return '';
        }

    }

    function checkSize($term) {

        if($term == 'fall') {
            return 6;
        }
        else if($term == 'spring') {
            return 6;
        }
        else if($term == 'summera') {
            return 4;
        }
        else if($term == 'summerb') {
            return 4;
        }
        else { //summer C
            return 6;
        }

    }

    function gradeUpTo($arrayCourse){
        //$arrayCourse stores array of courseID, assessmentTypeID, percent, grade

        $listCourse = [];
        $gradeEachCourse = [];

        foreach($arrayCourse as list($course, $ID, $percent, $grade)) {
            if(!isset($arr[$course])) {
                $arr[$course] = 1; //arr[$ID] is set
                array_push($listCourse, $course);
            }
        }

        for($i = 0, $c = count($listCourse); $i < $c; $i++) { // go through each Course

            $currCourse = $listCourse[$i];
            $collectAssessments = [];

            foreach($arrayCourse as list($co, $a, $p, $g)) {
                if($currCourse == $co) { //look for same course
                    array_push($collectAssessments, array($a, $p, $g)); // store ID, percent, grade
                }
            }
            $average = $this->findAvg($collectAssessments); // find the current average for course

            //echo "Current Course: $currCourse - Average Grade: $average\n";

            array_push($gradeEachCourse, array($currCourse, $average));
        }

        return $gradeEachCourse;
    }

    function findAvg($arrCourse) {

        $listID = [];
        $calculateScore = 0;
        $weightUsed = 0;

        foreach($arrCourse as list($ID, $percent, $grade)) { // collect unique IDs

            if(!isset($arr[$ID])) {
                $arr[$ID] = 1; //arr[$ID] is set
                array_push($listID, $ID);
            }

        }

        for($i = 0, $c = count($listID); $i < $c; $i++) { // go through each ID

            //echo "Assessment Type ID: $listID[$i] \n";

            $currID = $listID[$i];
            $gradeTotal = 0;
            $currPercent = 0;
            $x = 0;

            foreach($arrCourse as list($a, $p, $g)) {
                if($currID == $a) { //look fot same IDs
                    $gradeTotal += $g; // add grade to total
                    $currPercent = $p; //percentage for AssessmentTypeID
                    $x++; // track how many grades with the same AssessmentTypeID
                }
            }

            //echo "Assessment Type ID: $listID[$i] - Grade Total: $gradeTotal - Percent: $currPercent - How Many: $x\n";

            $calculateScore += (($gradeTotal / $x) * ($currPercent/100));
            $weightUsed += $currPercent;

            //echo "$calculateScore - $weightUsed\n";
        }

        $finalScore = ($calculateScore / $weightUsed) * 100;
        //echo "$finalScore\n";
        return $finalScore;
    }

    function getGradProgram() {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT graduateProgram, requiredGPA FROM GraduatePrograms";
        $params = array();
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            $this->log->toLog(2, __METHOD__, "No graduate programs returned");
            echo json_encode([]);
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $prg = $output[$i][0];
            $gpa = $output[$i][1];

            $this->log->toLog(0, __METHOD__, "Graduate Program: $prg, Required GPA: $gpa");
            array_push($return, array($prg,$gpa));
        }

        echo json_encode($return);
        return $return;
    }

    function getCurrentProgram() {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT majorName FROM Major WHERE majorID IN (SELECT majorID FROM StudentMajor WHERE userID = ?)";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            $this->log->toLog(2, __METHOD__, "No major program selected");
            echo json_encode([]);
            return $return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $currentProgram = $output[$i][0];

            $this->log->toLog(0, __METHOD__, "Current Program: $currentProgram");
            array_push($return, array($currentProgram));
        }

        echo json_encode($return);
        return $return;
    }

    function remove($id) {

        $db = new DatabaseConnector();

        $stmt = "Delete FROM StudentCourse WHERE userID = ? AND grade = 'IP' AND courseInfoID IN (SELECT C.courseInfoID FROM CourseInfo C WHERE courseID = ?)";
        $params = array($this->userID, $id);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "Course has be removed");
        $return = "true";

        echo json_encode($return);
        return $return;
    }

    function tabs($course) {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT assessmentName FROM AssessmentType WHERE studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' AND userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($this->userID, $course);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            $this->log->toLog(2, __METHOD__, "No assessments for course");
            echo json_encode([]);
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $assessments = $output[$i][0];

            $this->log->toLog(0, __METHOD__, "Tab Assessment Name: $assessments");
            array_push($return, $assessments);
        }

        echo json_encode($return);
        return $return;
    }

    function getAllAssessments($course) {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT assessmentName, percentage FROM AssessmentType WHERE  studentCourseID IN (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' AND userID = ? AND courseInfoID IN (SELECT courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($this->userID, $course);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            $this->log->toLog(2, __METHOD__, "No assessments or percentages returned");
            echo json_encode([]);
            return $return;
        }

        $average = 0;
        $totalPer = 0;

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $bucket = $output[$i][0];
            $per = $output[$i][1];

            $grade = $this->avgAssess($bucket, $course);
            if($grade != "No Grades") {
                $this->log->toLog(0, __METHOD__, "Course has $grade");
                array_push($return, array("assessment"=>$bucket, "percent"=>$per, "grade"=>round($grade, 2)));
                $average += $grade * $per;
                $totalPer += $per;
            }
            else {
                $this->log->toLog(0, __METHOD__, "Course $course has $grade");
                array_push($return, array("assessment"=>$bucket, "percent"=>$per, "grade"=>$grade));
            }
        }

        if($totalPer == 0) {
            array_push($return, array("assessment"=>"Total","percent"=>"" , "grade"=>"No Grades"));
        }
        else {
            array_push($return, array("assessment"=>"Total", "percent"=>"", "grade"=>round($average/$totalPer, 2)));
        }

        echo json_encode($return);
        return $return;
    }

    function avgAssess($category, $course) {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT grade FROM Assessment WHERE assessmentTypeID in (select assessmentTypeID FROM AssessmentType WHERE assessmentName = ?) AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($category, $this->userID, $course);
        $output = $db->select($stmt, $params);

        $runAvg = 0;
        $count = 0;

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $assessmentGrade = $output[$i][0];
            $runAvg += $assessmentGrade;
            $count++;

            array_push($return, array($assessmentGrade));
        }

        if($count != 0) {
            return $runAvg / $count;
        }
        else {
            $this->log->toLog(1, __METHOD__, "No grades returned for $category");
            return "No Grades";
        }
    }

    function plotPoints($course) {

        $conn = new DatabaseConnector();
        $params = array($this->userID, $course);
        $output = $conn->select("SELECT b.assessmentTypeID, b.percentage, a.grade, a.dateEntered FROM Assessment as a, AssessmentType as b WHERE a.studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? and courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?)) AND b.assessmentTypeID = a.assessmentTypeID ORDER BY dateEntered", $params);

        $x = 1;
        $dates = [];
        $points = [];
        $runningGrades = [];
        $plot = [];
        $currDate = "Empty";

        if (count($output) > 0) {
            foreach ($output as $assesment) {
                $this->log->toLog(0, __METHOD__, "Assessment Type ID: $assesment[0] Percentage: $assesment[1] Grade: $assesment[2] Date Entered: $assesment[3]");
                if ($currDate == "Empty") {
                    $currDate = $assesment[3];
                    array_push($dates, substr($assesment[3], 5));
                    array_push($runningGrades, array($assesment[0], $assesment[1], $assesment[2]));
                }
                else if ($currDate == $assesment[3]) {
                    array_push($runningGrades, array($assesment[0], $assesment[1], $assesment[2]));
                }
                else {
                    array_push($points, $this->gradeUp($runningGrades));
                    array_push($runningGrades, array($assesment[0], $assesment[1], $assesment[2]));
                    $x++;
                    $currDate = $assesment[3];
                    array_push($dates, substr($assesment[3], 5));
                }
            }

            array_push($runningGrades, array($assesment[0], $assesment[1], $assesment[2]));
            array_push($points, $this->gradeUp($runningGrades));
            array_push($plot, $dates, $points);

            echo json_encode($plot);
            return $plot;
        }
        else {
            echo json_encode($output);
            return $output;
        }
    }

    function gradeUp($runningGrades){
        $summationGrades = array();
        foreach($runningGrades as $gradeInfo)
        {
            if(isset($summationGrades[$gradeInfo[0]]))
            {
                $summationGrades[$gradeInfo[0]][1] +=  $gradeInfo[2];
                $summationGrades[$gradeInfo[0]][2]++;
            }
            else
            {
                $summationGrades[$gradeInfo[0]] = array($gradeInfo[1], $gradeInfo[2], 1);
            }
        }

        $totalPer = 0;
        $runningAvg = 0;

        foreach($summationGrades as $summation)
        {
            $runningAvg += (($summation[1] / $summation[2]) * $summation[0] / 100);
            $totalPer += $summation[0];
        }

        $runningAvg = $runningAvg / $totalPer * 100;
        return $runningAvg;
    }

    function add($assessment, $percentage, $course) {

        $db = new DatabaseConnector();

        $stmt = "INSERT into AssessmentType (assessmentName, percentage, studentCourseID) VALUES (?, ?, (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' AND userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?)))";
        $params = array($assessment, $percentage, $this->userID, $course);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "AssessmentType inserted into database");
        $return = ["success"];
        echo json_encode($return);

    }

    function addGrade($course, $assessment, $grade) {

        $db = new DatabaseConnector();

        $stmt = "INSERT into Assessment (assessmentTypeID, grade, studentCourseID, dateEntered) VALUES ((SELECT assessmentTypeID FROM AssessmentType WHERE StudentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?)) AND assessmentName = ?), ?, (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?)), '" . date("Y-m-d") ."')";
        $params = array($this->userID, $course, $assessment, $grade, $this->userID, $course);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "Grade inserted into database");
        $return = ["success"];
        echo json_encode($return);

    }

    function removeGrade($grade, $assessment, $course) {

        $db = new DatabaseConnector();

        $stmt = "Delete from Assessment WHERE grade = ? AND assessmentTypeID in (select assessmentTypeID FROM AssessmentType WHERE assessmentName = ?) AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? and courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?)) limit 1";
        $params = array($grade, $assessment, $this->userID, $course);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "Grade removed from database");
        $return = ["success"];
        echo json_encode($return);

    }

    function modifyGrade($newGrade, $grade, $assessment, $course) {

        $db = new DatabaseConnector();

        $stmt = "UPDATE Assessment SET grade = ? WHERE grade = ? AND assessmentTypeID in (select assessmentTypeID from AssessmentType where assessmentName = ?) AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID from CourseInfo where courseID = ?)) limit 1";

        $params = array($newGrade, $grade, $assessment, $this->userID, $course);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "Grade modified in database");
        $return = ["success"];
        echo json_encode($return);
    }

    function removeBucket($assessment, $course) {

        $db = new DatabaseConnector();

        $stmt = "DELETE from AssessmentType WHERE  AssessmentName = ? AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? and courseInfoID IN (SELECT courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($assessment, $this->userID, $course);
        $db->query($stmt, $params);

        $this->log->toLog(1, 'INFO', __METHOD__, "Bucket removed from database");
        $return = ["success"];
        echo json_encode($return);

    }

    function getGrades($assessment, $course) {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT grade FROM Assessment WHERE assessmentTypeID in (select assessmentTypeID FROM AssessmentType WHERE AssessmentName = ?) AND studentCourseID in (SELECT studentCourseID FROM StudentCourse WHERE grade = 'IP' and userID = ? AND courseInfoID in (select courseInfoID FROM CourseInfo WHERE courseID = ?))";
        $params = array($assessment, $this->userID, $course);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            $this->log->toLog(2, __METHOD__, "No grades returned form assessment");
            echo json_encode([]);
            return $return;
        }

        $index = 1;
        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $grades = $output[$i][0];

            $this->log->toLog(0, __METHOD__, "Grade: $grades");
            array_push($return, array("Grade" . $index, $grades));
            $index++;
        }

        echo json_encode($return);
        return $return;
    }

} //end of semesterDashboardController()