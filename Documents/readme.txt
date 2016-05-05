The directory structure of the Code branch contains four directories, One is for the android app. this directory contains the necessary code behind running the android app and utilizing the web services with android studio. Another directory is for the website. The sub directories are broken up into the major components that make the website.Lastly, the remaining two relate to the webservices. One directory is for the web clients used to test the webservices from an ide such as eclipse.


-Give write access to common_files folder
   apache needs write access to this folder
   sudo chmod -R 777 common_files/

-Change root for error log
   The following files in the common_files folder must be changed to show the current root. The directory structure in 
   the server may be different than your local VM.

   toLog.php    #line 7-8
   consumer.php #line 14
   settings.ini #line 8-9 

-Changing logging level
   To change the logging level first change the mode in the settings.ini file. Then run ' php clearMem.php ' to clear
   so that php can reload the ini file.
