<?php
include_once 'psl-config.php';
$conn = new mysqli(HOST, USER, PASSWORD, DATABASE);

if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$hash_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO Users (email, username, password, firstName, lastName, type) 
		VALUES (?, ?, ?, ?, ?, 0);");
$stmt->bind_param('sssss', $_POST["email"], $_POST["username"], $hash_password, $_POST["first_name"], $_POST["last_name"]);
?>

<!DOCTYPE= html>
<html>
	<head>
		<title>Congratulations</title>
		<link type="text/css" rel="stylesheet" href="../css/main.css">
	</head>
	<body>
		<div id="header">
		<div class="headerLinks" class="container clearfix">
			<div class="logo">
				<img class="logo" src="http://www.fiu.edu/_assets/images/core/fiu-logo-large.png" />
				<a href="../index.html">GPA Tracker and Forecaster</a>
			</div>
			<div class="nav">
				<ul>
					<li><a href="register.html">Register</a></li>
					<li><a href="../login.html">Log in</a></li>
					<li><a href="../overallgpadashboard/about.html">About</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="jumbotronReg">
		<div class="textbox">
			<?php 
				if($stmt->execute() === TRUE)
				{
					echo "<h1>Congratulations!</h1>";
					echo "<p>You have succesfully registered.</p>";
				}
				else
				{
					echo "<h1>Something went wrong.</h1>";
					echo "<p>The username or email you selected was taken.</p>";
				}
			?>
		</div>
	</div>
	</body>
</html>
