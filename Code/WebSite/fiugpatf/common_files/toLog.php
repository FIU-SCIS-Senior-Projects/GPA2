<?php
class workerThread extends Thread {
    protected $id;
    protected $info; 
    protected $settings; 

    //protected $root = '/home/GPA2/Code/WebSite/fiugpatf';
    protected $root = '/home/sproject/GPA2/Code/WebSite/fiugpatf';
   
    public function __construct($id, $info){
        $this->id = $id;
        $this->info = $info;
        $this->msgSemKey = sem_get(9876543210);
        $this->queKey = msg_get_queue(123456788);
    }

    public function run(){
        $settingSemKey = sem_get(9876543212);
        $settingMemKey = shm_attach(123456788);
        $settingKey = 666666666;

        sem_acquire($settingSemKey);

        if (shm_has_var($settingMemKey, $settingKey))
        {
           $this->settings = shm_get_var($settingMemKey, $settingKey);
           //echo "has var\n";
        }
        else
        {
           $this->settings = parse_ini_file("$this->root/common_files/settings.ini", true);
           shm_put_var($settingMemKey, $settingKey, $this->settings);
           //echo "no var\n";
        }
         
        sem_release($settingSemKey);

        $this->printt();

    }

    function printt(){
        sem_acquire($this->msgSemKey);

        $mode = $this->settings['error_mode']['mode'];

        if ($this->id == 0)
        {
            if ($mode == 'ERROR') {
                sem_release($this->msgSemKey);
                return;
            }
            else if ($mode == 'WARNING') {
                sem_release($this->msgSemKey);
                return;
            }
            else if($mode == 'INFO') {
                sem_release($this->msgSemKey);
                return;
            }
        }
        else if ($this->id == 1)
        {
            if ($mode == 'ERROR'){
                sem_release($this->msgSemKey);
                return;
            }
            else if($mode == 'WARNING'){
                sem_release($this->msgSemKey);
                return;
            }
        }
        else if ($this->id == 3)
        {
            if ($mode == 'ERROR'){
                sem_release($this->msgSemKey);
                return;
            }
        }

        $x = $this->info;
        $time = microtime(true);
        $dFormat = "m/d/Y - H:i:s:";
        $mSecs = $time - floor($time);
        $mSecs = substr($mSecs, 2, 4);
        $date = sprintf('%s%s', date($dFormat), $mSecs);

        $type = $this->settings['error_types'][$this->id];

        msg_send($this->queKey, 1, "$date $type $x\n");

        sem_release($this->msgSemKey);

        $this->checkConsumer();
    }

    function checkConsumer(){
        $flgSemKey = sem_get(9876543211);
        $memKey = shm_attach(123456789);
        $flgKey = 555555555;

        sem_acquire($flgSemKey);

        if (shm_has_var($memKey, $flgKey))
        {
            $flag = shm_get_var($memKey, $flgKey);

            if ($flag == 0)
            {
                exec("(cd $this->root/common_files/ && exec php consumer.php > /dev/null 2>/dev/null &)");
                $flag = 1;
                shm_put_var($memKey, $flgKey, $flag);
            }
        }
        else
        {
            exec("(cd $this->root/common_files/ && exec php consumer.php > /dev/null 2>/dev/null &)");
            $flag = 1;
            shm_put_var($memKey, $flgKey, $flag);
        }

        sem_release($flgSemKey);
    }

}

class ErrorLog {
    protected $host;

    public function __construct() {
        $this->host = php_uname('n');
    }

    public function toLog($error_id, $location, $details){

        $worker = new workerThread("$error_id", "$location $this->host $details");
        $worker->start();

        return $error_id;
    }

}
?>

