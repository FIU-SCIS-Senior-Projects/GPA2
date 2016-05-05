<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start(); // secure way of starting a PHP session.

if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (login($username, $password, $mysqli) == true) {
        $user = $username;
        $stmt = $mysqli->prepare("SELECT type FROM Users WHERE userName = ? ");
        $stmt->bind_param('s', $user);
        $stmt->execute(); // Execute the prepared query.
        $stmt->store_result();
        $stmt->bind_result($type);
        //".$username."
        $typeUser;
        while ($stmt->fetch()) {
            $typeUser = $type;
        }
        echo $type;
        
        
        
        if ($type == "1") {
            
            //Login success 
            header('Location: admin.html');
        } else {
            header('Location: ../overallgpadashboard/OvrlDash.html');
        }
        
    } else {
        // Login failed 
        header('Location: ../login.html');
        echo "<h1>login failed</h1>";
    }
    
    
} else {
    //     The correct POST variables were not sent to this page. 
    echo "Invalid Request";
}


?>
