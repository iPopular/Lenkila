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
                            <th>ชื่อสนาม  </th>
                            <th>รายละเอียด  </th>
                            <th>ที่อยู่  </th>
                            <th>เวลาเปิด  </th>
                            <th>เวลาปิด  </th>
                            <th></th>
                        </tr>
                    </thead>
                    <!--/Table head-->

                    <!--Table body-->
                    <tbody>
                        
                        <!--First row-->
                        @foreach($stadiums as $stadium)                         
                            <tr>
                                <form id="form-{{$stadium->id}}" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/stadium_management/update-stadium">
                                    {{ csrf_field() }}
                                    <input form="form-{{$stadium->id}}" id="hddStadiumId-{{$stadium->id}}" name="hddStadiumId" type="hidden" value="{{$stadium->id}}">
                                    <td>
                                        <div class="div-row div-row-{{$stadium->id}}" style="display:block;">
                                            {{ $stadium->name }}
                                        </div>
                                        <input form="form-{{$stadium->id}}" class="form-control form-table input-row input-row-{{$stadium->id}}" id="name-{{$stadium->id}}" name="name" type="text" placeholder="Name" value="{{$stadium->name}}" style="display:none;" required>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$stadium->id}}" style="display:block;">
                                            {{ $stadium->detail }}
                                        </div>
                                        <input form="form-{{$stadium->id}}" class="form-control form-table input-row input-row-{{$stadium->id}}" id="detail-{{$stadium->id}}" name="detail" type="text" placeholder="Detail" value="{{$stadium->detail}}" style="display:none;" required>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$stadium->id}}" style="display:block;">
                                            {{ $stadium->address }}
                                        </div>
                                        <input form="form-{{$stadium->id}}" class="form-control form-table input-row input-row-{{$stadium->id}}" id="address-{{$stadium->id}}" name="address" type="text" placeholder="Address" value="{{$stadium->address}}" style="display:none;" required>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$stadium->id}}" style="display:block;">
                                            {{ $stadium->open_time }}
                                        </div>
                                        <input form="form-{{$stadium->id}}" class="form-control form-table input-row input-row-{{$stadium->id}}" id="startTime-{{$stadium->id}}" name="open_time" value="{{$stadium->open_time}}" type="time" style="display:none; width:100%;" required>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$stadium->id}}" style="display:block;">
                                            {{ $stadium->close_time }}
                                        </div>
                                        <input form="form-{{$stadium->id}}" class="form-control form-table input-row input-row-{{$stadium->id}}" id="closeTime-{{$stadium->id}}" name="close_time" value="{{$stadium->close_time}}" type="time" style="display:none; width:100%;" required>
                                    </td>
                                    <td>
                                        <button form="form-{{$stadium->id}}" id="btn-edit-{{$stadium->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-edit-{{$stadium->id}}" class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>

                                        <button id="btn-delete-{{$stadium->id}}" class="btn btn-xs btn-danger waves-effect waves-light btn-table btn-delete" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-delete-{{$stadium->id}}" class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>

                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        <!--/First row-->
                        
                        
                        <!--Add row-->
                        <tr>
                            <form id="form-add-owner" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/stadium_management/add-stadium">
                            {{ csrf_field() }}
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="name" name="name" type="text" placeholder="ชื่อสนาม" value="" required>
                                </td>                                    
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="detail" name="detail" type="text" placeholder="รายละเอียด" value="" required>
                                </td>
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="address" name="address" type="text" placeholder="ที่อยู่" value=""  required>
                                </td>
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="open_time" name="open_time" type="time" value="" required>
                                </td>
                                <td>
                                    <input form="form-add-owner" class="form-control form-table" id="close_time" name="close_time" type="time" value="" required>
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
                        <form id="form-del-stadium" class="form-horizontal" role="form" method="POST" action="/{{ $stadium_name }}/stadium_management/delete-stadium">
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
                                <p class="text-important">หากทำการลบสนามแล้ว ท่านจะไม่สามารถกู้คืนได้!</p>
                                <strong>ท่านต้องการลบสนาม <strong id="str-ask-del"></strong> ใช่หรือไม่?</strong>
                                <input id="del-stadium" name="del-stadium" type="hidden">
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