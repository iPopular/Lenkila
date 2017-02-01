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
            <h2>รายการวันนี้ </h2>
            <table id="table_reserve_today" class="table table-striped table-hover" data-page-length="5" cellspacing="0">
                <!--Table head-->
                <thead>
                    <tr>
                        <th></th>
                        <th>ชื่อเล่น  </th>
                        <th>หมายเลขโทรศัพท์  </th>
                        <th>สนาม  </th>
                        <th>เวลาเริ่มต้น  </th>
                        <th>เวลาสิ้นสุด  </th>
                        <th>ราคา  </th>
                        <th></th>
                    </tr>
                </thead>
                <!--/Table head-->

                <!--Table body-->
                <tbody>                            
                    <!--First row-->
                    
                    @foreach($events as $event)                                               
                    <tr id="{{$event['id']}}">
                        <form id="form-{{$event['id']}}" class="form-horizontal" role="form" method="POST">
                            {{ csrf_field() }}                                    
                            
                            <input id="reserve-{{$event['id']}}" type="hidden" value="{{ $event['id'] }}">
                            <input id="field_price-{{$event['id']}}" type="hidden" value="{{ $event['field_price'] }}">
                            <input id="water_price-{{$event['id']}}" type="hidden" value="{{ $event['water_price'] }}">
                            <input id="supplement_price-{{$event['id']}}" type="hidden" value="{{ $event['supplement_price'] }}">
                            <input id="discount_price-{{$event['id']}}" type="hidden" value="{{ $event['discount_price'] }}">
                            <td>
                                @if($event['status'] == 1)
                                    <input type="checkbox" id="checkbox-{{$event['id']}}">
                                    <label for="checkbox-{{$event['id']}}"></label>
                                @else
                                    <input type="checkbox" id="checkbox" disabled="diabled">
                                    <label for="checkbox"></label>
                                @endif
                                <!--</fieldset>-->
                            </td>
                            <td>                                        
                                {{ $event['nickname'] }}                                        
                            </td>
                            <td>                                        
                                {{ $event['mobile_number'] }}                                        
                            </td>
                            <td>
                                {{ $event['field_name'] }}                                        
                            </td>
                            <td>
                                {{ $event['start_time'] }}   
                            </td>
                            <td>
                                {{ $event['end_time'] }}   
                            </td>
                            <td>
                                {{ $event['field_price'] }}   
                            </td>
                            <td>
                            @if($event['status'] == 1)                                      
                                <button form="form-{{$event['id']}}" id="btn-edit-paid-{{$event['id']}}" class="btn btn-xs btn-success waves-effect waves-light btn-table btn-edit-paid" type="button" data-toggle="modal" data-target="#modal-paid-reserve" >
                                    <i class="fa fa-money" aria-hidden="true"></i>
                                </button>
                            @else
                                <button class="btn btn-xs btn-success waves-effect waves-light btn-table btn-edit-paid" type="button" disabled="disabled" >
                                    <i class="fa fa-money" aria-hidden="true"></i>
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

            <div tabindex="-1" class="modal fade" id="modal-paid-reserve" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">ชำระเงิน</h4>
                        </div>
                        <div class="modal-body text-xs-left reserve">
                        <form id="paid-reserve" class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/today-paid-reserve">
                            {{ csrf_field() }}
                            <input type="hidden" id="hddReserveId" name="hddReserveId" />
                            <div class="row">
                                <div class="col-md-6 md-form">
                                    <i class="fa fa-columns prefix"></i>
                                    <input class="form-control price" id="field_price" name="field_price" type="text" onkeyup="sumPrice();">
                                    <label for="resource">ค่าสนาม</label>
                                </div>
                                <div class="col-md-6 md-form">
                                    <i class="fa fa-tint prefix"></i>
                                    <input class="form-control price" id="water_price" name="water_price" type="text" onkeyup="sumPrice();">
                                    <label for="water_price">ค่าน้ำ</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 md-form">
                                    <i class="fa fa-plus-square-o prefix"></i>
                                    <input class="form-control price" id="supplement_price" name="supplement_price" type="text" onkeyup="sumPrice();">
                                    <label for="supplement_price">อื่น ๆ</label>
                                </div>
                                <div class="col-md-6 md-form">
                                    <i class="fa fa-scissors prefix"></i>
                                    <input class="form-control" id="discount" name="discount" type="text">
                                    <label for="discount">ส่วนลด</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6 md-form">
                                    <i class="fa fa-money prefix"></i>
                                    <input class="form-control" id="total_price" name="total_price" type="text" readonly="readonly">
                                    <label for="total_price">รวม</label>
                                </div>
                            </div>
                        </form>
                        </div>
                        <div class="modal-footer">
                        <button form="paid-reserve" type="submit" class="btn btn-success"><i class="fa fa-money" aria-hidden="true"></i>  ชำระ</button>
                        <!--<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> ยกเลิก</button>-->
                        </div>
                    </div>
                </div>
            </div>

        </section>
        
    </div>
</main>
@stop