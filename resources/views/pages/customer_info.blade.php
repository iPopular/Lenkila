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
            <h5>ข้อมูลลูกค้า </h5>
                    
                <div class="text-xs-right">
                    <button id="btn-add" class="btn btn-md btn-info waves-effect waves-light btn-table btn-edit" type="button" data-toggle="modal" data-target="#modal-add"><i class="fa fa-plus" aria-hidden="true"></i>   สร้าง</button>
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
                            @foreach($stadium_customer->tmp_customer_stadium as $customer)                           
                            <tr>
                                <form id="form-{{$customer->id}}" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/update-account/{{ $customer->username }}">
                                    {{ csrf_field() }}
                                    <td>
                                        <div class="div-row div-row-{{$customer->id}}" style="display:block;">
                                            {{ $customer->nickname }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$customer->id}}" style="display:block;">
                                            {{ $customer->mobile_number }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$customer->id}}" style="display:block;">
                                            
                                        </div>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$customer->id}}" style="display:block;">
                                            
                                        </div>
                                    </td>
                                    <td>
                                        @if($user_role->update_customer == 1)
                                            <button form="form-{{$customer->id}}" id="btn-edit-{{$customer->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-edit-{{$customer->id}}" class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                        @if($user_role->delete_customer == 1)
                                            <button id="btn-delete-{{$customer->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-delete-{{$customer->id}}" class="fa fa-trash-o" aria-hidden="true"></i>
                                            </button>
                                        @endif
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

                <div tabindex="-1" class="modal fade modal-ext" id="modal-add" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/add-customer }}">
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
                                            <i class="fa fa-user-o prefix"></i>                                            
                                            <input class="form-control" id="nickname" name="nickname" type="text"  required>
                                            <label for="nickname">ชื่อเล่น</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <fieldset class="form-group">
                                                <input name="group1" id="male" name="male" type="radio" checked="checked">
                                                <label for="male">ชาย</label>
                                            </fieldset>

                                            <fieldset class="form-group">
                                                <input name="group1" id="female" name="female" type="radio">
                                                <label for="female">หญิง</label>
                                            </fieldset>
                                        </div>
                                    </div>
                                    
                                    <div class="form-inline">
                                        <div class="form-group md-form">
                                            <i class="fa fa-calendar prefix"></i>
                                            <input class="form-control datepicker" id="datepicker-birthday" type="text">
                                            <label for="datepicker-birthday">วันเกิด</label>
                                        </div>
                                        <div class="form-group md-form">
                                            <i class="fa fa-mobile prefix"></i>
                                            <input class="form-control" id="moblie-number" name="mobile-number" type="text" required>
                                            <label for="mobile-number">เบอร์โทร</label>
                                        </div>
                                    </div>

                                    <div class="md-form">
                                        <i class="fa fa-briefcase prefix"></i>
                                        <input class="form-control" id="workplace" name="workplace" type="text"></input>
                                        <label for="workplace">ที่ทำงาน</label>
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
            
        </section>
        
    </div>
</main>
@stop