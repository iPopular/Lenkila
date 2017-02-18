@extends('layouts.master') @section('main')
<link href="{{ URL::asset('css/fullcalendar.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/fullcalendar.print.min.css') }}" rel="stylesheet" media='print'>
<link href="{{ URL::asset('css/fullcalendar.annotations.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/scheduler.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
<link href="{{ URL::asset('css/bootstrap-colorselector.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ URL::asset('js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/fullcalendar.min.js') }}"></script>
<!--<script type="text/javascript" src="{{ URL::asset('js/fullcalendar.js') }}"></script>-->
<script type="text/javascript" src="{{ URL::asset('js/fullcalendar.annotations.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/scheduler.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/bootstrap-colorselector.js') }}"></script>
<script>
  $(document).ready(function() {
    var userRole = {!!json_encode(Auth::user()->role_id)!!};
    var left = '', lastdate;
    console.log(lastdate);
    if (userRole == 3)
      left = 'promptResource myprev';
    else
      left = 'myprev';

    var availHeight = window.screen.availHeight - 200; //144 = 9em
    
    //var availWidth = window.screen.availWidth;

    $('#calendar').fullCalendar({
      
      resourceLabelText: 'สนาม',
      defaultView: 'timelineTwoDays',
      resourceAreaWidth: '10%',
      views: {
        agendaTwoDay: {
          type: 'agenda',
          duration: { days: 2 },
          buttonText: '2 day',
          groupByResource: true
        },
        timelineTwoDays: {
              type: 'timelineWeek',
              duration: { days: 2 },
              buttonText: '2 day',
        }
      },
      editable: true,
      selectable: true,
      //defaultDate: '2017-02-14',
      //businessHours: true, // display business hours
      //eventLimit: true, // allow "more" link when too many events
      header: {
        left: left,
        center: 'title',
        //right: 'agendaDay, agendaTwoDay, mynext'
        right: 'timelineDay, timelineTwoDays, mynext'
      },
      customButtons: {
        promptResource: {
          text: '+ สนาม',
          click: function() {
            $('#modal-add-field').modal('show');
          }
        },
        mynext: {
            icon: 'right-single-arrow',
            click: function() {
                $('#calendar').fullCalendar('incrementDate', moment.duration(2, 'day'));
                $('#calendar').fullCalendar('incrementDate', moment.duration(-1, 'day'));
                setDateToCookie();
            },
        },
        myprev: {
            icon: 'left-single-arrow',
            click: function() {
                $('#calendar').fullCalendar('incrementDate', moment.duration(-1, 'day'));
                setDateToCookie();
            }
        },

      },
          
      //resourceLabelText: 'Rooms',
      resourceRender: function(resource, cellEls) {
        cellEls.on('click', function() {
          if (userRole == 3) {
            $('#modal-edit-field #field_id_edit').val(resource.id);
            $('#modal-edit-field #field_id_delete').val(resource.id);
            $('#modal-edit-field #title').val(resource.title);
            $('#modal-edit-field #detail').val(resource.detail);
            if (resource.status == 1)
              $('#modal-edit-field #edit_status').prop('checked', true);
            else
              $('#modal-edit-field #edit_status').prop('checked', false);
            $('.reserve label').addClass('active');
            $('#modal-edit-field').modal('show');
            console.log(resource);
          }
        });
      },
      lang: 'th',
      timezone: 'Asia/Bangkok',
      axisFormat: 'HH:mm',
      timeFormat: 'HH:mm',
      slotDuration: "01:00:00",
      //snapMinutes: 60,
      height: availHeight,
      //selectOverlap: false,
      eventOverlap: true,
      nowIndicator: true,
      minTime: 0,//{!!json_encode($openTime)!!}
      maxTime: '24:00',//{!!json_encode($closeTime)!!}


      //// uncomment this line to hide the all-day slot
      allDaySlot: false,
      schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
      resources: {!!json_encode($resource)!!},
      events: {!!json_encode($events)!!},
      // selectConstraint: 'available_hours',
      // eventConstraint: 'available_hours',
      select: function(start, end, jsEvent, view, resource, allDay) {
        if(resource.status == 1) {
          endtime = moment(end).format('HH:mm');
          starttime = moment(start).format('HH:mm');
          day = moment(start).format('dd ll');
          startDate = moment(start).format('Y-M-D');
          endDate = moment(end).format('Y-M-D');
          var range = starttime + ' - ' + endtime;
          $('#modal-add-reserve #hddStartTime').val(starttime);
          $('#modal-add-reserve #hddEndTime').val(endtime);
          $('#modal-add-reserve #hddStart').val(start);
          $('#modal-add-reserve #hddEnd').val(end);
          $('#modal-add-reserve #hddAllDay').val(allDay);
          $('#modal-add-reserve #resource').val(resource.title);
          $('#modal-add-reserve #hddResourceId').val(resource.id);
          $('#modal-add-reserve #hddStartDate').val(startDate);
          $('#modal-add-reserve #hddEndDate').val(endDate);
          $('#modal-add-reserve #day').val(day);
          //$('#modal-add-reserve #time').val(range);
          $('#modal-add-reserve #startTime').val(starttime);
          $('#modal-add-reserve #endTime').val(endtime);
          $('.reserve label').addClass('active');
          $('#modal-add-reserve').modal('show');
        }        

      },
      selectOverlap: function(event) {
          return event.rendering === 'background';
      },
      eventClick: function(calEvent, start, end) {
        callEditModal(calEvent, start, end);
      },
      eventResize: function(calEvent, start, end) {
        callEditModal(calEvent, start, end);
      },
      eventDrop: function(calEvent, start, end) {
        callEditModal(calEvent, start, end);
      },
      eventMouseover: function(calEvent) {
        $('#reserve_tooltip #tooltip_label').text(calEvent.description);
        $('#reserve_tooltip').tooltip('show');
      },
      eventMouseout: function(calEvent, jsEvent) {
        $('#reserve_tooltip').tooltip('toggle');
      }
    });

    $(".fc-right > button, .fc-left > button").removeClass();
    $('.fc-right > button, .fc-left > button').addClass('btn btn-cyan waves-effect waves-light');    

    $('.fc-right').prepend('<button id="hiddenField" class="datepicker btn btn-cyan waves-effect waves-light"><i class="fa fa-calendar prefix"></i></button>');      

    $('#hiddenField').datepicker({
    todayHighlight: true,
    showOn: "button",
    autoclose: true,
    todayBtn: "linked",
    buttonText: "day",
    }).on('changeDate', function(e){
      $('#calendar').fullCalendar('gotoDate', new Date(e.format('mm/dd/yyyy')));
      setDateToCookie();
    });

    // on load of the page: switch to the currently selected tab
    var curDate = getCookie('curDate');
    if(curDate != null)
      $('#calendar').fullCalendar('gotoDate', new Date(curDate));
    console.log(new Date(curDate));
    

    function callEditModal(calEvent, start, end) {
      if(calEvent.color != '#c1c1c1')
      {      
        var resource = $('#calendar').fullCalendar('getResourceById', calEvent.resourceId);
        endtime = moment(calEvent.end).format('HH:mm');
        starttime = moment(calEvent.start).format('HH:mm');
        day = moment(calEvent.start).format('dd ll');
        startDate = moment(calEvent.start).format('Y-M-D');
        endDate = moment(calEvent.end).format('Y-M-D');
        var range = starttime + ' - ' + endtime;
        var title = calEvent.title.split('_');
        $('#modal-edit-reserve #hddReserveId').val(calEvent.id);
        $('#modal-edit-reserve #reserve_id_delete').val(calEvent.id);
        $('#modal-edit-reserve #myModalLabel').text(calEvent.title + ' - ' + range);
        $('#modal-edit-reserve #hddStartTime').val(starttime);
        $('#modal-edit-reserve #hddEndTime').val(endtime);
        $('#modal-edit-reserve #hddStart').val(calEvent.start);
        $('#modal-edit-reserve #hddEnd').val(calEvent.end);
        $('#modal-edit-reserve #resource').val(resource.title);
        $('#modal-edit-reserve #nickname').val(title[0]);
        $('#modal-edit-reserve #mobile_number-edit').val(title[1]);
        $('#modal-edit-reserve #note-edit').val(calEvent.description);
        $('#modal-edit-reserve #hddResourceId').val(resource.id);
        $('#modal-edit-reserve #hddStartDate').val(startDate);
        $('#modal-edit-reserve #hddEndDate').val(endDate);
        $('#modal-edit-reserve #day').val(day);
        $('#modal-paid-reserve #field_price').val(calEvent.field_price);
        $('#modal-paid-reserve #water_price').val(calEvent.water_price);
        $('#modal-paid-reserve #supplement_price').val(calEvent.supplement_price);
        $('#modal-paid-reserve #discount_price').val(calEvent.discount_price);
        $('#modal-paid-reserve #hddReserveId').val(calEvent.id);
        //$('#modal-edit-reserve #time').val(range);
        $('#modal-edit-reserve #startTime').val(starttime);
        $('#modal-edit-reserve #endTime').val(endtime);
        sumPrice();
        $('.reserve label').addClass('active');
        $('#modal-edit-reserve').modal('show');
      }
    }

    $('#colorselector').colorselector();

    $('#datetimepicker3').datetimepicker({
      format: 'LT'
    });

    function setDateToCookie() 
    {
      // store the currently selected tab in the hash value
      var tglCurrent = $('#calendar').fullCalendar('getDate');
      tglCurrent = moment(tglCurrent).format('MM/DD/YYYY');
      setCookie("curDate", tglCurrent);
      console.log(tglCurrent);
    }

    var today = new Date();
    var expiry = new Date(today.getTime() * 24 * 3600 * 1000); // plus 24 hours

    function setCookie(name, value)
    {
      document.cookie=name + "=" + escape(value) + "; path=/; expires=" + expiry.toGMTString();
    }

    function getCookie(name)
    {
      var re = new RegExp(name + "=([^;]+)");
      var value = re.exec(document.cookie);
      return (value != null) ? unescape(value[1]) : null;
    }
  });
</script>
<main class="pt-6">
  <div class="container text-xs-center">
    <section class="section">
      @if(Session::has('success_msg'))
      <script>
        $(document).ready(function() {
          toastr["success"]("{{ Session::get('success_msg') }}");
        });
      </script>
      @elseif (Session::has('error_msg'))
      <script>
        $(document).ready(function() {
          toastr["error"]("{{ Session::get('error_msg') }}");
        });
      </script>
      @endif
      <div class="row">
        <div class="col-md-12">
          <div id='calendar'></div>
        </div>
      </div>
      
      <div tabindex="-1" class="modal fade" id="modal-add-reserve" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/add-reserve">
              <meta name="csrf_token" content="{{ csrf_token() }}" />
               {{ csrf_field() }}
              <div class="modal-header">
                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                  <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">จองสนาม</h4>
              </div>
              <div class="modal-body text-xs-left reserve">
                <input type="hidden" id="hddStart" name="hddStart" />
                <input type="hidden" id="hddEnd" name="hddEnd" />
                <input type="hidden" id="hddStartTime" name="hddStartTime" />
                <input type="hidden" id="hddEndTime" name="hddEndTime" />
                <input type="hidden" id="hddResourceId" name="hddResourceId" />
                <input type="hidden" id="hddAllDay" name="hddAllDay" />
                <input type="hidden" id="hddStartDate" name="hddStartDate" />
                <input type="hidden" id="hddEndDate" name="hddEndDate" />
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-columns prefix"></i>
                      <input class="form-control" id="resource" name="resource" type="text" readonly="readonly" required>
                      <label for="resource">สนาม</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-calendar-times-o prefix"></i>
                      <input class="form-control" id="day" name="day" type="text" readonly="readonly" required>
                      <label for="day">วัน</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-clock-o prefix"></i>
                      <input class="form-control" id="startTime" name="startTime" type="time" step="1800" required >
                      <label for="startTime">เวลาเริ่มต้น</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-times-circle-o prefix"></i>
                      <input class="form-control" id="endTime" name="endTime" type="time" step="1800"  required >
                      <label for="endTime">เวลาสิ้นสุด</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-mobile prefix"></i>
                      <input class="form-control" id="mobile_number" name="mobile_number" type="text" onchange="checkCustomer();" required>
                      <label for="mobile_number">เบอร์โทร</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-user-o prefix"></i>
                      <input class="form-control" id="nickname" name="nickname" type="text" required>
                      <label for="nickname">ชื่อเล่น</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="md-form">
                      <i class="fa fa-file-text-o prefix"></i>
                      <textarea class="md-textarea" id="note" name="note" type="text"></textarea>
                      <label for="note">โน๊ต</label>
                    </div>
                  </div>
                </div>

              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> บันทึก</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-edit-reserve" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body text-xs-left reserve">
              <form id="edit-reserve" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/edit-reserve">
                {{ csrf_field() }}
                <input type="hidden" id="hddStart" name="hddStart" />
                <input type="hidden" id="hddEnd" name="hddEnd" />
                <input type="hidden" id="hddStartTime" name="hddStartTime" />
                <input type="hidden" id="hddEndTime" name="hddEndTime" />
                <input type="hidden" id="hddResourceId" name="hddResourceId" />
                <input type="hidden" id="hddStartDate" name="hddStartDate" />
                <input type="hidden" id="hddEndDate" name="hddEndDate" />
                <input type="hidden" id="hddReserveId" name="hddReserveId" />
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-columns prefix"></i>
                      <input class="form-control" id="resource" name="resource" type="text" readonly="readonly" required>
                      <label for="resource">สนาม</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-calendar-times-o prefix"></i>
                      <input class="form-control" id="day" name="day" type="text" readonly="readonly" required>
                      <label for="day">วัน</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-clock-o prefix"></i>
                      <input class="form-control" id="startTime" name="startTime" type="time" step="1800" required >
                      <label for="startTime">เวลาเริ่มต้น</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-times-circle-o prefix"></i>
                      <input class="form-control" id="endTime" name="endTime" type="time" step="1800" required>
                      <label for="endTime">เวลาสิ้นสุด</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-mobile prefix"></i>
                      <input class="form-control" id="mobile_number-edit" name="mobile_number" type="text" readonly="readonly" required>
                      <label for="mobile_number">เบอร์โทร</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="md-form">
                      <i class="fa fa-user-o prefix"></i>
                      <input class="form-control" id="nickname" name="nickname" type="text" required>
                      <label for="nickname">ชื่อเล่น</label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="md-form">
                      <i class="fa fa-file-text-o prefix"></i>
                      <textarea class="md-textarea" id="note-edit" name="note" type="text"></textarea>
                      <label for="note">โน๊ต</label>
                    </div>
                  </div>
                </div>
              </form>
              <form id="delete-reserve" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/delete-reserve">
                {{ csrf_field() }}
                <input type="hidden" id="reserve_id_delete" name="reserve_id">
              </form>
            </div>
            <div class="modal-footer">              
              <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-paid-reserve"><i class="fa fa-money" aria-hidden="true"></i> ชำระเงิน</button>
              <button form="edit-reserve" type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> แก้ไข</button>
              <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>
              <button form="delete-reserve" type="submit" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> ลบ</button>
            </div>
          </div>
        </div>
      </div>

      <div tabindex="-1" class="modal fade bd-example-modal-sm" id="modal-paid-reserve" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="myModalLabel">ชำระเงิน</h4>
            </div>
            <div class="modal-body text-xs-left reserve">
              <form id="paid-reserve" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/paid-reserve">
                {{ csrf_field() }}
                <input type="hidden" id="hddReserveId" name="hddReserveId" />
                <div class="md-form">
                  <i class="fa fa-columns prefix"></i>
                  <input class="form-control price" id="field_price" name="field_price" type="text" onkeyup="sumPrice();">
                  <label for="resource">ค่าสนาม</label>
                </div>
                <div class="md-form">
                  <i class="fa fa-tint prefix"></i>
                  <input class="form-control price" id="water_price" name="water_price" type="text" onkeyup="sumPrice();">
                  <label for="water_price">ค่าน้ำ</label>
                </div>
                <div class="md-form">
                  <i class="fa fa-plus-square-o prefix"></i>
                  <input class="form-control price" id="supplement_price" name="supplement_price" type="text" onkeyup="sumPrice();">
                  <label for="supplement_price">อื่น ๆ</label>
                </div>
                <div class="md-form">
                  <i class="fa fa-scissors prefix"></i>
                  <input class="form-control price" id="discount_price" name="discount_price" type="text" onkeyup="sumPrice();">
                  <label for="discount_price">ส่วนลด</label>
                </div>
                <div class="md-form">
                  <i class="fa fa-money prefix"></i>
                  <input class="form-control" id="total_price" name="total_price" type="text" readonly="readonly">
                  <label for="total_price">รวม</label>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button form="paid-reserve" type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> บันทึก</button>
              <!--<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>-->
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
                  <div class="form-group md-form">
                    <fieldset class="form-group">
                      <input type="checkbox" id="add_status" name="add_status">
                      <label for="add_status">เปิดบริการ</label>
                    </fieldset>
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
                <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> บันทึก</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-edit-field" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                <span aria-hidden="true">×</span>
              </button>
              <h4 class="modal-title" id="myModalLabel">แก้ไขสนาม</h4>
            </div>
            <div class="modal-body text-xs-left reserve">
              <form id="edit-field" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/edit-field">
                {{ csrf_field() }}
                <input type="hidden" id="field_id_edit" name="field_id">
                <div class="form-inline">
                  <div class="form-group md-form">
                    <i class="fa fa-font prefix"></i>
                    <input class="form-control" id="title" name="title" type="text" required>
                    <label for="title">ชื่อสนาม</label>
                  </div>
                  <div class="form-group md-form">
                    <fieldset class="form-group">
                      <input type="checkbox" id="edit_status" name="edit_status">
                      <label for="edit_status">เปิดบริการ</label>
                    </fieldset>
                  </div>
                </div>
                <div class="form-inline">
                  <div class="md-form">
                    <i class="fa fa-file-text-o prefix"></i>
                    <textarea class="md-textarea" id="detail" name="detail" type="text"></textarea>
                    <label for="detail">รายละเอียด</label>
                  </div>
                </div>
              </form>
              <form id="delete-field" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/delete-field">
                {{ csrf_field() }}
                <input type="hidden" id="field_id_delete" name="field_id">
              </form>
            </div>
            <div class="modal-footer">
              <button form="delete-field" type="submit" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> ลบ</button>
              <button form="edit-field" type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> บันทึก</button>
              <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Generated markup by the plugin -->
      <div class="tooltip top" role="tooltip" id="reserve_tooltip">
        <div class="tooltip-arrow"></div>
        <div class="tooltip-inner">
          <p id="tooltip_label"></p>
        </div>
      </div>
  </div>

  </section>

  </div>
</main>
@stop