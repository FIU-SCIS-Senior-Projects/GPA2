<?php
include_once '../common_files/db_connect.php';
include_once '../common_files/functions.php';
sec_session_start();
if (isset($_POST['adminUser'])) {
    $_SESSION['username'] = $_POST['adminUser'];
    $user                 = $_SESSION['username'];
    $stmt1                = $mysqli->prepare("SELECT userID FROM Users WHERE  userName  = ?");
    $stmt1->bind_param('s', $user);
    $stmt1->execute();
    $stmt1->bind_result($output1);
    $userID = array();
    while ($stmt1->fetch()) {
        array_push($userID, $output1);
        
    }
	 
    $_SESSION['userID'] = $userID[0];
     $_SESSION['type'] = 1;
} else {
}
if (isset($_POST['type'])) {
    $type = $_POST['type'];
} else {
    $type = '0';
}


if (isset($_POST['action'])) {
    //echo json_encode('inside isset');
    $action = $_POST['action'];
} else {
    var_dump($_POST);
    echo 'inside else';
    $action = "";
}

if ($action == "addExtraCourse") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    // get username, user ID and courseInfo ID
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt2  = $mysqli->prepare("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = '" . $courseID . "'");
    $stmt2->execute();
    $stmt2->bind_result($output2);
    $course = array();
    while ($stmt2->fetch()) {
        array_push($course, $output2);
        
    }
    if ($stmt = $mysqli->prepare("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = '" . $courseID . "'")) {
        
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($CID);
        $stmt->fetch();
        
        if ($stmt->num_rows() > 0) {
            
            
            //if the course is in CourseInfo table, then check the courses in the major
            
            $sql    = "SELECT courseInfoID FROM MajorBucketRequiredCourses WHERE  courseInfoID = '" . $course[0] . "' And bucketID = bucketID in (Select bucketID From MajorBucket Where description = '" . $bucket . "' and majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) ";
            $query2 = $mysqli->prepare($sql);
            $query2->execute();
            $query2->bind_result($output3);
            $query2->fetch();
            
            if ($query2->num_rows() === 0) //if the course isnt in the major, insert it.
                {
                $grade       = "ND";
                $weight      = "3";
                $getBucketID = $mysqli->prepare("SELECT bucketID FROM MajorBucket WHERE  description  = '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "') ");
                $getBucketID->execute();
                $getBucketID->bind_result($BID);
                $bucketRef = array();
                while ($getBucketID->fetch()) {
                    array_push($bucketRef, $BID);
                    
                }
                
                $sqlInsert = "INSERT INTO StudentCourse (userID, courseInfoID, grade, weight,relevance,referenceBucket) VALUES(\"{$userID}\", \"{$course[0]}\",\"{$grade}\",\"{$weight}\", 3, \"{$bucketRef[0]}\")";
                
                if ($mysqli->query($sqlInsert) === TRUE) {
                    $result = array(
                        'success' => true
                    );
                } else {
                    $result = array(
                        'success' => true,
                        'message' => 'Course could not be inserted'
                    );
                }
            } else {
                $result = array(
                    'success' => true,
                    'message' => 'Course could not be inserted'
                );
            }
            
            
        } else {
            $result = array(
                'success' => false,
                'message' => 'Course could not be inserted'
            );
        }
    }
    echo json_encode($result);
}



if ($action == "getMinReq") {
    
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    $userID = $_SESSION['userID'];
    $user   = $_SESSION['username'];
    $stmt5  = $mysqli->prepare("SELECT `quantityNeeded` FROM `MajorBucket` WHERE `description` = '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')");
    $stmt5->execute();
    $stmt5->bind_result($qtyN);
    $output2 = array();
    while ($stmt5->fetch()) {
        array_push($output2, $qtyN);
        
    }
    /*get the sum of credits from classes in the user's major MajorBucketRequiredCourses that have been taken, or have been 		    selected, Unioned with the sum of credits from classes in the user's StudentCourse table that have been taken or have been selected.
*/
    $stmt1 = $mysqli->prepare("Select SUM(credits) FROM (
SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade, StudentCourse.selected FROM CourseInfo INNER JOIN StudentCourse ON  
CourseInfo.courseInfoID = StudentCourse.courseInfoID AND StudentCourse.courseInfoID in
 (Select  courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description =  '" . $bucket . "' AND 
 majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))
 AND userID = '" . $userID . "' AND  StudentCourse.selected in
 (SELECT selected FROM StudentCourse WHERE selected= '1') 
    
    UNION 
 SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade, StudentCourse.selected FROM CourseInfo INNER JOIN StudentCourse ON 
	CourseInfo.courseInfoID = StudentCourse.courseInfoID AND StudentCourse.courseInfoID in
	(Select  courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description =  '" . $bucket . "' AND
	majorID = majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))
	AND userID = '" . $userID . "' AND NOT  StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND') 

UNION
 Select CourseInfo.courseID, CourseInfo.credits,  StudentCourse.grade, StudentCourse.selected From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in 
 (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND  majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) AND
 StudentCourse.courseInfoID = CourseInfo.courseInfoID  AND  StudentCourse.selected in
 (SELECT selected FROM StudentCourse WHERE selected= '1')

   UNION 
 Select CourseInfo.courseID, CourseInfo.credits,  StudentCourse.grade, StudentCourse.selected From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in 
 (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) AND 
 StudentCourse.courseInfoID = CourseInfo.courseInfoID AND NOT  StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND') 

  )a;
  ");
    
    $stmt1->execute();
    $stmt1->bind_result($qtyS);
    $output = array();
    while ($stmt1->fetch()) {
        array_push($output, array(
            $qtyS,
            $output2[0]
        ));
        
    }
    echo json_encode($output);
    
    
    
}


if ($action == "getMinBucketReq") {
    
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    $userID = $_SESSION['userID'];
    $user   = $_SESSION['username'];
    $stmt5  = $mysqli->prepare("SELECT `quantityNeeded` FROM `MajorBucket` WHERE `description` = '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')");
    $stmt5->execute();
    $stmt5->bind_result($qtyN);
    $output2 = array();
    while ($stmt5->fetch()) {
        array_push($output2, $qtyN);
        
    }
    // get all child buckets name's
    $stmt1 = $mysqli->prepare("SELECT description FROM MajorBucket WHERE  majorID in (SELECT majorID FROM StudentMajor WHERE userID = '" . $userID . "') and parentID in (select bucketID FROM MajorBucket where description = '" . $bucket . "')
  ");
    
    $stmt1->execute();
    $stmt1->bind_result($qtyS);
    $output   = array();
    $selected = 0;
    while ($stmt1->fetch()) {
        $selected = $selected + totalSelect($qtyS);
        
    }
    array_push($output, array(
        $selected,
        $output2[0]
    ));
    echo json_encode($output);
    
    
    
}

function totalSelect($qtyS)
{
    $userID = $_SESSION['userID'];
    $total  = 0;
    $bucket = $qtyS;
    $output = array();
    $conn   = new mysqli("localhost", "root", "sqliscool", "GPA_Tracker");
    // get credits selected for a bucket (all IP courses and selected courses in the this major bucket. and also with referenceBucket
    
    $stmt1 = $conn->prepare("Select SUM(credits) FROM (
SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade, StudentCourse.selected FROM CourseInfo INNER JOIN StudentCourse ON  
CourseInfo.courseInfoID = StudentCourse.courseInfoID AND StudentCourse.courseInfoID in
 (Select  courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description =  '" . $bucket . "' AND 
 majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))
 AND userID = '" . $userID . "' AND  StudentCourse.selected in
 (SELECT selected FROM StudentCourse WHERE selected= '1') 
    
    UNION 
 SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade, StudentCourse.selected FROM CourseInfo INNER JOIN StudentCourse ON 
	CourseInfo.courseInfoID = StudentCourse.courseInfoID AND StudentCourse.courseInfoID in
	(Select  courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description =  '" . $bucket . "' AND
	majorID = majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))
	AND userID = '" . $userID . "' AND NOT  StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND') 

UNION
 Select CourseInfo.courseID, CourseInfo.credits,  StudentCourse.grade, StudentCourse.selected From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in 
 (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND  majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) AND
 StudentCourse.courseInfoID = CourseInfo.courseInfoID  AND  StudentCourse.selected in
 (SELECT selected FROM StudentCourse WHERE selected= '1')

   UNION 
 Select CourseInfo.courseID, CourseInfo.credits,  StudentCourse.grade, StudentCourse.selected From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in 
 (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) AND 
 StudentCourse.courseInfoID = CourseInfo.courseInfoID AND NOT  StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND') 

  )a;
  ");
    
    $stmt1->execute();
    $stmt1->bind_result($qtySelected);
    
    while ($stmt1->fetch()) {
        
        $total = $qtySelected;
    }
    
    
    
    return $total;
}

if ($action == "getMajorBuckets") {
    $user  = $_SESSION['username'];
    $stmt1 = $mysqli->prepare("SELECT userID FROM Users WHERE  userName  = ?");
    $stmt1->bind_param('s', $user);
    $stmt1->execute();
    $stmt1->bind_result($output1);
    $userID = array();
    while ($stmt1->fetch()) {
        array_push($userID, $output1);
        
    }
    $stmt = $mysqli->prepare("SELECT description, allRequired FROM MajorBucket WHERE majorID in (SELECT majorID FROM StudentMajor WHERE userID = '" . $userID[0] . "')  and parentID IS NULL");
    
    $stmt->execute();
    $stmt->bind_result($desc, $allReq);
    $output = array();
    
    while ($stmt->fetch()) {
        if ($allReq == 1) {
            $allR = "YES";
        } else
            $allR = "NO";
        array_push($output, array(
            '+',
            $desc,
            $allR
            
        ));
    }
    echo json_encode($output);
    
}


if ($action == "getMajorBucketsCourse") {
    
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt   = $mysqli->prepare("SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade FROM CourseInfo INNER JOIN StudentCourse ON  CourseInfo.courseInfoID = StudentCourse.courseInfoID AND StudentCourse.courseInfoID in (Select  courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description =  '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))AND userID = '" . $userID . "' AND NOT StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND') 
UNION
 Select CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade  From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND  majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')) AND NOT StudentCourse.grade in (SELECT grade FROM StudentCourse WHERE grade = 'ND')AND  StudentCourse.courseInfoID = CourseInfo.courseInfoID");
    
    $stmt->execute();
    $stmt->bind_result($desc, $cred, $grade);
    $output = array();
    
    while ($stmt->fetch()) {
        array_push($output, array(
            $desc,
            $cred,
            $grade
        ));
        
    }
    echo json_encode($output);
    
}
if ($action == "getMajorBucketsChildBuckets") {
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    // get child buckets of $bucket
    $stmt = $mysqli->prepare("SELECT description, allRequired FROM MajorBucket WHERE  majorID in (SELECT majorID FROM StudentMajor WHERE userID = '" . $userID . "') and parentID in (Select bucketID From MajorBucket Where description = '" . $bucket . "' AND majorID in (select majorID FROM StudentMajor WHERE userID = '" . $userID . "'))");
    $stmt->execute();
    $stmt->bind_result($desc, $allReq);
    $output = array();
    while ($stmt->fetch()) {
        $parent = "NOT SET";
        if ($allReq == 1) {
            $allR = "YES";
        } else
            $allR = "NO";
        array_push($output, array(
            '+',
            $desc,
            $allR
            
        ));
        
    }
    echo json_encode($output);
    
    
    
}
if ($action == "getMajorBucketsNeeded") {
    $user  = $_SESSION['username'];
    // Get user ID
    $stmt1 = $mysqli->prepare("SELECT userID FROM Users WHERE  userName  = ?");
    $stmt1->bind_param('s', $user);
    $stmt1->execute();
    $stmt1->bind_result($output1);
    $userID = array();
    while ($stmt1->fetch()) {
        
        array_push($userID, $output1);
        
        
    }
    
    //get all buckets whose parentID is null
    $stmt = $mysqli->prepare("SELECT description, allRequired FROM MajorBucket WHERE  majorID in (SELECT majorID FROM StudentMajor WHERE userID = '" . $userID[0] . "') and parentID IS NULL");
    
    $stmt->execute();
    $stmt->bind_result($desc, $allReq);
    $output = array();
    
    while ($stmt->fetch()) {
        $parent = "NOT SET";
        
        
        if ($allReq == 1) {
            $allR = "YES";
        } else
            $allR = "NO";
        array_push($output, array(
            '+',
            $desc,
            $allR
        ));
        
    }
    echo json_encode($output);
    
}
if ($action == "findChildBuckets") {
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    if ($query = $mysqli->prepare("SELECT description, allRequired, parentID FROM MajorBucket WHERE  majorID in (SELECT majorID FROM StudentMajor WHERE userID = '" . $userID . "') and parentID in (select bucketID FROM MajorBucket where description = '" . $bucket . "')")) {
        $query->execute();
        $query->store_result();
        $query->bind_result($output10, $d, $e);
        $query->fetch();
        
        if ($query->num_rows() > 0) {
            //$parent = "YES";
            $result = array(
                'success' => true
            );
        } else {
            // $parent = "NO";
            $result = array(
                'success' => false
            );
        }
    }
    echo json_encode($result);
}

if ($action == "getMajorBucketsCourseNeeded") {
    
    if (isset($_POST['bucket'])) {
        $bucket = $_POST['bucket'];
    } else {
        $bucket = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt   = $mysqli->prepare("
SELECT DISTINCT CourseInfo.courseID, CourseInfo.credits, StudentCourse.weight, StudentCourse.relevance, StudentCourse.courseInfoID, StudentCourse.selected FROM StudentCourse INNER JOIN CourseInfo ON CourseInfo.courseInfoID in (Select courseInfoID From MajorBucketRequiredCourses where bucketID in (Select bucketID FROM MajorBucket where description = '" . $bucket . "' AND  majorID in (Select majorID From StudentMajor where userID = '" . $userID . "')))AND CourseInfo.courseInfoID in (SELECT courseInfoID From StudentCourse Where userID = '" . $userID . "' AND grade = 'ND' )  AND StudentCourse.courseInfoID = CourseInfo.courseInfoID And StudentCourse.userID = '" . $userID . "'

UNION
 Select CourseInfo.courseID, CourseInfo.credits, StudentCourse.weight, StudentCourse.relevance, StudentCourse.courseInfoID, StudentCourse.selected From StudentCourse inner join CourseInfo ON StudentCourse.referenceBucket in (Select bucketID from MajorBucket Where description =  '" . $bucket . "' AND majorID in (Select majorID From StudentMajor where userID = '" . $userID . "'))AND StudentCourse.grade = 'ND' AND StudentCourse.courseInfoID = CourseInfo.courseInfoID 
");
    
    $stmt->execute();
    $stmt->bind_result($desc, $cred, $weight, $relev, $cIID, $select);
    $output = array();
    
    // '<input id = "'.$desc.'check" type="checkbox" name="myCheckbox" disabled="disabled" /> '
    while ($stmt->fetch()) {
        if ($select == 1) {
            $box = '<input id = "' . $desc . 'check" type="checkbox" name="myCheckbox" checked disabled="disabled" /> <a style ="color:blue;">toggle</a>';
            array_push($output, array(
                $desc,
                $cred,
                $weight,
                $relev,
                $box
            ));
        } else {
            array_push($output, array(
                $desc,
                $cred,
                $weight,
                $relev,
                '<input id = "' . $desc . 'check" type="checkbox" name="myCheckbox" /> <a  style ="color:blue;">toggle</a>'
            ));
        }
    }
    echo json_encode($output);
    
}

if ($action == "changeSelected") {
    
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    if (isset($_POST['state'])) {
        $state = $_POST['state'];
    } else {
        $state = "";
    }
    
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    
    
    $sql = "UPDATE StudentCourse SET selected ='" . $state . "' WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo WHERE courseID =  '" . $courseID . "') AND userID = '" . $userID . "'";
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Item could not be deleted'
        );
    }
    echo json_encode($result);
}

if ($action == "courseTakenElectives") {
    $user = $_SESSION['username'];
    $stmt = $mysqli->prepare("SELECT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE  StudentCourse.userID in (SELECT userID FROM Users WHERE userName ='" . $user . "' ) AND CourseInfo.courseInfoID in (SELECT courseInfoID FROM MajorBucketRequiredCourses WHERE bucketID = '2')");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $stmt->bind_result($suser, $CID, $credit, $grd);
    $output = array();
    while ($stmt->fetch()) {
        array_push($output, array(
            $CID,
            $credit,
            $grd
        ));
    }
    echo json_encode($output);
    
}
if ($action == "addCourse") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    if (isset($_POST['grade'])) {
        $grade = $_POST['grade'];
    } else {
        $grade = "";
    }
    if (isset($_POST['credits'])) {
        $credits = $_POST['credits'];
    } else {
        $credits = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt2  = $mysqli->prepare("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = '" . $courseID . "'");
    
    $stmt2->execute();
    
    $stmt2->bind_result($output2);
    $course = array();
    while ($stmt2->fetch()) {
        array_push($course, $output2);
        
    }
    $sql = "UPDATE StudentCourse SET grade ='IP' WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo WHERE courseID =  '" . $courseID . "') AND userID = '" . $userID . "' ";
    
    
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Item could not be deleted'
        );
    }
    echo json_encode($result);
}

if ($action == "courseTaken1") {
    $user = $_SESSION['username'];
    $stmt = $mysqli->prepare("SELECT CourseInfo.courseID, CourseInfo.credits, StudentCourse.grade FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE  StudentCourse.userID in (SELECT userID FROM Users WHERE userName = ? ) AND CourseInfo.courseInfoID in (SELECT courseInfoID FROM MajorBucketRequiredCourses WHERE bucketID = '1')");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $stmt->bind_result($CID, $credit, $grd);
    $output = array();
    while ($stmt->fetch()) {
        array_push($output, array(
            $CID,
            $credit,
            $grd
        ));
    }
    echo json_encode($output);
    
}


if ($action == "modCourse") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    if (isset($_POST['grade'])) {
        $grade = $_POST['grade'];
    } else {
        $grade = "";
    }
    if (isset($_POST['credits'])) {
        $credits = $_POST['credits'];
    } else {
        $credits = "";
    }
    if (isset($_POST['modifiedCourse'])) {
        $modifiedCourse = $_POST['modifiedCourse'];
    } else {
        $modifiedCourse = "";
    }
    if (isset($_POST['modifiedGrade'])) {
        $modifiedGrade = $_POST['modifiedGrade'];
    } else {
        $modifiedGrade = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    $sql = "UPDATE StudentCourse SET grade ='" . $modifiedGrade . "' WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo WHERE courseID =  '" . $courseID . "') AND userID = '" . $userID . "' ";
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Grade could not be modified'
        );
    }
    echo json_encode($result);
}
if ($action == "modWeight") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    if (isset($_POST['modifiedWeight'])) {
        $modifiedWeight = $_POST['modifiedWeight'];
    } else {
        $modifiedWeight = "";
    }
    if (isset($_POST['modifiedRelevance'])) {
        $modifiedRelevance = $_POST['modifiedRelevance'];
    } else {
        $modifiedRelevance = "";
    }
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt2  = $mysqli->prepare("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = '" . $courseID . "'");
    
    $stmt2->execute();
    
    $stmt2->bind_result($output2);
    $course = array();
    while ($stmt2->fetch()) {
        array_push($course, $output2);
        
    }
    $sql = "UPDATE StudentCourse SET weight ='" . $modifiedWeight . "', relevance = '" . $modifiedRelevance . "' WHERE courseInfoID = '" . $course[0] . "' AND userID ='" . $userID . "'  ";
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Item could not be deleted'
        );
    }
    echo json_encode($result);
}
if ($action == "deleteItem") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    if (isset($_POST['grade'])) {
        $grade = $_POST['grade'];
    } else {
        $grade = "";
    }
    if (isset($_POST['credits'])) {
        $credits = $_POST['credits'];
    } else {
        $credits = "";
    }
    
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    $sql = "UPDATE StudentCourse SET grade ='ND' WHERE courseInfoID in (SELECT courseInfoID FROM CourseInfo WHERE courseID =  '" . $courseID . "') AND userID = '" . $userID . "' ";
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Course could not be deleted'
        );
    }
    echo json_encode($result);
}
class major
{
    private $username;
    private $majorID;
    private $d_month;
    private $d_year;
    public function __construct($un, $maj, $dm, $dy)
    {
        $this->username = $un;
        $this->majorID  = $maj;
        $this->d_month  = $dm;
        $this->d_year   = $dy;
    }
    public function prepare_conjunction()
    {
        $date = $this->d_year . $this->d_month;
        return "(major_id = " . $this->majorID . " AND date_start <= " . $date . " AND date_end >= " . $date . ")";
    }
}
if ($action == "courseNeeded") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    
    
    
    $stmt2 = $mysqli->prepare("SELECT courseID, credits
                              FROM CourseInfo
                              WHERE courseInfoID in (SELECT courseInfoID
                                                 FROM MajorBucketRequiredCourses

                                    WHERE bucketID = '1'  AND 
                                    NOT courseInfoID in (SELECT courseInfoID
                                                     FROM   StudentCourse
                                                     WHERE  userID = '" . $userID . "'))");
    $stmt2->bind_param('ss', $user, $user);
    $stmt2->execute();
    $stmt2->bind_result($CID, $credit);
    $output = array();
    while ($stmt2->fetch()) {
        array_push($output, array(
            $CID,
            $credit,
            $credit,
            '3'
        ));
    }
    echo json_encode($output);
    
}

if ($action == "courseNeededElectives") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['username'];
    
    $stmt = $mysqli->prepare("SELECT courseID, credits
                              FROM CourseInfo
                              WHERE courseInfoID in (SELECT courseInfoID
                                                 FROM MajorBucketRequiredCourses

                                    WHERE bucketID = '2'  AND 
                                    NOT courseInfoID in (SELECT courseInfoID
                                                     FROM   StudentCourse
                                                     WHERE  userID ='" . $userID . "'))");
    
    $stmt->execute();
    $stmt->bind_result($CID, $credit);
    $output = array();
    while ($stmt->fetch()) {
        array_push($output, array(
            $CID,
            $credit,
            $credit,
            '3'
        ));
    }
    echo json_encode($output);
    
}


if ($action == "deleteCourseNeeded") {
    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }
    $user = $_SESSION['username'];
    $stmt = $mysqli->prepare("SELECT userID FROM Users WHERE  userName  = ?");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $stmt->bind_result($output1);
    $userID = array();
    while ($stmt1->fetch()) {
        array_push($userID, $output1);
        
    }
    $stmt2 = $mysqli->prepare("SELECT courseInfoID FROM CourseInfo WHERE  courseID  = '" . $courseID . "'");
    
    $stmt2->execute();
    
    $stmt2->bind_result($output2);
    $course = array();
    while ($stmt2->fetch()) {
        array_push($course, $output2);
        
    }
    $sql = "DELETE FROM StudentCourse WHERE userID = '" . $userID[0] . "' AND courseInfoID = '" . $course[0] . "' ";
    
    if ($mysqli->query($sql) === TRUE) {
        $result = array(
            'success' => true
        );
    } else {
        $result = array(
            'success' => false,
            'message' => 'Item could not be deleted'
        );
    }
    echo json_encode($result);
}
if ($action == "getUser") {
    
    $user      = $_SESSION['username'];
    $password  = 'pword';
    $list      = array(
        'name' => $user,
        'password' => $password
    );
    $listArray = json_encode($list);
    echo $listArray;
}
if ($action == "getGradProgram") {
    $user  = $_SESSION['username'];
    $stmt5 = $mysqli->prepare("SELECT graduateProgram, requiredGPA FROM GraduatePrograms ");
    $stmt5->execute();
    $stmt5->bind_result($prg, $gpa);
    $output4 = array();
    while ($stmt5->fetch()) {
        array_push($output4, array(
            $prg,
            $gpa
        ));
    }
    echo json_encode($output4);
    
}

if ($action == "getGPA") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt5  = $mysqli->prepare("SELECT StudentCourse.grade, CourseInfo.credits FROM StudentCourse inner join  CourseInfo on StudentCourse.courseInfoID = CourseInfo.courseInfoID  AND StudentCourse.grade Not in (Select grade from StudentCourse WHERE grade = 'ND' or grade = 'IP') AND StudentCourse.userID in (select userID From Users Where userID = '" . $userID . "')");
    $stmt5->execute();
    $stmt5->bind_result($grades, $credits);
    $output4 = array();
    while ($stmt5->fetch()) {
        array_push($output4, array(
            $grades,
            $credits
        ));
    }
    echo json_encode($output4);
    
}

if ($action == "GetGraphData") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt5  = $mysqli->prepare("SELECT StudentCourse.grade, CourseInfo.credits FROM `StudentCourse` inner join 
CourseInfo on StudentCourse.grade not in (select grade from StudentCourse Where 
grade = 'ND' or grade ='IP'or grade = 'TR') AND StudentCourse.semester = 'fall' and 
StudentCourse.year <='2015' and StudentCourse.userID = '" . $userID . "' And CourseInfo.courseInfoID = StudentCourse.courseInfoID");
    $stmt5->execute();
    $stmt5->bind_result($currentGrade, $currentCredits);
    $output4 = array();
    while ($stmt5->fetch()) {
        array_push($output4, array(
            $currentGrade,
            $currentCredits
        ));
    }
    echo json_encode($output4);
    
}

//Query DB for creditsTaken and creditsLeft
if ($action == "TakenAndRemaining") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt   = $mysqli->prepare("Select  f.creditsTaken, e.RemainingCredits From ((Select sum(CourseInfo.credits) as RemainingCredits From (Select a.courseInfoID From (SELECT courseInfoID FROM `MajorBucketRequiredCourses` Where bucketID in (Select  bucketID From MajorBucket Where majorID in (Select majorID From StudentMajor Where userID = '" . $userID . "') AND allRequired = '1'))a inner join StudentCourse on a.courseInfoID = StudentCourse.courseInfoID AND  StudentCourse.userID = '" . $userID . "' AND StudentCourse.grade = 'ND'

UNION

Select b.courseInfoID From (SELECT courseInfoID FROM `MajorBucketRequiredCourses` Where bucketID in (Select  bucketID From MajorBucket Where majorID  in (Select majorID From StudentMajor Where userID = '" . $userID . "') AND allRequired = '0'))b inner join StudentCourse on b.courseInfoID = StudentCourse.courseInfoID AND  StudentCourse.userID = '" . $userID . "' AND StudentCourse.grade = 'ND' and StudentCourse.selected = '1'

)c inner join CourseInfo on CourseInfo.courseInfoID = c.courseInfoID)e
JOIN
(Select Sum(CourseInfo.credits) as creditsTaken from CourseInfo inner join StudentCourse on StudentCourse.courseInfoID = CourseInfo.courseInfoID AND StudentCourse.grade not in (Select grade from StudentCourse where grade = 'ND') AND StudentCourse.userID ='" . $userID . "')f)");
    $stmt->execute();
    $stmt->bind_result($creditsTaken, $creditsLeft);
    $output = array();
    while ($stmt->fetch()) {
        array_push($output, array(
            $creditsTaken,
            $creditsLeft
        ));
    }
    echo json_encode($output);
}

//Query DB for grades and course credits
if ($action == "GradesAndCredits"){
    $user = $_SESSION['user'];
    $userID = $_SESSION['userID'];

    $log = fopen("phplog.txt", "w");

    if ($stmt = $mysqli->prepare("SELECT StudentCourse.grade, CourseInfo.credits FROM CourseInfo INNER JOIN StudentCourse
             ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE StudentCourse.userID = ?
             AND NOT StudentCourse.grade = 'IP' AND NOT StudentCourse.grade = 'ND' AND NOT StudentCourse.grade = 'DR'
             AND NOT StudentCourse.grade = 'TR'")){
        $stmt->bind_param('s', $userID);
        $stmt->execute();
        $stmt->bind_result($courseGrade, $courseCredits);
        $gradesAndCredits = array();

        while ($stmt->fetch()){
            fwrite($log, "$courseGrade : $courseCredits\n");
            array_push($gradesAndCredits, array(
                $courseGrade,
                $courseCredits
            ));
        }
    }
    else{
        fwrite($log, "S:$stmt->error \n M:$mysqli->error");
    }

    fwrite($log, "completed successfully\n");
    echo json_encode($gradesAndCredits);
}

//Query for Course Names and IDs and Credits In Progress
if ($action == "CurrentCourses") {
    $user = $_SESSION['user'];
    $userID = $_SESSION['userID'];

    $log = fopen("phplog.txt", "a");

    if ($stmt = $mysqli->prepare("SELECT CourseInfo.courseID, CourseInfo.courseName, CourseInfo.credits, StudentCourse.weight,StudentCourse.relevance FROM StudentCourse INNER JOIN CourseInfo ON StudentCourse.courseInfoID = CourseInfo.courseInfoID WHERE grade = 'IP' AND userID = ? ")) {
        $stmt->bind_param('s', $userID);
        $stmt->execute();
        $stmt->bind_result($courseID, $courseName, $credits, $courseWeight, $courseRelevance);
        $courseInfo = array();

        while ($stmt->fetch()){
            fwrite($log, "$courseID $courseName\n");
            array_push($courseInfo, array(
                $courseID,
                $courseName,
                $credits,
                $courseWeight,
                $courseRelevance
            ));
        }
    }
    else{
        fwrite($log, "S:$stmt->error \n M:$mysqli->error");
    }

    fwrite($log, "completed successfully\n");
    echo json_encode($courseInfo);
}
// in theory this should UPDATE weight and relevance in DB
if ($action == "ModifyWeightAndRelevance") {
    $user = $_SESSION['user'];
    $userID = $_SESSION['userID'];

    if (isset($_POST['courseID'])) {
        $courseID = $_POST['courseID'];
    } else {
        $courseID = "";
    }

    if (isset($_POST['modifiedRelevance'])) {
        $modifiedRelevance = $_POST['modifiedRelevance'];
    } else {
        $modifiedRelevance = "";
    }

    if (isset($_POST['modifiedWeight'])) {
        $modifiedWeight = $_POST['modifiedWeight'];
    } else {
        $modifiedWeight = "";
    }

    if ($stmt = $mysqli->prepare("UPDATE StudentCourse SET relevance = ?, weight = ? WHERE courseInfoID = (SELECT courseInfoID FROM CourseInfo WHERE courseID = ?) AND userID = ?")){
        $stmt->bind_param('ssss', $modifiedRelevance, $modifiedWeight, $courseID, $userID);
        $stmt->execute();
    }

    if ($mysqli->query($sql) === TRUE) {

       $result = array(
          'success' => true
       );
    } else {
       $result = array(
          'success' => false,
          'message' => 'Weight and Relevance could not be updated'
       );
    }

    echo json_encode($result);
}


if ($action == "getCurrentProgram") {
    $user   = $_SESSION['username'];
    $userID = $_SESSION['userID'];
    $stmt5  = $mysqli->prepare("SELECT majorName FROM Major Where majorID in (Select majorID From StudentMajor Where userID ='" . $userID . "') ");
    $stmt5->execute();
    $stmt5->bind_result($currentProg);
    $output4 = array();
    while ($stmt5->fetch()) {
        array_push($output4, array(
            $currentProg
        ));
    }
    echo json_encode($output4);
    
}


if ($action == "editStudent") {
	  $userID = $_SESSION['userID'];
		$type = $_SESSION['type'];
/*    $stmt2 = $mysqli->prepare("SELECT type FROM Users WHERE userID = '".$userID."' ");
    $stmt2->execute();
    $stmt2->bind_result($userType);
    $userTypeArray = array();
    while ($stmt2->fetch()) {
        array_push($userTypeArray, array(
            $userType
           
        ));
    }
*/
	 if ($type == 1){
    $user = $_SESSION['username'];
	  $conn   = new mysqli("localhost", "root", "sqliscool", "GPA_Tracker");
    $stmt = $conn->prepare("SELECT userName, lastName, firstName, email FROM Users WHERE type = '0' ");
    $stmt->execute();
    $stmt->bind_result($suser, $LN, $FN, $mail);
    $output = array();
    while ($stmt->fetch()) {
        array_push($output, array(
            $suser,
            $LN,
            $FN,
            $mail
        ));
    }
    echo json_encode($output);
    }
else
{
 $list      = array(
        'message' => "false",
        'why' => "You do not have permission to access this page."
    );
    $listArray = json_encode($list);
    echo $listArray;
}
}
?>

