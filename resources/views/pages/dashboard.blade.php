@extends('layouts.master') @section('main')
<main class="pt-8">
  <div class="container text-xs-center">
    <section class="section">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <div class="author-box text-xs-left">
            <div class="row">

              <!--Name-->
              <h3 class="h3-responsive">แผงควบคุม</h3>
              <br>

              <!--Author Data-->
              <div class="col-md-12">
                <div class="form-inline">
                    <div class="md-form">
                        <i class="fa fa-flag prefix"></i>
                        <input class="form-control" id="staduim_name" name="staduim_name" type="text" readonly="readonly" value="{{$stadium}}">
                        <label for="staduim_name">ชื่อสเตเดียม</label>
                    </div>
                    <!--<div class="form-group">
                        <button class="btn btn-default waves-effect waves-light form-group" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> บันทึก</button>
                    </div>-->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>
@stop