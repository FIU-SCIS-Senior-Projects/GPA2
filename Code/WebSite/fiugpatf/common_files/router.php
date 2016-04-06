<?php

include_once 'settingscontroller.php';

class Router
{
   protected $action;
   protected $session_name = 'sec_session_id';
   protected $secure = false;
   protected $httponly= true;
   protected $cookieParams;
   protected $user;

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
         case "importAudit":
            $controller = new SettingsController($_SESSION['userID'], $_SESSION['username']);
            $controller-> $action();
            break;
         case "prepareTable":
            $controller = new SettingsController($_SESSION['userID'], $_SESSION['username']);
            $controller-> $action();
            break;
         case "importReq":
            $controller = new SettingsController($_SESSION['userID'], $_SESSION['username']);
            $controller-> $action($_FILES['file']['tmp_name']);
            break;
      }
   }
}

if (isset($_POST['action']))
   $action = $_POST['action'];
else
   $action = $_POST['action'];

$pageRouter = new Router($action);