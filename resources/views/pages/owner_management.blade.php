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
            <h5>การจัดการบัญชีผู้ใช้ </h5>
            <br>
                
            <!--Owner table-->
            <div class="table-responsive">
                <table class="table" data-page-length="5">
                    <!--Table head-->
                    <thead>
                        <tr>
                            <th>ชื่อ - นามสกุล  </th>
                            <th>ชื่อผู้ใช้  </th>
                            <th>รหัสผ่าน  </th>
                            <th>อีเมล  </th>
                            <th>สนาม  </th>
                            <th>สิทธิ์  </th>
                            <th></th>
                        </tr>
                    </thead>
                    <!--/Table head-->

                    <!--Table body-->
                    <tbody>
                        
                        <!--First row-->
                        @foreach($owner_users as $user)
                            @if(Auth::user()->role_id > $user->role_id || Auth::user()->role_id == 4)                           
                                <tr>
                                    <form id="form-{{$user->id}}" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/owner_management/update-account/{{ $user->username }}">
                                        {{ csrf_field() }}
                                        <td>
                                            <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                                {{ $user->firstname }} {{ $user->lastname }}
                                            </div>
                                            <input form="form-{{$user->id}}" class="form-control form-table input-row input-row-{{$user->id}}" id="name-{{$user->id}}" name="name" type="text" placeholder="Name" value="{{$user->firstname}} {{$user->lastname}}" style="display:none;" required>
                                        </td>
                                        <td>
                                            <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                                {{ $user->username }}
                                            </div>
                                            <input form="form-{{$user->id}}" class="form-control form-table input-row input-row-{{$user->id}}" id="username-{{$user->id}}" name="username" type="text" placeholder="Username" value="{{$user->username}}" style="display:none;" required>
                                        </td>
                                        <td>
                                            <div id="div-btn-password-{{$user->id}}" class="div-btn-password" style="display:block;">
                                                <button title="" id="btn-password-{{$user->id}}" class="btn btn-sm btn-primary waves-effect waves-light btn-password" type="button" data-original-title="Change Password" data-toggle="tooltip" data-placement="top" disabled="true">เปลี่ยนรหัสผ่าน
                                                </button>
                                            </div>
                                            <div id="div-password-{{$user->id}}" class="div-password" style="display:none;">
                                                <input form="form-{{$user->id}}" class="form-control form-table input-row input-password" id="password-{{$user->id}}" name="password" type="password" placeholder="Password" value="">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                                {{ $user->email }}
                                            </div>
                                            <input form="form-{{$user->id}}" class="form-control form-table input-row input-row-{{$user->id}}" id="email-{{$user->id}}" name="email" type="email" placeholder="Email" value="{{$user->email}}" style="display:none;" required>
                                        </td>
                                        <td>
                                            <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                                {{ $user->stadium_name }}
                                            </div>
                                            <select form="form-{{$user->id}}"  id="stadium-{{$user->id}}" name="stadium_id" class="form-control select-border form-table input-row input-row-{{$user->id}}" autocomplete="off"  style="display:none;">
                                                <option value="">กรุณาเลือกสนาม</option>
                                                @foreach($stadiums as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                            <script>
                                                $("select#stadium-{{$user->id}} option[value='{{ $user->stadium_id }}']").attr("selected", "selected");
                                            </script>
                                        </td>
                                        <td>
                                            <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                                {{ $user->role_name }}
                                            </div>
                                            <select form="form-{{$user->id}}"  id="role_id-{{$user->id}}" name="role_id" class="form-control select-border form-table input-row input-row-{{$user->id}}" autocomplete="off"  style="display:none;">
                                                <option value="">กรุณาเลือกสิทธิ์</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                            <script>
                                                $("select#role_id-{{$user->id}} option[value='{{ $user->role_id }}']").attr("selected", "selected");
                                            </script>
                                        </td>
                                        <td>

                                            <button form="form-{{$user->id}}" id="btn-edit-{{$user->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-edit-{{$user->id}}" class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>

                                            <button id="btn-delete-{{$user->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-delete-{{$user->id}}" class="fa fa-trash-o" aria-hidden="true"></i>
                                            </button>

                                        </td>
                                    </form>
                                </tr>
                            @endif
                        @endforeach
                        <!--/First row-->
                        
                        
                        <!--Add row-->
                        <tr>
                            <form id="form-add-owner" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/owner_management/add-owner">
                            {{ csrf_field() }}
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="name" name="name" type="text" placeholder="ชื่อ - สกุล" value="" required>
                                </td>                                    
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="username" name="username" type="text" placeholder="ชื่อผู้ใช้" value="" required>
                                </td>
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="password" name="password" type="password" placeholder="รหัสผ่าน" value=""  required>
                                </td>
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="email" name="email" type="email" placeholder="อีเมล" value="" required>
                                </td>
                                <td>
                                    <select form="form-add-owner" id="stadium_id" name="stadium_id" class="form-control select-border" autocomplete="off">
                                        <option value="">กรุณาเลือกสนาม</option>
                                        @foreach($stadiums as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select form="form-add-account" id="role_id" name="role_id" class="form-control select-border" autocomplete="off">
                                        <option value="">กรุณาเลือกสิทธิ์</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button form="form-add-owner" class="btn btn-primary waves-effect waves-light" type="submit"><i class="fa fa-plus right"></i></button>
                                </td>
                            </form>
                        </tr>
                        <!--/Add row-->
                        

                    </tbody>
                    <!--/Table body-->
                </table>
            </div>
            <!--/Owner table-->


            <div tabindex="-1" class="modal fade" id="modal-ConfirmDelete" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                <div class="modal-dialog" role="document">
                    <!--Content-->
                    <div class="modal-content">
                        <form id="form-del-owner" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/owner_management/delete-owner">
                            {{ csrf_field() }}
                            <!--Header-->
                            <div class="modal-header">
                                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                <h4 class="modal-title  modal-title-important" id="myModalLabel">คำเตือน!</h4>
                            </div>
                            <!--Body-->
                            <div class="modal-body">
                                <p class="text-important">หากทำการลบบัญชีผู้ใช้แล้ว ท่านจะไม่สามารถกู้คืนได้!</p>
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
            
        </section>
        
    </div>
</main>
@stop