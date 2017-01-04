@extends('layouts.master') @section('main')
<link href="{{ URL::asset('css/fullcalendar.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/fullcalendar.print.min.css') }}" rel="stylesheet" media='print'>
<link href="{{ URL::asset('css/scheduler.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/bootstrap-colorselector.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ URL::asset('js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/fullcalendar.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/scheduler.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-datetimepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-colorselector.js') }}"></script>
<script>
  $(document).ready(function() {

    $('#calendar').fullCalendar({
        defaultView: 'agendaDay',
        editable: true,
        selectable: true,
        eventLimit: true, // allow "more" link when too many events
        header: {
            left: 'promptResource prev',
            center: 'title',
            right: 'next'
        },
        customButtons: {
            promptResource: {
                text: '+ สนาม',
                click: function() {
                    //var title = prompt('Room name');
                    // if (title) {
                    //     $('#calendar').fullCalendar(
                    //         'addResource',
                    //         { title: title },
                    //         true // scroll to the new resource?
                    //     );
                    // }
                    $('#modal-add-field').modal('show');
                }
            }
        },
        resourceLabelText: 'Rooms',
        resourceRender: function(resource, cellEls) {
            cellEls.on('click', function() {
                if (confirm('Are you sure you want to delete ' + resource.title + '?')) {
                    $('#calendar').fullCalendar('removeResource', resource);
                }
            });
        },
        lang: 'th',
        timezone: 'Asia/Bangkok',
        axisFormat: 'HH:mm',
        timeFormat: 'HH:mm',
        //// uncomment this line to hide the all-day slot
        //allDaySlot: false,
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        resources: {!! json_encode($resource) !!}, 
        events: [
            { id: '1', resourceId: 'a', start: '2017-01-06', end: '2017-01-08', title: 'event 1', color: '#FF4500'},
            { id: '2', resourceId: 'a', start: '2017-01-07T09:00:00', end: '2017-01-07T14:00:00', title: 'event 2' , color: '#32CD32'},
            { id: '3', resourceId: 'b', start: '2017-01-07T12:00:00', end: '2017-01-08T06:00:00', title: 'event 3' },
            { id: '4', resourceId: 'c', start: '2017-01-07T07:30:00', end: '2017-01-07T09:30:00', title: 'event 4' },
            { id: '5', resourceId: 'd', start: '2017-01-07T10:00:00', end: '2017-01-07T15:00:00', title: 'event 5' }
        ],

        select: function(start, end, jsEvent, view, resource, allDay) {
            console.log(
                'select',
                start.format(),
                end.format(),
                resource ? resource.id : '(no resource)'
            );

            endtime = moment(end).format('HH:mm');
            starttime = moment(start).format('HH:mm');
            day = moment(start).format('dd ll');
            var range = starttime + ' - ' + endtime;
            $('#mpeWorkTask #hddStartTime').val(start);
            $('#mpeWorkTask #hddEndTime').val(end);
            $('#mpeWorkTask #hddAllDay').val(allDay);
            $('#mpeWorkTask #resource').val(resource.title);
            $('#mpeWorkTask #day').val(day);
            $('#mpeWorkTask #time').val(range);
            $('.reserve label').addClass('active');
            $('#mpeWorkTask').modal('show');
        
        },
        dayClick: function(date, jsEvent, view, resource) {
            console.log(
                'dayClick',
                date.format(),
                resource ? resource.id : '(no resource)'
            );
        }
    });

    $('#btnSave').on('click', function(e){
        e.preventDefault();
        doSave();
    });

    $('#btnCancel').on('click', function(e){
        e.preventDefault();
        $('#taskForm').find("input[type=text],input[type=hidden]").val("");
    });

    function doSave(){
        $("#mpeWorkTask").modal('hide');
        console.log($('#hddStartTime').val());
        console.log($('#hddEndTime').val());
        console.log($('#hddAllDay').val());
        $("#mpeAlertMsg").modal('show');
                
        $("#calendar").fullCalendar('renderEvent', {
            title: $('#txtTask').val(),
            start: $('#hddStartTime').val(),
            end: $('#hddEndTime').val(),
            allDay: ($('#hddAllDay').val() == "true"),
            color: '#87CEFA',
            textColor: '#333333'
        },true);

        $('#taskForm').find("input[type=text],input[type=hidden]").val("");
    }

    $('#colorselector').colorselector();   
    console.log({!! json_encode($resource) !!});
});
</script>
<main class="pt-6">
  <div class="container text-xs-center">
    <section class="section">
      <div id='calendar'></div>

      <div tabindex="-1" class="modal fade" id="mpeWorkTask" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
                    </button>
                <h4 class="modal-title" id="myModalLabel">จองสนาม</h4>
            </div>
            <div class="modal-body text-xs-left reserve">
              <form id="taskForm" class="form-horizontal">
                <input type="hidden" id="hddStartTime" />
                <input type="hidden" id="hddEndTime" />
                <input type="hidden" id="hddAllDay" />
                <div class="form-inline">
                    <div class="form-group md-form">
                        <i class="fa fa-columns prefix"></i>                                            
                        <input class="form-control" id="resource" name="resource" type="text" readonly="readonly" required>
                        <label for="resource">สนาม</label>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="form-group md-form">
                        <i class="fa fa-calendar-times-o prefix"></i>                                            
                        <input class="form-control" id="day" name="day" type="text" readonly="readonly" required>
                        <label for="day">วัน</label>
                    </div>
                    <div class="form-group md-form">
                        <!--<i class="fa fa-clock-o prefix"></i>                                            
                        <input class="form-control" data-format="MM/dd/yyyy HH:mm PP" id="time" name="time" type="text"  required>
                        <label for="time">เวลา</label>-->
                        <div id="datetimepicker2" class="input-append">
                            <input data-format="HH:mm PP" type="text"></input>
                            <span class="add-on">
                            <i data-time-icon="fa fa-clock-o">
                            </i>
                            </span>
                        </div>                 
                    </div>
                    
                </div>
                <div class="form-inline">
                    <div class="form-group md-form">
                        <i class="fa fa-user-o prefix"></i>                                            
                        <input class="form-control" id="nickname" name="nickname" type="text"  required>
                        <label for="nickname">ชื่อเล่น</label>
                    </div>
                    <div class="form-group md-form">
                        <i class="fa fa-mobile prefix"></i>
                        <input class="form-control" id="mobile_number-edit" name="mobile_number" type="text" required>
                        <label for="mobile_number">เบอร์โทร</label>
                    </div>
                </div>
                <div class="form-inline">
                    <div class="md-form">
                        <i class="fa fa-file-text-o prefix"></i>
                        <textarea class="md-textarea" id="note-edit" name="note" type="text"></textarea>
                        <label for="note">โน๊ต</label>
                    </div>                                        
                </div>
                <div class="form-inline">
                    <div class="md-form">
                        <i class="fa fa-file-text-o prefix"></i>
                        <label>สีพื้นหลัง</label>                        
                        <select id="colorselector">
                            <option value="106" data-color="#A0522D">sienna</option>
                            <option value="47" data-color="#CD5C5C" selected="selected">indianred</option>
                            <option value="87" data-color="#FF4500">orangered</option>
                            <option value="17" data-color="#008B8B">darkcyan</option>
                            <option value="18" data-color="#B8860B">darkgoldenrod</option>
                            <option value="68" data-color="#32CD32">limegreen</option>
                            <option value="42" data-color="#FFD700">gold</option>
                            <option value="77" data-color="#48D1CC">mediumturquoise</option>
                            <option value="107" data-color="#87CEEB">skyblue</option>
                            <option value="46" data-color="#FF69B4">hotpink</option>
                            <option value="47" data-color="#CD5C5C">indianred</option>
                            <option value="64" data-color="#87CEFA">lightskyblue</option>
                            <option value="13" data-color="#6495ED">cornflowerblue</option>
                            <option value="15" data-color="#DC143C">crimson</option>
                            <option value="24" data-color="#FF8C00">darkorange</option>
                            <option value="78" data-color="#C71585">mediumvioletred</option>
                            <option value="123" data-color="#000000">black</option>
                        </select>
                        
                    </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" id="btnSave" class="btn btn-primary">บันทึก</button>
              <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
            </div>
          </div>
        </div>
      </div>

      <div id="mpeAlertMsg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
              <h4 class="modal-title" id="mySmallModalLabel">ข้อความ<a class="anchorjs-link" href="#mySmallModalLabel"><span class="anchorjs-icon"></span></a></h4>
            </div>
            <div class="modal-body">
              บันทึกข้อมูลสำเร็จ
            </div>
          </div>
        </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-add-field" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/add-field">
            {{ csrf_field() }}
                <div class="modal-header">
                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                    <h4 class="modal-title" id="myModalLabel">เพิ่มสนาม</h4>
                </div>
                <div class="modal-body text-xs-left reserve">
                
                    <div class="form-inline">
                        <div class="form-group md-form">
                            <i class="fa fa-font prefix"></i>                                            
                            <input class="form-control" id="title" name="title" type="text" required>
                            <label for="title">ชื่อสนาม</label>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="md-form">
                            <i class="fa fa-file-text-o prefix"></i>
                            <textarea class="md-textarea" id="detail" name="detail" type="text"></textarea>
                            <label for="detail">รายละเอียด</label>
                        </div>                                        
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary">บันทึก</button>
                    <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
                </div>
            </form>
          </div>
        </div>
      </div>
  </div>

  </section>

  </div>
</main>
@stop