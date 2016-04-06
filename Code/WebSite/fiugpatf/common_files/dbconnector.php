<?php
include_once 'toLog.php';
class DatabaseConnector
{
   protected $host = "localhost";
   protected $user = "sec_user";
   protected $pass = "Uzg82t=u%#bNgPJw";
   protected $dbse = "GPA_Tracker";
   protected $mysqli;
   protected $log;

   public function __construct()
   {
      $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->dbse);
      $this->log = new ErrorLog();

      if(!$this->mysqli)
      {
         $this->log->toLog(2, __METHOD__, "Error connection $this->mysqli->error");
      }
   }

   public function select($stmt, $params)
   {
      $conn = $this->mysqli->prepare($stmt);

      if (!$conn)
      {
         $err = $this->mysqli->error;
         $this->log->toLog(2, __METHOD__, "Prepare stmt failed $err");
         return -1;
      }

      $aParams = array();
      $paramType = '';
      $n = count($params);
      for($i=0; $i< $n; $i++)
      {
         $paramType .= 's';
      }

      $aParams[] = & $paramType;
      for($i=0; $i< $n; $i++)
      {
         $aParams[] = & $params[$i];
      }

      if ($n > 0) {
         if (!(call_user_func_array(array($conn, 'bind_param'), $aParams))) {
            $this->log->toLog(2, __METHOD__, "Error binding $conn->error");
            return -1;
         }
      }

      if (!($conn->execute()))
      {
         $this->log->toLog(2, __METHOD__, "Error executing $conn->error");
         return -1;
      }

      $result = $conn->get_result();
      $output = array();

      while ($row = $result->fetch_array(MYSQLI_NUM))
         $output[] = $row;

      $conn->close();
      return $output;
   }

   public function query($stmt, $params)
   {
      $conn = $this->mysqli->prepare($stmt);

      if (!$conn)
      {
         $err = $this->mysqli->error;
         $this->log->toLog(2, __METHOD__, "Prepare stmt failed $err");
         return -1;
      }

      $aParams = array();
      $paramType = '';
      $n = count($params);
      for($i=0; $i< $n; $i++)
      {
         $paramType .= 's';
      }

      $aParams[] = & $paramType;
      for($i=0; $i< $n; $i++)
      {
         $aParams[] = & $params[$i];
      }

      if (!(call_user_func_array(array($conn, 'bind_param'), $aParams)))
      {
         $this->log->toLog(2, __METHOD__, "Error binding $conn->error");
         return -1;
      }

      if ($conn->execute() == false)
      {
         $this->log->toLog(2, __METHOD__, "Error executing $conn->error");
         return -1;
      }
      $conn->close();
   }

   function __destruct()
   {
      $this->mysqli->close();
   }
}