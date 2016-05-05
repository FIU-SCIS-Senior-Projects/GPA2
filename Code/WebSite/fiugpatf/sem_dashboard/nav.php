<?php
include_once '../common_files/psl-config.php';
include_once '../common_files/functions.php';

sec_session_start();

if(isset($_SESSION['username']))
{
    echo "<li><a href=\"../overallgpadashboard/OvrlDash.html\">GPA Dashboard</a></li>";
    echo "<li><a href=\"./semesterDashboard.html\">Semester Dashboard</a></li>";
	echo "<li><a href=\"../common_files/settings.html\">Settings</a></li>";
    echo "<li><a href=\"../common_files/logout.php\">Logout</a></li>";
    
    echo '<li>';
    echo '  <div class="dropdown">';
    echo '    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown">Help <span class="caret"></span></a>';
    echo '      <ul class="dropdown-menu" style="margin-top: 1px;">';
    echo '        <li>';
    echo '          <a href="../overallgpadashboard/OvrlDash_help_contents.html">GPA Dashboard Help</a>';
    echo '        </li>';
    echo '        <li>';
    echo '          <a href="../sem_dashboard/SemHelpContents.html">Semester Dashboard Help</a>';
    echo '        </li>';
    echo '        <li>';
    echo '          <a href="../overallgpadashboard/about.html">About</a>';
    echo '        </li>';
    echo '      </ul>';
    echo '  </div>';
    echo '</li>';
}
else
{
    echo "<li><a href=\"../common_files/register.html\">Register</a></li>";
    echo "<li><a href=\"../login.html\">Log in</a></li>";
    echo "<li><a href=#>About</a></li>";
}
?>
