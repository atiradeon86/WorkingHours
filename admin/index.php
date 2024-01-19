<?php
require_once '../db-connect.php'; 

$message ="";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sql = "SELECT FORMAT (start_date, 'yyyy-MM-dd') as Workday, 
FORMAT(cast(StartTime as time), N'hh\:mm') AS StartTime, 
FORMAT(cast(EndTime as time), N'hh\:mm') AS EndTime,
ROUND ( CAST(DATEDIFF (second,CAST(StartTime AS DATETIME),CAST(EndTime AS DATETIME)) AS FLOAT)/3600,2) AS WorkingHours,
work_reason
from dbo.WorkingHours
WHERE YEAR(start_date) ='2024' AND MONTH(start_date) ='01'";

#echo $sql;

$result = sqlsrv_query( $conn, $sql);

while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
    $WorkingHours[]= $row;
}


$sum = 0;
foreach ($WorkingHours as $item) {
    // Check if the key exists and if it is a numeric value
    if (isset($item['WorkingHours']) && is_numeric($item['WorkingHours'])) {
        $sum += (float)$item['WorkingHours'];
    }
}


#Change WorkingHours (-30 minutes)
foreach ($WorkingHours as $key => $item) {
    foreach ($item as $subKey => $value) {
        if ($subKey === 'WorkingHours') {
            $WorkingHours[$key][$subKey] = $value - 0.50;
        }
    }
}

$resultString = '';
foreach ($WorkingHours as $item) {
    foreach ($item as $key => $value) {
        $data = $key . ': ' . $value . '<br>';
        $resultString .= $data;
        #echo $key . ': ' . $value . '<br>';
    }
    $resultString .= '<br>';
}

#$message +=  $WorkingHours

#SUM Working Hours

$sumAccounting = 0;
foreach ($WorkingHours as $item) {
    // Check if the key exists and if it is a numeric value
    if (isset($item['WorkingHours']) && is_numeric($item['WorkingHours'])) {
        $sumAccounting += (float)$item['WorkingHours'];
    }
}

$resultString .= '<br>';

$sum = "All Hours (With -30 minute break): $sum<br>";
$suma = "All Hours (With -30 minute break): $sumAccounting";


$resultString .= $sum;
$resultString .= $suma;

print_r($resultString);

?>