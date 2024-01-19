<html>
<head>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script type="text/javascript">
$( document ).ready(function() {

$('body').on( 'submit', '#SearchEvent', function(e) {
e.preventDefault();
var SearchData = new Array();
var start= $('input[name="start_date"]').val();
var end= $('input[name="end_date"]').val();

$.ajax({
    
                type: "POST",
                url: "handler.php?cmd=searchwork&start=" + start + "&end=" +end,
                dataType : "json",
                success : function(data) {
                    var result = data;      
                    var ResultDiv = $("#ContentDiv");
                    var SumDiv = $("#Sum");
                    SumDiv.empty();
                    ResultDiv.empty();
                    $.each(result, function(i, item) {
                                    SearchData.push({
                                        WorkDay: result[i].Workday,
                                        StartTime: result[i].StartTime,
                                        EndTime: result[i].EndTime,
                                        WorkingHours: result[i].WorkingHours,
                                        work_reason: result[i].work_reason,
                                    });
                                 });    
                                 console.log(SearchData);      
                                 var workreason ="";
                                 var totalWorkingHours = 0;
                                 $.each(SearchData, function(index, workObject){
                                    totalWorkingHours += workObject.WorkingHours;
                                    
                                  if ( workObject.work_reason != "") {
                                    workreason = workObject.work_reason  + "</p>";
                                  } 
                                    else{
                                        workreason="";
                                    }
                                  
                                    var content = "<p><span class='WorkDay'>" + workObject.WorkDay + "</span><br>"
                                                  + workObject.StartTime +"-" +  workObject.EndTime + "<br>"+
                                                    "<span class='WorkingHours'>" +workObject.WorkingHours +  " Hour</span><br>"+
                                                workreason + "</p>";
                                                ResultDiv.append(content);
                                                $("#send").removeAttr("style").show();
                                });     
                               //console.log(totalWorkingHours);   
                                SumDiv.append("Total Hours: " + totalWorkingHours);   
                    
                },

            });
        });
});
    
</script>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<style>
body {
    background: #333741;
    color: white;
}
.WorkDay {
    font-size: 18px;
    color: #007bff;
}
.ptitle {
    color: #007bff;
}

.WorkingHours {
    color: green;
}
#sum {
    display: block;
    position: relative;
    margin-bottom: 20px;
}

</style>
<body>
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

#print_r($resultString);

?>
<div id="search" style="width: 700px; margin: 0 auto; text-align:center; position: relative; display:block;" >
    <form action="" method="post" id="SearchEvent">
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for "start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control clear-form">
                    </div>
            </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for "end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control clear-form">
                    </div>
            </div>              
        </div>
        <div class="modal-footer"> 
            <button type="submit" class="btn btn-primary" id="edit">Search</button>
            <button type="submit" style="display:none;" class="btn btn-secondary" id="send">Send E-mail</button>
        </div>
    </form>
</div>
<div id="Sum"></div>
<div id="ContentDiv"></div>
</body>
</html>