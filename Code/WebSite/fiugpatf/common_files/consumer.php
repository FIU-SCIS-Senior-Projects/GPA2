<?php

$flgSemKey = sem_get(9876543211);
$memKey = shm_attach(123456789);
$queKey = msg_get_queue(123456788);
$flgKey = 555555555;
$settingMemKey = shm_attach(123456788);
$settingKey = 666666666;
$flag = 0;
$received = true;
$first = true;

$settings = shm_get_var($settingMemKey, $settingKey);
$file = $settings['log_location']['server'];
$log = fopen($file, "a");


while ($received){
   
   sem_acquire($flgSemKey);

   $received = msg_receive ($queKey, 1, $msg_type, 16384, $msg, true, MSG_IPC_NOWAIT, $msg_error);
   
   if ($received){
      fwrite($log, $msg);
   }
   else
   {
      shm_put_var($memKey, $flgKey, $flag);
   }

   sem_release($flgSemKey);

}
?>
