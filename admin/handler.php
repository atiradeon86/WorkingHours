<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Include database configuration file  
require_once '../db-connect.php'; 

if (!empty($_GET['cmd']) && $_GET['cmd'] == 'searchwork') {
 file_put_contents('debugsearch.txt', print_r($_GET, true));

 
 $startdate = $_GET['start'];

 /*
 $start_year =  substr($startdate, 0,4);
 $start_month =  substr($startdate, 5,2);
 $starT_day =  substr($startdate,8 ,2);
 */

 $enddate = $_GET['end'];

 /*
 $end_year =  substr($enddate, 0,4);
 $end_month =  substr($enddate, 5,2);
 $end_day =  substr($enddate,8 ,2);
 */

$sql = "SELECT FORMAT (start_date, 'yyyy-MM-dd') as Workday, 
FORMAT(cast(StartTime as time), N'hh\:mm') AS StartTime, 
FORMAT(cast(EndTime as time), N'hh\:mm') AS EndTime,
ROUND ( CAST(DATEDIFF (second,CAST(StartTime AS DATETIME),CAST(EndTime AS DATETIME)) AS FLOAT)/3600,2) AS WorkingHours,
work_reason from dbo.WorkingHours WHERE Start_date BETWEEN '$startdate' AND '$enddate' ";

$r = sqlsrv_query( $conn, $sql);



while( $row = sqlsrv_fetch_array( $r, SQLSRV_FETCH_ASSOC )) {
    $Reault[]= $row;
}

#file_put_contents('debug.txt', print_r($Reault, true)); 

 echo json_encode($Reault);


}


?>