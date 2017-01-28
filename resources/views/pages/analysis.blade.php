@extends('layouts.master') @section('main')
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
        <h5>วิเคราะห์ข้อมูล</h5>
            <div class="row">

                <div class="col-md-4">
                    <form id="formAnalysis" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/analysis-getStat">
                        <meta name="csrf_token" content="{{ csrf_token() }}" />
                        <div class="input-daterange input-group" id="date-analysis">
                            <input name="mount" type="text" class="input-sm form-control"/>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-2 md-form">
                        </div>
                        <div class="col-md-6 md-form">
                            <label>รายได้ทั้งหมด</label>
                        </div>
                        <div class="col-md-4 md-form">
                            <label id="income"></label>
                        </div>
                    </div>
                    </br>
                    <div class="row">
                        <div class="col-md-2 md-form">
                        </div>
                        <div class="col-md-6 md-form">
                            <label>จำนวนการจอง</label>
                        </div>
                        <div class="col-md-4 md-form">
                            <label id="count_reserve"></label>
                        </div>
                    </div>          
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                            <select id="discount_type_edit" name="discount_type" class="mdb-select colorful-select dropdown-ins" required>
                                <option value="money">รายได้ทั้งหมด</option>
                                <option value="visit">จำนวนการจอง</option>
                            </select> 
                        </div>
                    </div>
                    <canvas id="lineChartEx"></canvas>
                </div>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-md-4 md-form">
                    <label style="font-size: 1.25rem;">ลูกค้าดีเด่นประจำเดือน</label>
                </div>
            </div>
            <br>
            <div class="row">                
                <div class="col-md-3 md-form">                    
                    <button id="btn-customer" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-customer" type="button" data-toggle="modal" data-target="#modal-customer">
                        <h2 id="best_customer"></h2>
                    </button> 
                </div>
            </div>

             <div tabindex="-1" class="modal fade modal-ext" id="modal-customer" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                <div class="modal-dialog" role="document">
                    <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/edit-best-customer">
                    {{ csrf_field() }}
                        <!--Content-->
                        <div class="modal-content">
                            <!--Header-->
                            <div class="modal-header">
                                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">ข้อมูลลูกค้า</h4>
                            </div>
                            <!--Body-->
                            <div class="modal-body text-xs-left edit">
                                <input type="hidden" id="hdd_mobile_number" name="hdd_mobile_number">
                                <div class="form-inline">
                                    <div class="form-group md-form">
                                        <i class="fa fa-user-o prefix"></i>                                            
                                        <input class="form-control" id="nickname" name="nickname" type="text" disabled="disabled" required>
                                        <label for="nickname">ชื่อเล่น</label>
                                    </div>
                                    <div class="form-group md-form">
                                        <i class="fa fa-mobile prefix"></i>
                                        <input class="form-control" id="mobile_number" name="mobile_number" type="text" disabled="disabled" required>
                                        <label for="mobile_number">เบอร์โทร</label>
                                    </div>
                                </div>
                                <div class="form-inline">
                                    <div class="form-group md-form">
                                        <i class="fa fa-user prefix"></i>
                                        <input class="form-control" id="firstname" name="firstname" disabled="disabled" type="text">
                                        <label for="firstname">ชื่อ</label>
                                    </div>
                                    <div class="form-group md-form">
                                        <i class="fa fa-users prefix"></i>
                                        <input class="form-control" id="lastname" name="lastname" disabled="disabled" type="text">
                                        <label for="lastname">นามสกุล</label>
                                    </div>
                                </div>
                                <div class="form-inline">
                                    <div class="form-group md-form">
                                        <i class="fa fa-calendar prefix"></i>
                                        <input class="form-control datepicker" id="birthday" name="birthday" type="text" disabled="disabled" >
                                        <label for="birthday">วันเกิด</label>
                                    </div>                                        
                                    <div class="form-group">
                                        <fieldset class="form-group">
                                            <input name="sex" id="male" type="radio" value="male" disabled="disabled" >
                                            <label for="male">ชาย</label>
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <input name="sex" id="female" value="female" type="radio" disabled="disabled">
                                            <label for="female">หญิง</label>
                                        </fieldset>
                                    </div>
                                </div>   
                                <div class="form-inline">
                                    <div class="md-form">
                                        <i class="fa fa-briefcase prefix"></i>
                                        <input class="form-control" id="workplace" name="workplace" type="text" disabled="disabled" >
                                        <label for="workplace">ที่ทำงาน</label>
                                    </div>
                                </div>
                                <div class="form-inline">
                                    <div class="form-group md-form">
                                        <i class="fa fa-car prefix"></i>
                                        <input class="form-control" id="visited" name="visited" type="text" disabled="disabled" >
                                        <label for="visited">จำนวนครั้งที่เคยมา</label>
                                    </div>

                                    <div class="form-group md-form">
                                        <i class="fa fa-clock-o prefix"></i>
                                        <input class="form-control" id="time-often" name="time-often" type="text" disabled="disabled">
                                        <label for="time-often">เวลาที่มาบ่อย</label>
                                    </div>
                                </div>
                                <div class="form-inline">
                                    <div class="md-form">
                                        <i class="fa fa-file-text-o prefix"></i>
                                        <textarea class="md-textarea" id="note" name="note" type="text"></textarea>
                                        <label for="note">โน๊ต</label>
                                    </div>                                        
                                </div>
                            </div>
                            <!--Footer-->
                            <div class="modal-footer">
                                <div class="form-inline" style="color:red;">
                                    <!--<i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group  text-important">หากต้องการแก้ไขข้อมูลลูกค้า กรุณาแก้ไขในเมนูข้อมูลลูกค้า</p>-->
                                    <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-pencil" aria-hidden="true"></i> บันทึก</button>
                                </div>
                            </div>                            
                        </div>
                        <!--/.Content-->
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>
@stop