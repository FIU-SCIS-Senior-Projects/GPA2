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

    public function __construct($userID, $username)
    {
        $this->userID = $userID;
        $this->username = $username;
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

            $grade = $this->getGrade($user, $courseID);
            if($grade != 'No Grades') {
                array_push($return, array($courseID, $courseName, $credit, round($grade, 2)));
            }
            else
            {
                array_push($return, array($courseID, $courseName, $credit, $grade));
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
        }

        if($count != 0) {
            return round($runAvg / $count, 2);
        }
        else {
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
          AND b.assessmentTypeID = a.assessmentTypeID ORDER BY dateEntered";
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
                        array_push($plots, array($this->dateOfTerm($semester, $tp, $year), $ag));
                        $currAverage = $ag;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $y++;
                } else {
                    array_push($plots, array($this->dateOfTerm($semester, $y, $year), $currAverage));
                    $y++;
                }
            }
            //$allPlots for all the courses
            array_push($allPoints, $plots);

        }

        echo json_encode($allPoints);
        return $allPoints;
    }

    function dateOfTerm($term, $timePeriod, $year) {

        if($term == 'fall') {
            $fallStart = date('Y-m-d', strtotime('third monday of august' . $year));

            if($timePeriod == 0) {
                return strtotime('third monday of august' . $year) * 1000;
            }
            else if($timePeriod == 1) {
                return strtotime($fallStart . '+ 3 weeks') * 1000;
            }
            else if($timePeriod == 2) {
                return strtotime($fallStart . '+ 6 weeks') * 1000;
            }
            else if($timePeriod == 3) {
                return strtotime($fallStart . '+ 9 weeks') * 1000;
            }
            else if($timePeriod == 4) {
                return strtotime($fallStart . '+ 12 weeks') * 1000;
            }
            else if($timePeriod == 5) {
                return strtotime($fallStart . '+ 15 weeks') * 1000;
            }
            else if($timePeriod == 6) {
                return strtotime($fallStart . '+ 18 weeks') * 1000;
            }
            else {
                return 'summer';
            }
        }
        else if($term == 'spring') {
            $springStart = date('Y-m-d', strtotime('second monday of january' . $year));

            if($timePeriod == 0) {
                return strtotime('second monday of january' . $year) * 1000;
            }
            else if($timePeriod == 1) {
                return strtotime($springStart . '+ 3 weeks') * 1000;
            }
            else if($timePeriod == 2) {
                return strtotime($springStart . '+ 6 weeks') * 1000;
            }
            else if($timePeriod == 3) {
                return strtotime($springStart . '+ 9 weeks') * 1000;
            }
            else if($timePeriod == 4) {
                return strtotime($springStart . '+ 12 weeks') * 1000;
            }
            else if($timePeriod == 5) {
                return strtotime($springStart . '+ 15 weeks') * 1000;
            }
            else if($timePeriod == 6) {
                return strtotime($springStart . '+ 18 weeks') * 1000;
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
                if($currCourse == $co) { //look fot same course
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

} //end of semesterDashboardController()