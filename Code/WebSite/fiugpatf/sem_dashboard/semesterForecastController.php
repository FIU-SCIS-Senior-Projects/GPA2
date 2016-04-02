<?php
/**
 * Created by PhpStorm.
 * User: Lizette Mendoza
 * Date: 3/9/16
 * Time: 11:57 PM
 */
include_once'../common_files/dbconnector.php';
include_once '../common_files/toLog.php';

class SemesterForecastController {

    protected $userID;
    protected $username;

    public function __construct($userID, $username)
    {
        $this->userID = $userID;
        $this->username = $username;
    }

    function GPAGoal() {
        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT gpaGoal FROM Users WHERE userID = ?";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if($output[0][0] == "") {
            toLog(2, "ERROR", "SemesterForecastController/GPAGoal", "GPA Goal is null");
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
            toLog(2, 'ERROR', __METHOD__, "No courses remaining");
            echo json_encode('No courses remaining');
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

        $stmt = "SELECT StudentCourse.grade, CourseInfo.credits FROM CourseInfo INNER JOIN StudentCourse
             ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE StudentCourse.userID = ?
             AND NOT StudentCourse.grade = 'IP' AND NOT StudentCourse.grade = 'ND' AND NOT StudentCourse.grade = 'DR'
             AND NOT StudentCourse.grade = 'TR'";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            toLog(2, 'ERROR', __METHOD__, "No course grades or course credits");
            echo json_encode('No grades');
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {
            $courseGrade = $output[$i][0];
            $courseCredits = $output[$i][1];
            array_push($return, array($courseGrade, $courseCredits));
        }

        echo json_encode($return);
        return $return;
    }

    function currentCourses() {

        $db = new DatabaseConnector();
        $return = [];

        $stmt = "SELECT CourseInfo.courseID, CourseInfo.courseName, CourseInfo.credits, StudentCourse.weight, StudentCourse.relevance FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE grade = 'IP' AND userID = ? ";
        $params = array($this->userID);
        $output = $db->select($stmt, $params);

        if(count($output) == 0) {
            toLog(2, 'ERROR', __METHOD__, "No current course information");
            echo json_encode([]);
            return;
        }

        for ($i = 0, $c = count($output); $i < $c; $i++) {

            if($output[$i][3] == "") {
                toLog(3, "WARNING", __METHOD__, "weight is null");
            }
            if($output[$i][4] == "") {
                toLog(3, "WARNING", __METHOD__, "relevance is null");
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

    // UPDATE weight and relevance in DB
    function modifyWeightAndRelevance() {

        $db = new DatabaseConnector();

        if (isset($_POST['courseID'])) {
            $courseID = $_POST['courseID'];
        } else {
            $courseID = "";
        }

        if (isset($_POST['modifiedRelevance'])) {
            $modifiedRelevance = $_POST['modifiedRelevance'];
        } else {
            $modifiedRelevance = "";
        }

        if (isset($_POST['modifiedWeight'])) {
            $modifiedWeight = $_POST['modifiedWeight'];
        } else {
            $modifiedWeight = "";
        }

        $stmt = "UPDATE StudentCourse SET relevance = ?, weight = ? WHERE courseInfoID = (SELECT courseInfoID FROM CourseInfo WHERE courseID = ?) AND userID = ?";
        $params = array($modifiedRelevance, $modifiedWeight, $courseID, $this->userID);
        $db->query($stmt, $params);
        toLog(1, "INFO", "SemesterForecastController/modifyWeightAndRelevance", "weight and relevance have been modified");
    }



} //end of semesterForecastController()
