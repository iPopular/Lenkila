@extends('layouts.master') @section('main')
<main class="pt-6">
    <div class="container text-xs-center">
        <section class="section">
            @if(Session::has('success_msg'))                    
                <script>                    
                    $( document ).ready(function() {
                        toastr["success"]("{{ Session::get('success_msg') }}");
                    });
                </script>
            @elseif (Session::has('error_msg'))
                <script>
                    $( document ).ready(function() {
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
            <h5>ข้อมูลลูกค้า </h5>
                    
                <div class="text-xs-right">
                    <button id="btn-add" class="btn btn-md btn-info waves-effect waves-light btn-table btn-edit" type="button" data-toggle="modal" data-target="#modal-add-customer"><i class="fa fa-plus" aria-hidden="true"></i>   สร้าง</button>
                </div>
                <!--Account table-->
                <div class="table-responsive">
                    <table class="table product-table">
                        <!--Table head-->
                        <thead>
                            <tr>
                                <th>ชื่อเล่น</th>
                                <th>เบอร์โทร</th>
                                <th>วันที่มาบ่อย</th>
                                <th>เวลาที่มาบ่อย</th>
                                <th></th>
                            </tr>
                        </thead>
                        <!--/Table head-->

                        <!--Table body-->
                        <tbody>                            
                            <!--First row-->
                            <script>console.log({!! $stadium_customer->tmp_customer_stadium  !!});</script>
                            <script>console.log({!! $stadium_customer->tmp_customer_stadium[0]->customer  !!});</script>
                            @foreach($stadium_customer->tmp_customer_stadium as $tmp)                           
                            <tr>
                                <form id="form-{{$tmp->member_id}}" class="form-horizontal" role="form" method="POST">
                                    {{ csrf_field() }}
                                    <input id="nickname-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->nickname }}">
                                    <input id="mobile_number-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->mobile_number }}">
                                    <input id="firstname-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->firstname }}">
                                    <input id="lastname-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->lastname }}">
                                    <input id="birthday-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->birthday }}">
                                    <input id="workplace-{{$tmp->member_id}}" type="hidden" value="{{ $tmp->customer->workplace }}">
                                    <td>                                        
                                        {{ $tmp->customer->nickname }}                                        
                                    </td>
                                    <td>
                                        {{ $tmp->customer->mobile_number }}                                        
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>
                                        
                                    </td>
                                    <td>                                       
                                        <button form="form-{{$tmp->member_id}}" id="btn-edit-customer-{{$tmp->member_id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit-customer" type="button" data-toggle="modal" data-target="#modal-edit-customer">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>                                   
                                    
                                        <button id="btn-delete-{{$tmp->member_id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table" type="button" data-original-title="Remove item" data-toggle="tooltip">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>                                       
                                    </td>
                                </form>
                            </tr>
                             @endforeach
                            <!--/First row-->

                        </tbody>
                        <!--/Table body-->
                    </table>
                </div>
                <!--/Account table-->

                <div tabindex="-1" class="modal fade" id="modal-ConfirmDelete" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <!--Content-->
                        <div class="modal-content">
                            <form id="form-del-account" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/delete-account">
                                {{ csrf_field() }}
                                <!--Header-->
                                <div class="modal-header">
                                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    <h4 class="modal-title" id="myModalLabel">คำเตือน!</h4>
                                </div>
                                <!--Body-->
                                <div class="modal-body">
                                    <p>หากทำการลบบัญชีผู้ใช้แล้ว ท่านจะไม่สามารถกู้คืนได้!</p>
                                    <strong>ท่านต้องการลบบัญชี <strong id="str-ask-del"></strong> ใช่หรือไม่?</strong>
                                    <input id="del-user" name="del-user" type="hidden">
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

                <div tabindex="-1" class="modal fade modal-ext" id="modal-add-customer" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/add-customer">
                        {{ csrf_field() }}
                            <!--Content-->
                            <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">
                                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    <h4 class="modal-title" id="myModalLabel">สร้างข้อมูลลูกค้า</h4>
                                </div>
                                <!--Body-->
                                <div class="modal-body text-xs-left">
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-user-o prefix"></i>                                            
                                            <input class="form-control" id="nickname" name="nickname" type="text"  required>
                                            <label for="nickname">ชื่อเล่น</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <i class="fa fa-mobile prefix"></i>
                                            <input class="form-control" id="mobile_number" name="mobile_number" type="text" required>
                                            <label for="mobile_number">เบอร์โทร</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-user prefix"></i>
                                            <input class="form-control" id="firstname" name="firstname" type="text">
                                            <label for="firstname">ชื่อ</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <i class="fa fa-users prefix"></i>
                                            <input class="form-control" id="lastname" name="lastname" type="text">
                                            <label for="lastname">นามสกุล</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-calendar prefix"></i>
                                            <input class="form-control datepicker" id="birthday" name="birthday" type="text">
                                            <label for="birthday">วันเกิด</label>
                                        </div>                                        
                                        <div class="form-group">
                                            <fieldset class="form-group">
                                                <input name="sex" type="radio" id="male" checked="checked">
                                                <label for="male">ชาย</label>
                                            </fieldset>

                                            <fieldset class="form-group">
                                                <input name="sex" type="radio" id="female">
                                                <label for="female">หญิง</label>
                                            </fieldset>
                                        </div>
                                    </div>   
                                    <div class="form-inline">
                                        <div class="md-form">
                                            <i class="fa fa-briefcase prefix"></i>
                                            <input class="form-control" id="workplace" name="workplace" type="text"></input>
                                            <label for="workplace">ที่ทำงาน</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-car prefix"></i>
                                            <input class="form-control" id="visited" name="visited" type="text"></input>
                                            <label for="visited">จำนวนครั้งที่เคยมา</label>
                                        </div>

                                        <div class="form-group md-form">
                                            <i class="fa fa-clock-o prefix"></i>
                                            <input class="form-control" id="time-often" name="time-often" type="text"></input>
                                            <label for="time-often">เวลาที่มาบ่อย</label>
                                        </div>
                                    </div>
                                </div>
                                <!--Footer-->
                                <div class="modal-footer">
                                    <div class="form-inline" style="color:red;">
                                        <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group">กรุณากรอกชื่อเล่น และเบอร์โทรศัพท์ของลูกค้า</p>
                                        <button class="btn btn-default waves-effect waves-light form-group" type="submit">สร้าง</button>
                                    </div>
                                </div>                            
                            </div>
                            <!--/.Content-->
                        </form>
                    </div>
                </div>

                 <div tabindex="-1" class="modal fade modal-ext" id="modal-edit-customer" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/edit-customer">
                        {{ csrf_field() }}
                            <!--Content-->
                            <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">
                                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    <h4 class="modal-title" id="myModalLabel">สร้างข้อมูลลูกค้า</h4>
                                </div>
                                <!--Body-->
                                <div class="modal-body text-xs-left edit">
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-user-o prefix"></i>                                            
                                            <input class="form-control" id="nickname-edit" name="nickname" type="text"  required>
                                            <label for="nickname">ชื่อเล่น</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <i class="fa fa-mobile prefix"></i>
                                            <input class="form-control" id="mobile_number-edit" name="mobile_number" type="text" readonly="readonly" required>
                                            <label for="mobile_number">เบอร์โทร</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-user prefix"></i>
                                            <input class="form-control" id="firstname-edit" name="firstname" type="text">
                                            <label for="firstname">ชื่อ</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <i class="fa fa-users prefix"></i>
                                            <input class="form-control" id="lastname-edit" name="lastname" type="text">
                                            <label for="lastname">นามสกุล</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-calendar prefix"></i>
                                            <input class="form-control datepicker" id="birthday-edit" name="birthday" type="text">
                                            <label for="birthday">วันเกิด</label>
                                        </div>                                        
                                        <div class="form-group">
                                            <fieldset class="form-group">
                                                <input name="sex-edit" id="male-edit" type="radio" checked="checked">
                                                <label for="male-edit">ชาย</label>
                                            </fieldset>

                                            <fieldset class="form-group">
                                                <input name="sex-edit" id="female-edit" type="radio">
                                                <label for="female-edit">หญิง</label>
                                            </fieldset>
                                        </div>
                                    </div>   
                                    <div class="form-inline">
                                        <div class="md-form">
                                            <i class="fa fa-briefcase prefix"></i>
                                            <input class="form-control" id="workplace-edit" name="workplace" type="text"></input>
                                            <label for="workplace">ที่ทำงาน</label>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-car prefix"></i>
                                            <input class="form-control" id="visited-edit" name="visited" type="text"></input>
                                            <label for="visited">จำนวนครั้งที่เคยมา</label>
                                        </div>

                                        <div class="form-group md-form">
                                            <i class="fa fa-clock-o prefix"></i>
                                            <input class="form-control" id="time-often-edit" name="time-often" type="text"></input>
                                            <label for="time-often">เวลาที่มาบ่อย</label>
                                        </div>
                                    </div>
                                </div>
                                <!--Footer-->
                                <div class="modal-footer">
                                    <div class="form-inline" style="color:red;">
                                        <i class="fa fa-asterisk" aria-hidden="true"></i>  <p class="form-group">กรุณากรอกชื่อเล่น และเบอร์โทรศัพท์ของลูกค้า</p>
                                        <button class="btn btn-default waves-effect waves-light form-group" type="submit">แก้ไข</button>
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