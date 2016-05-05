<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/22/16
 * Time: 3:52 PM
 */

include_once'../common_files/dbconnector.php';
include_once '../common_files/toLog.php';

class GPAForecastController {

    protected $userID;
    protected $username;
    protected $log;

    public function __construct($userID, $username)
    {
        $this->userID = $userID;
        $this->username = $username;
        $this->log = new ErrorLog();
    }

    function GPAGoal() {
        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT gpaGoal FROM Users WHERE userID = ?";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if($output[0][0] == "") {
            $this->log->toLog(2, __METHOD__, "GPA Goal is null");
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $gpaGoal = $output[$i][0];
            array_push($return, array($gpaGoal));
        }

        echo json_encode($return);
        return $return;
    }

    //Query DB for creditsTaken and creditsLeft
    function takenAndRemaining() {
        $db = new DatabaseConnector();
        $return = [];

        $stmt   = "SELECT SUM(CourseInfo.credits) FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE (grade = 'IP' OR grade = 'ND') AND userID = ?";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if($output[0][0] == '') {
            $this->log->toLog(2, __METHOD__, "No previous course information available");
            echo json_encode('No grades');
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $creditsLeft = $output[$i][0];
            array_push($return, array($creditsLeft));
        }

        echo json_encode($return);
        return $return;

    }

    //Query DB for grades and course credits for courses completed at FIU
    function gradesAndCredits() {
        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT StudentCourse.grade, CourseInfo.credits FROM CourseInfo INNER JOIN StudentCourse ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE StudentCourse.userID = ? AND NOT StudentCourse.grade = 'IP' AND NOT StudentCourse.grade = 'ND' AND NOT StudentCourse.grade = 'DR' AND NOT StudentCourse.grade = 'TR' ORDER BY StudentCourse.year, StudentCourse.semester ";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0)
        {
            $this->log->toLog(2, __METHOD__, "No course grades or course credits");
            echo json_encode('No grades');
            return 'No grades';
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $courseGrade = $output[$i][0];
            $courseCredits = $output[$i][1];
            array_push($return, array($courseGrade, $courseCredits));
        }

        echo json_encode($return);
        return $return;
    }

    function remainingCourses()
    {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT CourseInfo.courseID, CourseInfo.courseName, CourseInfo.credits, StudentCourse.weight, StudentCourse.relevance FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE userID = ? AND (grade = 'IP' OR grade = 'ND')";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0)
        {
            $this->log->toLog(2, __METHOD__, "No course information available");
            echo json_encode([]);
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {

            if ($output[$i][3] == "") {
                $this->log->toLog(3, __METHOD__, "weight is null");
            } else if ($output[$i][4] == "") {
                $this->log->toLog(3, __METHOD__, "relevance is null");
            }

            $courseID = $output[$i][0];
            $courseName = $output[$i][1];
            $credits = $output[$i][2];
            $courseWeight = $output[$i][3];
            $courseRelevance = $output[$i][4];

            array_push($return, array($courseID, $courseName, $credits, $courseWeight, $courseRelevance));
        }

        echo json_encode($return);
        return $return;
    }
}