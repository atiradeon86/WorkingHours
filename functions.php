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
    
    
    // Test
    
   
    $id =  $_POST['work_id'];
    $check_query = "SELECT * FROM dbo.WorkingHours WHERE id='$id' AND start_date ='$start_date' AND end_date='$end_date'";
    $chk = sqlsrv_query($conn, $check_query, array(), array( "Scrollable" => 'static' ));
    $row_count = sqlsrv_num_rows( $chk );
    echo ($row_count);
    echo $check_query;
   if ($row_count =="1") {
  
    while( $row = sqlsrv_fetch_array( $chk, SQLSRV_FETCH_ASSOC )) {
        $EditArray[]= $row;
    }
   
   
    $sql = "UPDATE dbo.WorkingHours SET start_date ='$start_date' WHERE id='$id'";
   }
 
   else {

    $sql = "INSERT INTO dbo.Workinghours 
    (event_name,description,link,start_date,end_date,StartTime,EndTime,color,work_reason) 
    VALUES('$title','$description','$url','$start_date','$end_date','$start_time','$end_time','$color','$reason')";
    #$var = array($color,$start_date,$end_date,$start_time,$end_time,$url);
   }
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
} elseif (!empty($_GET['cmd']) && $_GET['cmd'] == 'edit') {
    $id = $_GET['id'];
    $sql = "SELECT id,event_name,color,link,start_date,end_date,StartTime,EndTime,work_reason FROM dbo.WorkingHours WHERE id=$id";  
    $stmt = sqlsrv_query($conn, $sql);
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC )) {
        $EditArray[]= $row;
    }
    echo json_encode($EditArray);
} elseif (!empty($_GET['cmd']) && $_GET['cmd'] == 'SaveEditedEvent') {
    
    #Debug
    #file_put_contents('debug.txt', print_r($_POST, true));  
    #file_put_contents('debug.txt', $_POST['work_id']);  

    $id =  $_POST['work_id'];
    $title = $_POST['work_name'];
    $reason = $_POST['reason'];
    $description ="";
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $start_time= $_POST['start_time'];
    $end_time= $_POST['end_time'];
    $color = $_POST['color'];
    $url = $_POST['url'];

    #Update Query
    $sql = "UPDATE dbo.WorkingHours SET event_name ='$title', start_date='$start_date', end_date='$end_date', StartTime='$start_time',work_reason='$reason', EndTime='$end_time', color='$color', link ='$url'  WHERE id='$id'";
    
    #SQL Debug
    #file_put_contents('sql.txt', $sql); 

    $result = sqlsrv_query( $conn, $sql);

    $json['status'] = true; 
    echo json_encode($json);
}

?>