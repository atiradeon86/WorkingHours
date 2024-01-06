<!DOCTYPE html>
<html>
  <head>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    
    <script type="text/javascript">
    
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
                events.push({

                    event_id : result[i].id,
                    title : result[i].event_name,
                    start: result[i].start_date,
                    end: result[i].end_date,
                    color: result[i].color,
                    link: result[i].link,
                    extendedProps: {
                        StartTime: result[i].StartTime,
                        EndTime: result[i].EndTime,
                        Id: result[i].id,
                    },
                });
            });
            console.log(events);
            
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    height: 860,
                    eventClick: function(info) {

                        // Split the time to int
                        var starth =  info.event.extendedProps.StartTime
                        var endh = info.event.extendedProps.EndTime
                        
                        var EndtimeParts = endh.split(':');
                        var Endtpart1 = parseInt(EndtimeParts[0], 10);
                        var Endtpart2 = parseInt(EndtimeParts[1], 10);

                        var StarttimeParts = starth.split(':');
                        var Startpart1 = parseInt(StarttimeParts[0], 10);
                        var Startpart2 = parseInt(StarttimeParts[1], 10);
                        

                        //debug
                        //console.log(Startpart1,Startpart2);
                        //console.log(Endtpart1,Endtpart2);
                        
                        var eventId = info.event.extendedProps.Id;
                        console.log(eventId);
                        //Creating time objects

                        StartTime = new Date();
                        StartTime.setHours(Startpart1,Startpart2,0);

                        EndTime = new Date();
                        EndTime.setHours(Endtpart1,Endtpart2,0);

                        var StartMinutesFormatted = ("0" + StartTime.getMinutes()).slice(-2);
                        var StartHoursFormatted = ("0" + StartTime.getHours()).slice(-2);
                        
                        var EndMinutesFormatted = ("0" + EndTime.getMinutes()).slice(-2);
                        var EndHoursFormatted = ("0" + EndTime.getHours()).slice(-2);

                        var hoursinner = document.getElementById('hs');
                        //alert(info.event.title)
                        //alert(info.event.extendedProps.StartTime)
                        if (hoursinner != null) {
                            hoursinner.remove();
                        }
                        var hours = document.getElementById('hours');
                        var hoursdata = "<div id='hs'><br><i class='bi bi-clock-fill' style='margin-right:30px;'></i>" + StartHoursFormatted + ":" + StartMinutesFormatted +  "-"+  EndHoursFormatted + ":" + EndMinutesFormatted + "</div>"
                        hours.innerHTML += hoursdata
                    
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth',
                },
                navlinks: true,
                selectable: true,
                editable: true,
                firstDay: 1,
                locale: 'de',
                events: events,
                select: function(datetime){
                    var ActualDay = moment(datetime.start).format('YYYY-MM-DD');
                    $('#start_date').val(ActualDay);
                    $('#end_date').val(ActualDay);
                    console.log(ActualDay);
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
                    e.preventDefault();
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
        })

</script>
<style>
    #wrapper {
        display: block;
        position: relative;
        margin-top: 100px;
    }
    #calendar {
       margin-top: 90px;

    }
    body {
        background: #333741;
        color: white;
    }
    #hours {
        font-size:40px;
    }
    .fc-daygrid-day-number, .fc .fc-col-header-cell-cushion {
        font-size:40px;
        text-align: center;
        margin:0 auto 0 auto;
    }
    .fc .fc-col-header-cell-cushion {
        padding:4px 4px;
    }
    .fc-h-event .fc-event-main {
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

</style>
</head>
  <body>
    <div id="wrapper">
        <div id ="logo" style="background-image: url(./img/trogroup-logo.png); width: 300px; height: 110px; margin: 0 auto; background-repeat: no-repeat;"></div>
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
                                    <select class="form-control" name="reason">
                                        <option>Please Select</option>
                                        <option>HomeOffice</option>
                                        <option>Urlaub</option>
                                        <option>What street did you grow up on?</option>
                                        <option>Zeitausgleich</option>
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
                                        <label for "Color">Start Time</label>
                                        <input type="color" name="color" value="#1a252f" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                        <div class="form-group">
                                        <label for "time">Url</label>
                                        <input type="text" name="url" id="url" class="form-control">
                                    </div>
                                </div>
                            </div>

                            

                           
                   
                <div class="modal-footer"> 
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>

    </body>
</html>