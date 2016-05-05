<?php
include_once'../common_files/dbconnector.php';
$session_name = 'sec_session_id';   // Set a custom session name
$secure = FALSE;
// This stops JavaScript being able to access the session id.
$httponly = true;
// Forces sessions to only use cookies.
if (ini_set('session.use_only_cookies', 1) === FALSE) {
    header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
    exit();
}
// Gets current cookies params.

$cookieParams = session_get_cookie_params();
session_set_cookie_params($cookieParams["lifetime"],
    $cookieParams["path"],
    $cookieParams["domain"],
    $secure,
    $httponly);
// Sets the session name to the one set above.
session_name($session_name);
session_start();

if($_POST['action'] == 'getGrades')
{
    if(isset($_SESSION['userID']))
    {
        $mysqli = new mysqli("localhost","sec_user","Uzg82t=u%#bNgPJw","GPA_Tracker");
        $user = $_SESSION['userID'];
        $stmt = $mysqli->prepare("SELECT grade
        FROM   Assessment
        WHERE  assessmentTypeID in (select assessmentTypeID 
        	FROM AssessmentType 
        	WHERE AssessmentName = ?) 
        AND 
        studentCourseID in (SELECT studentCourseID
        	FROM StudentCourse
			WHERE grade = 'IP' and userID = ? 
			AND courseInfoID in (select courseInfoID 
				FROM CourseInfo 
				WHERE courseID = ?))");
        $stmt->bind_param('sss', $_POST['assessment'], $user, $_POST['course']);
        $stmt->execute();
        $stmt->bind_result($grades);
        $output = array();
        $index = 1;
        while($stmt->fetch())
        {
            array_push($output, array("Grade" . $index, $grades));
            $index++;
        }
        echo json_encode($output);
        unset($_POST['assessment']);
    }
}




