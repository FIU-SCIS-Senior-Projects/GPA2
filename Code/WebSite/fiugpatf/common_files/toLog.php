<?php
class workerThread extends Thread {

   public function __construct($i){
      $this->i = $i;
      $this->msgSemKey = sem_get(9876543210);
      $this->queKey = msg_get_queue(123456788);
   }

   public function run(){

      date_default_timezone_set('America/New_York');

      sem_acquire($this->msgSemKey);

      $x = $this->i;
      $time = microtime(true);
      $dFormat = "m/d/Y - H:i:s:";
      $mSecs = $time - floor($time);
      $mSecs = substr($mSecs, 2, 4);
      $date = sprintf('%s%s', date($dFormat), $mSecs);

      msg_send($this->queKey, 1, "$date $x\n");

      sem_release($this->msgSemKey);

   }

}


function toLog($error_id, $error_type, $location, $details){
   
   $host = php_uname('n');
   $worker = new workerThread("$error_id $error_type $location $host $details");
   $worker->start();
   checkConsumer();
   
   return $error_id;

}

function checkConsumer(){

      $root = '/home/sproject/GPA2/Code/WebSite/fiugpatf';
      //protected $root = $_SERVER['DOCUMENT_ROOT'];

      $flgSemKey = sem_get(9876543211);
      $memKey = shm_attach(123456789);
      $flgKey = 555555555;
      $flag = 0;
      
      sem_acquire($flgSemKey);

      if (shm_has_var($memKey, $flgKey))
      {
         $flag = shm_get_var($memKey, $flgKey);

         if ($flag == 0)
         {
             exec("(cd $root/common_files/ && exec php consumer.php > /dev/null 2>/dev/null &)");
            $flag = 1;
            shm_put_var($memKey, $flgKey, $flag);
         }
      }
      else
      {
          exec("(cd $root/common_files/ && exec php consumer.php > /dev/null 2>/dev/null &)");
         $flag = 1;
         shm_put_var($memKey, $flgKey, $flag);
      }

      sem_release($flgSemKey);

}

?>

