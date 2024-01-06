<?php
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

    print_r($_POST);
    $json['status'] = true; 
    echo json_encode($json);
}

?>