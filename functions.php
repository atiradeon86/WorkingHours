<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include database configuration file  
require_once './db-connect.php'; 

if (!empty($_GET['cmd']) && $_GET['cmd'] == 'listworks') {

// Fetch events from database 
$sql = "SELECT id,event_name,color,link,start_date,end_date,StartTime,EndTime FROM dbo.WorkingHours"; 
#$result = $conn->query($sql);  
$result = sqlsrv_query( $conn, $sql);

while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
    $eventArray[]= $row;
}
echo json_encode($eventArray);

}
elseif (!empty($_GET['cmd']) && $_GET['cmd'] == 'addwork') {

    //print_r($_POST);
     $title = $_POST['work_name'];
     $title=".";
     if ($_POST['reason'] =="Please Select") { $reason ="";} else {
        $reason = $_POST['reason'];
    }
     $description ="";
     $start_date = $_POST['start_date'];
     $end_date = $_POST['end_date'];
     $start_time= $_POST['start_time'];
     $end_time= $_POST['end_time'];
     $color = $_POST['color'];
    $url = $_POST['url'];
    
    $sql = "INSERT INTO dbo.Workinghours 
    (event_name,description,link,start_date,end_date,StartTime,EndTime,color,work_reason) 
    VALUES('$title','$description','$url','$start_date','$end_date','$start_time','$end_time','$color','$reason')";
    #$var = array($color,$start_date,$end_date,$start_time,$end_time,$url);
    
    echo $sql;
    echo $end_date;

    $stmt = sqlsrv_query($conn, $sql);
 
    if( $stmt === false ) {
        if( ($errors = sqlsrv_errors() ) != null) {
            foreach( $errors as $error ) {
                echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
                echo "code: ".$error[ 'code']."<br />";
                echo "message: ".$error[ 'message']."<br />";
            }
        }
    }

    $json['status'] = true; 
    echo json_encode($json);
}

?>