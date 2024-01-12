<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
  <head>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    
    <script type="text/javascript">

    function dateConvert(date) {

    var mssqlDateTime = date;
    var dateObject = new Date(mssqlDateTime);

    var year = dateObject.getFullYear();
    var month = (dateObject.getMonth() + 1).toString().padStart(2, '0');
    var day = dateObject.getDate().toString().padStart(2, '0');
    var hours = dateObject.getHours().toString().padStart(2, '0');
    var minutes = dateObject.getMinutes().toString().padStart(2, '0');
    var seconds = dateObject.getSeconds().toString().padStart(2, '0');
    var formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`; 
    return  formattedDateTime
   
}

$( document ).ready(function() {

$('body').on( 'submit', '#SaveEditedEvent', function(e) {

$.ajax({
                type: "POST",
                url: "functions.php?cmd=SaveEditedEvent",
                data: $(this).serialize(),
                success : function(data) {
                    $('#AddNewWork').modal('hide');                                
                    
                },

            });
        });
});
    
    function getAllEvent()
    {
        var events = new Array()
        $.ajax({
            type: "POST",
            url: "./functions.php?cmd=listworks",
            dataType : "json",
            success: function(data) {
                var result = data;
                
            $.each(result, function(i, item) {
                start = result[i].start_date.date;
                end = result[i].end_date.date;
                startConverted= dateConvert(start);
                endConverted= dateConvert(end);
                events.push({
                
                    event_id : result[i].id,
                    title : result[i].event_name,
                    start: startConverted,
                    end: endConverted,
                    color: result[i].color,
                    link: result[i].link,
                    extendedProps: {
                        StartTime: result[i].StartTime,
                        EndTime: result[i].EndTime,
                        Id: result[i].id,
                        Reason: result[i].work_reason,
                        Color: result[i].color,
                    },
                });
            });
            console.log(events);
            
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    height: 630,
                    eventClick: function(info) {

                       var hoursinner = document.getElementById('hs');

                       var eventId = info.event.extendedProps.Id;
                       var start = info.event.extendedProps.StartTime['date'];
                      
                       var startsplit = start.split(" ");
                      
                       startsplitfinal = startsplit[1].split(".");
                       
                      
                       var end = info.event.extendedProps.EndTime['date'];
                       var endsplit = end.split(" ");
                       endsplitfinal = endsplit[1].split(".");
                      
                    
                        if (hoursinner != null) {
                            hoursinner.remove();
                        }
                        var reason2 = info.event.extendedProps.Reason;
                        var showreason="";
                        if ((reason2 != "")) {
                            showreason= " (" + reason2 + ")";
                        }
                         else {
                            showreason="";
                         }
                         var color = info.event.extendedProps.Color;
                         //console.log(color);
                        var hours = document.getElementById('hours');
                        var hoursdata = "<div id='hs'><i class='bi bi-clock-fill' style='margin-right:30px;'></i>" + startsplitfinal[0].substring(0, 5) +  "-"+  endsplitfinal[0].substring(0, 5); + "<span id='reason' style='color:" + color + "!important;'>"  + showreason + "</span>" + 
                        "<form action='' method='post' id='EditEvent'><input type='submit' class='btn btn-primary' id='edit' class=" + eventId +" value='Edit'><div id='edit-id' style='display:none;'>" + eventId + "</div></form></div>";
                        hours.innerHTML += hoursdata
                    
                },
                headerToolbar: {
                    start: 'title',
                    center: '',
                    end: 'prev,next,today',
                },
                selectable: true,
                editable: false,
                firstDay: 1,
                displayEventTime: false,
                locale: 'de',
                events: events,
                select: function(datetime){
                    var ActualDay = moment(datetime.start).format('YYYY-MM-DD');
                    $('#start_date').val(ActualDay);
                    $('#end_date').val(ActualDay);
                   
                    $('#AddNewWork').modal('show');
                },
                });
                calendar.render();   
        }
        });
    }
    getAllEvent();

    $(document).ready(function(){



       

    $('body').on( 'submit', '#SubmitEvent', function(e) {
                   
                        $.ajax({
                            type: "POST",
                            url: "functions.php?cmd=addwork",
                            data: $(this).serialize(),
                            success : function(data) {
                                $('#AddNewWork').modal('hide');                                
                                getAllEvent();
                            },

                        });
                    });

                    $('body').on( 'submit', '#EditEvent', function(e) {
                    e.preventDefault();
                    var id = $('#edit-id').text();
                    var AjaxEditUrl = "functions.php?cmd=edit&id=" + id;
                    var EditData = new Array();
                    $.ajax({
                            type: "POST",
                            url: AjaxEditUrl,
                            dataType : "json",
                            success : function(data) {
                                var result = data;
                                $.each(result, function(i, item) {
                                    EditData.push({

                                        event_id : result[i].id,
                                        title : result[i].event_name,
                                        start: result[i].start_date,
                                        end: result[i].end_date,
                                        color: result[i].color,
                                        link: result[i].link,
                                        work_reason : result[i].work_reason, 
                                        StartTime: result[i].StartTime,
                                        EndTime: result[i].EndTime
                                    ,
                                    });
                                 });
                                 //console.log(EditData[0].work_reason);
                                 var title = (EditData[0].title);
                                 var event_id = (EditData[0].event_id);
                                 var color = (EditData[0].color);
                                 var reason = (EditData[0].work_reason);
                                 console.log(reason);
                                 var start = (EditData[0].start.date);
                                
                                 var end = (EditData[0].end.date);
                                 var link  = (EditData[0].link);
                                 var StartTime = (EditData[0].StartTime.date);
                              
                                 var EndTime = (EditData[0].EndTime.date);
                                 EndtimeSplit = EndTime.split(" ");
                                 EndTimeSplitfinal = EndtimeSplit[1].split(".");
                                //console.log(EndTimeSplitfinal)

                                 var startSplit = start. split(" ");
                                 var endSplit = end. split(" ");
                                starTtimeSplit = StartTime.split(" ");
                                startTimeSplitfinal = starTtimeSplit[1].split(".");
                                //console.log(startTimeSplitfinal[0])
                                $("#SubmitEvent").prop('id','SaveEditedEvent');
                                //console.log(id);
                                $('input[name="work_id"]').val(id);
                               
                                $('input[name="work_name"]').val(title);
                                 $('input[name="color"]').val(color);
                                 $('input[name="url"]').val(link);
                                 $("#reason option[value='" + reason + "']").prop('selected', true);
                                 $('input[name="start_date"]').val(startSplit[0]);
                                 $('input[name="start_time"]').val(startTimeSplitfinal[0]);
                                 $('input[name="end_time"]').val(EndTimeSplitfinal[0]);
                                 $('input[name="end_date"]').val(endSplit[0]);
                                 $('#AddNewWork').modal('show');  
                            },

                        });
                    });

        })

</script>
<style>


#wrapper {
        display: block;
        position: relative;
        margin-top: 20px;
        
    }
    #calendar {

       margin: 0 auto;
       width: 1200px;
     

    }

    .btn-primary,.btn-secondary {
        font-size: 30px;
    }

    body {
        background: #333741;
        color: white;
    }
    #hours {
        font-size:40px;
        text-align: center;
    }
    .fc-daygrid-day-number, .fc .fc-col-header-cell-cushion {
        font-size:18px;
        text-align: center;
        margin:0 auto 0 auto;
    }
    .fc .fc-col-header-cell-cushion {
        padding:4px 4px;
    }
    .fc-h-event .fc-event-main, .fc-daygrid-event-harness {
        height: 50px;
        color: #1a252f;
        background: #1a252f;
        display: block;
    }
  
    .fc .fc-highlight {
        color: white;
        background: #1a252f;
        
    }
    .fc-day-today{
        background: #364552!Important;
    }
    .fc-theme-standard th {
        background: #1a252f;
    }
    .fc-icon {
        width: 4em;
        background: #1a252f;
        border-color: #1a252f;
    }
    .fc .fc-button-primary,.fc .fc-button-primary:disabled {
        background: #1a252f;
    }
    .fc .fc-button-primary:disabled {
        font-size: 18px;
    }
    .modal-content {
        background: #1a252f;
        border: red;
    }
    .modal-header {
    border-bottom: 0 none;
    }
    .form-control  {
        height: 50px;
    }
    select.form-control {
        height: 50px!IMportant; 

        display: block;
    }

    .modal-footer {
        margin-top: 20px!Important;
    }

    .fc-direction-ltr .fc-daygrid-event.fc-event-end {
        height: 50px;
        color: #1a252f;
    }

    .fc-daygrid-event-dot {
        color: #1a252f;
    }

    #logo {
        background-image: url(./img/trogroup-logo.png); 
        width: 300px; height: 110px;
        background-repeat: no-repeat;
        margin: 0 auto;
    }
    .fc-toolbar-title {
        font-size: 18px;
    }

@media only screen and (max-width: 1024px) {

        
#wrapper {
        display: block;
        position: relative;
        margin-top: 0px!important;
    }
    #calendar {
      
       width: 350px;
     

    }

    .btn-primary,.btn-secondary {
        font-size: 24px;
    }

    body {
        background: #333741;
        color: white;
    }
    #hours {
        font-size:24px;
        text-align: center;
    }
    .fc-daygrid-day-number, .fc .fc-col-header-cell-cushion {
        font-size:18px;
        text-align: center;
        margin:0 auto 0 auto;
    }
    .fc .fc-col-header-cell-cushion {
        padding:4px 4px;
    }
    .fc-h-event .fc-event-main, .fc-daygrid-event-harness {
     
        color: #1a252f;
        background: #1a252f;
        display: block;
    }
  
    .fc .fc-highlight {
        color: white;
        background: #1a252f;
        
    }
    .fc-day-today{
        background: #364552!Important;
    }
    .fc-theme-standard th {
        background: #1a252f;
    }
    .fc-icon {
        width: 2em;
        background: #1a252f;
        border-color: #1a252f;
    }
    .fc .fc-button-primary,.fc .fc-button-primary:disabled {
        background: #1a252f;
    }
    .fc .fc-button-primary:disabled {
        font-size: 18px;
    }
    .modal-content {
        background: #1a252f;
        border: red;
    }
    .modal-header {
    border-bottom: 0 none;
    }
    .form-control  {
       
    }
    select.form-control {
        height: 50px!IMportant; 

        display: block;
    }

    .modal-footer {
        margin-top: 20px!Important;
    }

    .fc-direction-ltr .fc-daygrid-event.fc-event-end {
     
        color: #1a252f;
    }

    .fc-daygrid-event-dot {
        color: #1a252f;
    }
    #logo { display: none; }
    }
 

</style>
<meta content="width=device-width, initial-scale=1" name="viewport" />
</head>
  <body>
    <div id="wrapper">
        <div id ="logo"></div>
        <div id ="calendar"></div>
        <div id="hours"></div>
    </div>
    <div class="modal fade" id="AddNewWork" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="AddNewWork" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Work / Event</h5>
                </div>
                <form action="" method="post" id="SubmitEvent">
                    <div class="modal-body">
                       
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                    <label for "work_name">Title</label>
                                    <input type="text" name="work_name" id="work_name" class="form-control clear-form">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                    <div class="form-group">
                                    <label for "reason">Work Reason</label>
                                    <select class="form-control" name="reason" id="reason">
                                        <option value="">Please Select</option>
                                        <option value="HomeOffice">HomeOffice</option>
                                        <option value="Urlaub">Urlaub</option>
                                        <option value="Zeitausgleich">Zeitausgleich</option>
                                    </select>
                                </div>
                            </div>
                            
                        
                                <div class="col-sm-6">
                                    <div class="form-group">
                                    <label for "start_date">Date</label>
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
                            <div class="row">                      
                                <div class="col-sm-6">
                                        <div class="form-group">
                                        <label for "start_time">Start Time</label>
                                        <input type="time" name="start_time"  id="start_time" class="form-control" value="08:00"/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                        <div class="form-group">
                                        <label for "time">End Time</label>
                                        <input type="time" name="end_time" id="end_time" class="form-control" value="17:00">
                                    </div>
                                </div>
                            </div>
                            <div class="row">                      
                                <div class="col-sm-6">
                                        <div class="form-group">
                                        <label for "Color">Color</label>
                                        <input type="color" name="color" value="#007bff" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                        <div class="form-group">
                                        <label for "time">Url</label>
                                        <input type="text" name="url" id="url" class="form-control">
                                        <input type="hidden" name ="work_id" id="work_id">
                                    </div>
                                </div>
                            </div>
                    <div class="modal-footer"> 
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>