<?php
include_once 'OvrlDashController.php';
include_once 'GPAForecastController.php';

class OverallDashboardRouter
{
    protected $session_name = 'sec_session_id';   // Set a custom session name
    protected $secure = false; // This stops JavaScript being able to access the session id.
    protected $httponly = true; // Forces sessions to only use cookies.
    protected $cookieParams;
    protected $action;

    public function __construct($action)
    {
        if (ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
            exit();
        }

        $this->cookieParams = session_get_cookie_params();

        session_set_cookie_params($this->cookieParams["lifetime"],
            $this->cookieParams["path"],
            $this->cookieParams["domain"],
            $this->secure,
            $this->httponly);

        session_name($this->session_name);
        session_start();

        $this->action = $action;
        $this->route();
    }

    public function route()
    {
        $action = $this->action;

        switch ($action)
        {
            case "getMajorBuckets":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "getMajorBucketsNeeded":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "getProgramInfo":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "remainingCourses":
                $controller = new GPAForecastController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "getGraphData":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "GPAGoal":
                $controller = new GPAForecastController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "takenAndRemaining":
                $controller = new GPAForecastController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "gradesAndCredits":
                $controller = new GPAForecastController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "checkWeightAndRelevance":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "getGPA":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "findChildBuckets":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['bucket']);
                break;
            case "getMajorBucketsChildBuckets":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['bucket']);
                break;
            case "getMajorBucketsCourseNeeded":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['bucket']);
                break;
            case "getMajorBucketsCourse":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['bucket']);
                break;
            case "deleteCourseNeeded":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "addCourse":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "editStudent":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "modCourse":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "modWeight":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['course']);
                break;
            case "deleteItem":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "saveTargetGPA":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action($_POST['gpa']);
                break;
            case "getAllCoursesTaken":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
            case "getAllCoursesNeeded":
                $controller = new OverallDashboardController($_SESSION['userID'], $_SESSION['username']);
                $controller->$action();
                break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
    $_POST = json_decode(file_get_contents('php://input'), true);
$pageRouter = new OverallDashboardRouter($_POST['action']);
