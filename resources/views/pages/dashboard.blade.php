@extends('layouts.master') @section('main')
<main class="pt-6">
  <div class="container">
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
      @foreach($errors->all() as $error)
          <script>
              $( document ).ready(function() {
                  toastr["error"]("{{ $error }}");
              });
          </script>
      @endforeach
      <!--Name-->
      <h3 class="h3-responsive">แผงควบคุม</h3>
      <br>
        
      <!-- Nav tabs -->
      <ul class="nav nav-tabs md-pills pills-ins" role="tablist" id="myTab">
          <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#panel1_stadium" role="tab"><i class="fa fa-flag"></i> สนาม</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#panel1_field_price" role="tab"><i class="fa fa-money"></i> ราคาสนาม</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#panel1_promotion" role="tab"><i class="fa fa-heart"></i> โปรโมชั่น</a>
          </li>
          <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#panel1_holiday" role="tab"><i class="fa fa-sun-o"></i> วันหยุด</a>
          </li>
      </ul>

      <!-- Tab panels -->
      <div class="tab-content">

          <!--Panel 1-->
          <div class="tab-pane fade in active" id="panel1_stadium" role="tabpanel">
              <br>
              <div class="row">
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                  <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/edit-stadium">
                  {{ csrf_field() }}
                    <!--Naked Form-->
                    <div class="card-block">
                        <!--Body-->
                        <div class="md-form">
                            <i class="fa fa-flag prefix"></i>
                            <input class="form-control" id="staduim_name_edit" name="staduim_name_edit" type="text" value="{{$stadium_name}}">
                            <label for="staduim_name_edit">ชื่อสเตเดียม</label>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="md-form">
                              <i class="fa fa-clock-o prefix"></i>
                              <input class="form-control" id="openTime" name="openTime" type="time" value="{{$stadium->open_time}}" required>
                              <label class="active" for="openTime">เวลาเปิด</label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="md-form">
                              <i class="fa fa-times-circle-o prefix"></i>
                              <input class="form-control" id="closeTime" name="closeTime" type="time"  value="{{$stadium->close_time}}" required>
                              <label class="active" for="closeTime">เวลาปิด</label>
                            </div>
                          </div>
                        </div>
                        <div class="md-form">
                          <i class="fa fa-map-marker prefix"></i>
                          <input type="text" id="address" name="address" class="form-control" value="{{$stadium->address}}">
                          <label for="address">ที่อยู่</label>
                        </div>
                        <div class="md-form">
                          <i class="fa fa-info-circle prefix"></i>
                          <input type="text" id="detail" name="detail" class="form-control" value="{{$stadium->detail}}">
                          <label for="detail" class="">รายละเอียด</label>
                        </div>
                        <div class="text-xs-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> บันทึก</button>
                        </div>
                    </div>
                  </form>
                </div>
              </div>

          </div>
          <!--/.Panel 1-->

          <!--Panel 2-->
          <div class="tab-pane fade" id="panel1_field_price" role="tabpanel">
              <br>
              <div class="text-xs-right">
                  <button id="btn-add" class="btn btn-md btn-info waves-effect waves-light" type="button" data-toggle="modal" data-target="#modal-add-field_price"><i class="fa fa-plus" aria-hidden="true"></i>   สร้าง</button>
              </div>
              <div class="text-xs-center">
                <table id="table_field_price" class="table table-hover" data-page-length="5" cellspacing="0">
                    <!--Table head-->
                    <thead>
                        <tr>
                            <th>ชื่อสนาม  </th>
                            <th>เวลาเริ่ม  </th>
                            <th>เวลาสิ้นสุด  </th>
                            <th>วัน  </th>
                            <th>ราคา/ชม.  </th>
                            <th></th>
                        </tr>
                    </thead>
                    <!--/Table head-->

                    <!--Table body-->
                    <tbody>                            
                        <!--First row-->                      
                        @foreach($stadium->field as $field)
                          @foreach($field->tmp_field_price as $field_price)                        
                            <tr>
                                <form id="form-{{$field_price->id}}" class="form-horizontal" role="form" method="POST">
                                    {{ csrf_field() }}                                    
                                    <input id="field-{{$field_price->id}}" type="hidden" value="{{ $field_price->field_id }}">
                                    <input id="start_time-{{$field_price->id}}" type="hidden" value="{{ $field_price->start_time }}">
                                    <input id="end_time-{{$field_price->id}}" type="hidden" value="{{ $field_price->end_time }}">
                                    <input id="day-{{$field_price->id}}" type="hidden" value="{{ $field_price->day }}">
                                    <!--<input id="end_date-{{$field_price->id}}" type="hidden" value="{{ $field_price->end_date }}">-->
                                    <input id="price-{{$field_price->id}}" type="hidden" value="{{ $field_price->price }}">
                                    <input id="bgColor-{{$field_price->id}}" type="hidden" value="{{ $field_price->set_color }}">                                   
                                    <td>                                        
                                        {{ $field->name }}                                        
                                    </td>
                                    <td>
                                        {{ $field_price->start_time }}                                        
                                    </td>
                                    <td>
                                        {{ $field_price->end_time }}
                                    </td>
                                    <td>
                                        {{ $field_price->day }}
                                    </td>
                                    <td>
                                        {{ $field_price->price }}
                                    </td>
                                    <td>                                       
                                        <button form="form-{{$field_price->id}}" id="btn-edit-field_price-{{$field_price->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit-field_price" type="button" data-toggle="modal" data-target="#modal-edit-field_price">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>                                   
                                    
                                        <button id="btn-delete-{{$field_price->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete-field_price" type="button" data-original-title="Remove item" data-toggle="tooltip">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>                                       
                                    </td>
                                </form>
                            </tr>
                            @endforeach
                          @endforeach
                        <!--/First row-->

                    </tbody>
                    <!--/Table body-->
                </table>
              </div>

          </div>
          <!--/.Panel 2-->

          <!--Panel 3-->
          <div class="tab-pane fade" id="panel1_promotion" role="tabpanel">
              <br>
              <div class="text-xs-right">
                  <button id="btn-add-promotion" class="btn btn-md btn-info waves-effect waves-light" type="button" data-toggle="modal" data-target="#modal-add-promotion"><i class="fa fa-plus" aria-hidden="true"></i>   สร้าง</button>
              </div>
              <div class="text-xs-center">
                <table id="table_promotion" class="table table-hover" data-page-length="5" cellspacing="0">
                    <!--Table head-->
                    <thead>
                        <tr>
                            <th>ชื่อโปรโมชั่น  </th>
                            <th>เวลาเริ่ม  </th>
                            <th>เวลาสิ้นสุด  </th>
                            <th>วันที่เริ่ม  </th>
                            <th>วันที่สิ้นสุด  </th>
                            <th>ส่วนลด/ชม.  </th>
                            <!--<th>ล็อคช่วงเวลา  </th>-->
                            <th></th>
                        </tr>
                    </thead>
                    <!--/Table head-->

                    <!--Table body-->
                    <tbody>                            
                        <!--First row-->                      
                        
                          @foreach($stadium->promotions as $promotion)                        
                            <tr>                                                                
                                <input id="pro-promotion_name-{{$promotion->id}}" type="hidden" value="{{ $promotion->name }}">
                                <input id="pro-start_time-{{$promotion->id}}" type="hidden" value="{{ $promotion->start_time }}">
                                <input id="pro-end_time-{{$promotion->id}}" type="hidden" value="{{ $promotion->end_time }}">
                                <input id="pro-start_date-{{$promotion->id}}" type="hidden" value="{{ $promotion->start_date }}">
                                <input id="pro-end_date-{{$promotion->id}}" type="hidden" value="{{ $promotion->end_date }}">
                                <input id="pro-discount-{{$promotion->id}}" type="hidden" value="{{ $promotion->discount }}">
                                <input id="pro-discount_type-{{$promotion->id}}" type="hidden" value="{{ $promotion->discount_type }}">
                                <input id="pro-fixed_range-{{$promotion->id}}" type="hidden" value="{{ $promotion->fixed_range }}">                                                                
                                <td>                                        
                                    {{ $promotion->name }}
                                </td>                                    
                                <td>
                                    {{ $promotion->start_time }}                                        
                                </td>
                                <td>
                                    {{ $promotion->end_time }}
                                </td>
                                <td>
                                    {{ $promotion->start_date }}
                                </td>
                                <td>
                                    {{ $promotion->end_date }}
                                </td>
                                <td>
                                    {{ $promotion->discount }} {{ $promotion->discount_type }}
                                </td>
                                <!--<td>
                                    @if($promotion->fixed_range == '1')
                                      ใช่ 
                                    @else 
                                      ไม่ 
                                    @endif
                                </td>-->
                                <td>                                       
                                    <button form="form-promotion-{{$promotion->id}}" id="btn-edit-promotion-{{$promotion->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit-promotion" type="button" data-toggle="modal" data-target="#modal-edit-promotion">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>                                   
                                
                                    <button id="btn-delete-{{$promotion->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete-promotion" type="button" data-original-title="Remove item" data-toggle="tooltip">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>                                       
                                </td>                                
                            </tr>
                            @endforeach
                          
                        <!--/First row-->

                    </tbody>
                    <!--/Table body-->
                </table>
              </div>
              
          </div>
          <!--/.Panel 3-->

          <!--Panel 4-->
          <div class="tab-pane fade" id="panel1_holiday" role="tabpanel">
              <br>
              <div class="text-xs-right">
                  <button id="btn-add-holiday" class="btn btn-md btn-info waves-effect waves-light" type="button" data-toggle="modal" data-target="#modal-add-holiday"><i class="fa fa-plus" aria-hidden="true"></i>   สร้าง</button>
              </div>
              <div class="text-xs-center">
                <table id="table_holiday" class="table table-hover" data-page-length="5" cellspacing="0">
                    <!--Table head-->
                    <thead>
                        <tr>
                            <th>วัน  </th>
                            <th>เวลาเริ่ม  </th>
                            <th>เวลาสิ้นสุด  </th>
                            <th>วันที่เริ่ม  </th>
                            <th>วันที่สิ้นสุด  </th>
                            <th>เปิด/ปิดบริการ  </th>
                            <th></th>
                        </tr>
                    </thead>
                    <!--/Table head-->

                    <!--Table body-->
                    <tbody>                            
                        <!--First row-->                      
                        
                          @foreach($stadium->holidays as $holiday)                        
                            <tr>                                                                
                                <input id="holiday_name-{{$holiday->id}}" type="hidden" value="{{ $holiday->name }}">
                                <input id="holiday_start_time-{{$holiday->id}}" type="hidden" value="{{ $holiday->start_time }}">
                                <input id="holiday_end_time-{{$holiday->id}}" type="hidden" value="{{ $holiday->end_time }}">
                                <input id="holiday_start_date-{{$holiday->id}}" type="hidden" value="{{ $holiday->start_date }}">
                                <input id="holiday_end_date-{{$holiday->id}}" type="hidden" value="{{ $holiday->end_date }}">
                                <input id="holiday_avalible-{{$holiday->id}}" type="hidden" value="{{ $holiday->avalible }}">                                                    
                                <td>                                        
                                    {{ $holiday->name }}
                                </td>
                                <td>
                                    {{ $holiday->start_time }}                                        
                                </td>
                                <td>
                                    {{ $holiday->end_time }}
                                </td>                                    
                                <td>
                                    {{ $holiday->start_date }}
                                </td>
                                <td>
                                    {{ $holiday->end_date }}
                                </td>
                                <td>
                                    @if($holiday->avalible == '1')
                                      เปิด 
                                    @else 
                                      ปิด 
                                    @endif
                                </td>
                                <td>                                       
                                    <button form="form-holiday-{{$holiday->id}}" id="btn-edit-holiday-{{$holiday->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit-holiday" type="button" data-toggle="modal" data-target="#modal-edit-holiday">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>                                   
                                
                                    <button id="btn-delete-{{$holiday->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete-holiday" type="button" data-original-title="Remove item" data-toggle="tooltip">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>                                       
                                </td>                                
                            </tr>
                            @endforeach
                          
                        <!--/First row-->

                    </tbody>
                    <!--/Table body-->
                </table>
              </div>
              
          </div>
          <!--/.Panel 4-->

      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-add-field_price" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/add-field-price">
              {{ csrf_field() }}
                  <!--Content-->
                  <div class="modal-content">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">สร้างข้อมูลราคา</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left edit">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-square-o prefix"></i>
                                  <select id="field" name="field" class="mdb-select colorful-select dropdown-ins" required>
                                      <option value="">กรุณาเลือกสนาม</option>
                                      @foreach($stadium->field as $field)
                                          <option value="{{ $field->id }}">{{ $field->name }}</option>
                                      @endforeach
                                  </select>
                                  <label id="field_label" for="field">สนาม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-money prefix"></i>
                                  <input class="form-control" id="field_price" name="field_price" type="text" required>
                                  <label id="price_label" for="field_price">ราคา</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time" name="start_time" type="time" value="00:00" required>
                                <label class="active" for="start_time">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time" name="end_time" type="time" value="00:00" required>
                                <label class="active" for="end_time">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">                            
                            <div class="col-md-1">
                            </div>
                            <div class="col-md-11">
                                <div class="md-form">
                                    <div class="form-inline">
                                        <label class="active" >วัน</label>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_0" name="day_0">
                                            <label for="day_0">Sun</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_1" name="day_1">
                                            <label for="day_1">Mon</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_2" name="day_2">
                                            <label for="day_2">Tue</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_3" name="day_3">
                                            <label for="day_3">Wen</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_4" name="day_4">
                                            <label for="day_4">Thu</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_5" name="day_5">
                                            <label for="day_5">Fri</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_6" name="day_6">
                                            <label for="day_6">Sat</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="holiday" name="holiday">
                                            <label for="holiday">Holiday</label>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-eyedropper prefix"></i>                                                               
                                <input id="bgColor" name="bgColor" type="color" value="" class="form-control" style="height: 50px;  border: none;"/>
                                <label class="active" for="bgColor">สีพื้นหลัง</label>
                              </div> 
                            </div>
                          </div>
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> สร้าง</button>
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-edit-field_price" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/edit-field-price">
              {{ csrf_field() }}
                <input type="hidden" id="hdd_field_price" name="hdd_field_price">
                  <!--Content-->
                  <div class="modal-content">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">แก้ไขข้อมูลราคา</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left edit">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-square-o prefix"></i>
                                  <select id="field_edit" name="field" class="mdb-select colorful-select dropdown-ins" required>
                                      <option value="">กรุณาเลือกสนาม</option>
                                      @foreach($stadium->field as $field)
                                          <option value="{{ $field->id }}">{{ $field->name }}</option>
                                      @endforeach
                                  </select>
                                  <label id="field_label_edit" for="field_edit">สนาม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-money prefix"></i>
                                  <input class="form-control" id="field_price_edit" name="field_price" type="text" required>
                                  <label id="price_label_edit" for="field_price_edit">ราคา</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time_edit" name="start_time" type="time" required>
                                <label class="active" for="start_time_edit">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time_edit" name="end_time" type="time" required>
                                <label class="active" for="end_time_edit">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">                            
                            <div class="col-md-1">
                            </div>
                            <div class="col-md-11">
                                <div class="md-form">
                                    <div class="form-inline">
                                        <label class="active" >วัน</label>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_0-edit" name="day_0">
                                            <label for="day_0-edit">Sun</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_1-edit" name="day_1">
                                            <label for="day_1-edit">Mon</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_2-edit" name="day_2">
                                            <label for="day_2-edit">Tue</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_3-edit" name="day_3">
                                            <label for="day_3-edit">Wen</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_4-edit" name="day_4">
                                            <label for="day_4-edit">Thu</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_5-edit" name="day_5">
                                            <label for="day_5-edit">Fri</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="day_6-edit" name="day_6">
                                            <label for="day_6-edit">Sat</label>
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <input type="checkbox" class="filled-in" id="holiday-edit" name="holiday">
                                            <label for="holiday-edit">Holiday</label>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                          </div>
                          <br>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-eyedropper prefix"></i>                                                               
                                <input id="bgColor_edit" name="bgColor" type="color" value="" class="form-control" style="height: 50px;  border: none;"/>
                                <label class="active" for="bgColor_edit">สีพื้นหลัง</label>
                              </div> 
                            </div>
                          </div>
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-success waves-effect waves-light btn-copy-field_price" type="button" data-toggle="modal" data-target="#modal-add-field_price"><i class="fa fa-files-o" aria-hidden="true"></i> คัดลอก</button>
                              <button class="btn btn-default waves-effect waves-light" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i> แก้ไข</button>                              
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-delete-field_price" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <!--Content-->
              <div class="modal-content text-xs-center">
                  <form id="form-del-account" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/delete-field-price">
                      {{ csrf_field() }}
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title modal-title-important" id="myModalLabel">คำเตือน!</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body">
                          <p class="text-important">การลบข้อมูลราคาจะไม่สามารถกู้คืนได้!</p>
                          <strong>ท่านต้องการลบข้อมูลราคา ใช่หรือไม่?</strong>
                          <input id="del-field_price" name="del-field_price" type="hidden">
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <button class="btn btn-danger waves-effect waves-light" type="submit"><i class="fa fa-trash-o" aria-hidden="true"></i> ใช่</button>
                          <button class="btn btn-info waves-effect waves-light" type="button" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ไม่</button>
                      </div>
                  </form>
              </div>
              <!--/.Content-->
          </div>
      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-add-promotion" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/add-promotion">
              {{ csrf_field() }}
                  <!--Content-->
                  <div class="modal-content">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">สร้างข้อมูลโปรโมรชั่น</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left">
                          <div class="row">                            
                            <div class="col-md-12">
                              <div class="md-form">
                                  <i class="fa fa-font prefix"></i>
                                  <input class="form-control" id="promotion_name" name="promotion_name" type="text" required>
                                  <label for="promotion_name">ชื่อโปรโมชั่น</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time" name="start_time" type="time" value="00:00" required>
                                <label class="active" for="start_time">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time" name="end_time" type="time" value="00:00" required>
                                <label class="active" for="end_time">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="input-daterange" id="field_price_datepicker">
                              <div class="col-md-6">
                                <div class="md-form">
                                    <i class="fa fa-calendar-check-o prefix"></i>
                                    <input class="form-control" id="start_date" name="start" type="text">
                                    <label for="start_date">วันที่เริ่ม</label>
                                </div>
                              </div>
                              <div class="col-md-6">                                  
                                <div class="md-form">
                                    <i class="fa fa-calendar-times-o prefix"></i>
                                    <input class="form-control" id="end_date" name="end" type="text">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <br>
                          <div class="row">                            
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-scissors prefix"></i>
                                  <input class="form-control" id="discount" name="discount" type="text">
                                  <label for="discount">ส่วนลด</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form discount_type">
                                <i class="fa fa-percent prefix"></i>
                                <select id="discount_type" name="discount_type" class="mdb-select colorful-select dropdown-ins" required>
                                    <option value="THB">บาท</option>
                                    <option value="%">ร้อยละ</option>
                                </select>                                
                              </div>
                            </div>                            
                          </div>
                          <!--<div class="row">
                            <div class="col-md-12">
                              <fieldset class="form-group">
                                  <input type="checkbox" id="fixed_range" name="fixed_range">
                                  <label for="fixed_range">ล็อคช่วงเวลา</label>
                              </fieldset>
                            </div>
                          </div>-->
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> สร้าง</button>
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-edit-promotion" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/edit-promotion">
              {{ csrf_field() }}
                  <!--Content-->
                  <div class="modal-content">
                    <input type="hidden" id="hddpromotion" name="hddpromotion">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">แก้ไขข้อมูลโปรโมรชั่น</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left edit">
                          <div class="row">                            
                            <div class="col-md-12">
                              <div class="md-form">
                                  <i class="fa fa-font prefix"></i>
                                  <input class="form-control" id="promotion_name_edit" name="promotion_name" type="text" required>
                                  <label for="promotion_name_edit">ชื่อโปรโมชั่น</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time_edit" name="start_time" type="time" required >
                                <label class="active" for="start_time_edit">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time_edit" name="end_time" type="time" required>
                                <label class="active" for="end_time_edit">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="input-daterange" id="field_price_datepicker">
                              <div class="col-md-6">
                                <div class="md-form">
                                    <i class="fa fa-calendar-check-o prefix"></i>
                                    <input class="form-control" id="start_date_edit" name="start" type="text">
                                    <label for="start_date_edit">วันที่เริ่ม</label>
                                </div>
                              </div>
                              <div class="col-md-6">                                  
                                <div class="md-form">
                                    <i class="fa fa-calendar-times-o prefix"></i>
                                    <input class="form-control" id="end_date_edit" name="end" type="text">
                                    <label for="end_date_edit">วันที่สิ้นสุด</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <br>
                          <div class="row">                            
                            <div class="col-md-6">
                              <div class="md-form">
                                  <i class="fa fa-scissors prefix"></i>
                                  <input class="form-control" id="discount_edit" name="discount" type="text">
                                  <label for="discount_edit">ส่วนลด</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form discount_type">
                                <i class="fa fa-percent prefix"></i>
                                <select id="discount_type_edit" name="discount_type" class="mdb-select colorful-select dropdown-ins" required>
                                    <option value="THB">บาท</option>
                                    <option value="%">ร้อยละ</option>
                                </select>                                
                              </div>
                            </div>                            
                          </div>
                          <!--<div class="row">
                            <div class="col-md-12">
                              <fieldset class="form-group">
                                  <input type="checkbox" id="fixed_range_edit" name="fixed_range">
                                  <label for="fixed_range_edit">ล็อคช่วงเวลา</label>
                              </fieldset>
                            </div>
                          </div>-->
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i> แก้ไข</button>
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-delete-promotion" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <!--Content-->
              <div class="modal-content text-xs-center">
                  <form id="form-del-account" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/delete-promotion">
                      {{ csrf_field() }}
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title modal-title-important" id="myModalLabel">คำเตือน!</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body">
                          <p class="text-important">การลบข้อมูลโปรโมรชั่นจะไม่สามารถกู้คืนได้!</p>
                          <strong>ท่านต้องการลบข้อมูลโปรโมรชั่น ใช่หรือไม่?</strong>
                          <input id="del-promotion" name="del-promotion" type="hidden">
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <button class="btn btn-danger waves-effect waves-light" type="submit"><i class="fa fa-trash-o" aria-hidden="true"></i> ใช่</button>
                          <button class="btn btn-info waves-effect waves-light" type="button" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ไม่</button>
                      </div>
                  </form>
              </div>
              <!--/.Content-->
          </div>
      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-add-holiday" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/add-holiday">
              {{ csrf_field() }}
                  <!--Content-->
                  <div class="modal-content">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">สร้างข้อมูลวันหยุด</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left">
                          <div class="row">                            
                            <div class="col-md-12">
                              <div class="md-form">
                                  <i class="fa fa-font prefix"></i>
                                  <input class="form-control" id="holiday_name" name="holiday_name" type="text" required>
                                  <label for="holiday_name">วัน</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time" name="start_time" type="time" value="00:00" required>
                                <label class="active" for="start_time">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time" name="end_time" type="time" value="00:00" required>
                                <label class="active" for="end_time">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="input-daterange" id="holiday_datepicker">
                              <div class="col-md-6">
                                <div class="md-form">
                                    <i class="fa fa-calendar-check-o prefix"></i>
                                    <input class="form-control" id="start_date" name="start" type="text">
                                    <label for="start_date">วันที่เริ่ม</label>
                                </div>
                              </div>
                              <div class="col-md-6">                                  
                                <div class="md-form">
                                    <i class="fa fa-calendar-times-o prefix"></i>
                                    <input class="form-control" id="end_date" name="end" type="text">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <input type="checkbox" class="filled-in" id="avalible" name="avalible">
                                    <label for="avalible">เปิดบริการ</label>
                                </fieldset>
                            </div>
                          </div>
                          <br>
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> สร้าง</button>
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade modal-ext" id="modal-edit-holiday" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/edit-holiday">
              {{ csrf_field() }}
                  <!--Content-->
                  <div class="modal-content">
                    <input type="hidden" id="hddholiday" name="hddholiday">
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title" id="myModalLabel">แก้ไขข้อมูลวันหยุด</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body text-xs-left edit">
                          <div class="row">                            
                            <div class="col-md-12">
                              <div class="md-form">
                                  <i class="fa fa-font prefix"></i>
                                  <input class="form-control" id="holiday_name-edit" name="holiday_name" type="text" required>
                                  <label for="holiday_name-edit">วัน</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-clock-o prefix"></i>
                                <input class="form-control" id="start_time" name="start_time" type="time" value="00:00" required>
                                <label class="active" for="start_time">เวลาเริ่ม</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="md-form">
                                <i class="fa fa-times-circle-o prefix"></i>
                                <input class="form-control" id="end_time" name="end_time" type="time" value="00:00" required>
                                <label class="active" for="end_time">เวลาสิ้นสุด</label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="input-daterange" id="holiday_datepicker">
                              <div class="col-md-6">
                                <div class="md-form">
                                    <i class="fa fa-calendar-check-o prefix"></i>
                                    <input class="form-control" id="start_date" name="start" type="text">
                                    <label for="start_date">วันที่เริ่ม</label>
                                </div>
                              </div>
                              <div class="col-md-6">                                  
                                <div class="md-form">
                                    <i class="fa fa-calendar-times-o prefix"></i>
                                    <input class="form-control" id="end_date" name="end" type="text">
                                    <label for="end_date">วันที่สิ้นสุด</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <input type="checkbox" class="filled-in" id="holiday_avalible" name="holiday_avalible">
                                    <label for="holiday_avalible">เปิดบริการ</label>
                                </fieldset>
                            </div>
                          </div>
                          <br>
                        
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <div class="form-inline" style="color:red;">
                              <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">กรุณากรอกข้อมูลให้ครบถ้วน</p>
                              <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-plus" aria-hidden="true"></i> แก้ไข</button>
                          </div>
                      </div>                            
                  </div>
                  <!--/.Content-->
              </form>
          </div>
      </div>

      <div tabindex="-1" class="modal fade" id="modal-delete-holiday" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
          <div class="modal-dialog" role="document">
              <!--Content-->
              <div class="modal-content text-xs-center">
                  <form id="form-del-account" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/delete-holiday">
                      {{ csrf_field() }}
                      <!--Header-->
                      <div class="modal-header">
                          <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                  <span aria-hidden="true">×</span>
                              </button>
                          <h4 class="modal-title modal-title-important" id="myModalLabel">คำเตือน!</h4>
                      </div>
                      <!--Body-->
                      <div class="modal-body">
                          <p class="text-important">การลบข้อมูลวันหยุดจะไม่สามารถกู้คืนได้!</p>
                          <strong>ท่านต้องการลบข้อมูลวันหยุด ใช่หรือไม่?</strong>
                          <input id="del-holiday" name="del-holiday" type="hidden">
                      </div>
                      <!--Footer-->
                      <div class="modal-footer">
                          <button class="btn btn-danger waves-effect waves-light" type="submit"><i class="fa fa-trash-o" aria-hidden="true"></i> ใช่</button>
                          <button class="btn btn-info waves-effect waves-light" type="button" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ไม่</button>
                      </div>
                  </form>
              </div>
              <!--/.Content-->
          </div>
      </div>

    </section>
  </div>
</main>
@stop