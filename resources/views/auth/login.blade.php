@extends('layouts.master') @section('main')
<main class="pt-8">
  <div class="container text-xs-center">
    <section class="section">
      <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}
            <!--Author box-->
            <div class="author-box text-xs-left">

              <!--Name-->
              <h3 class="h3-responsive text-xs-center">LENKILA</h3>
              <hr>
              <br>

              <div class="form-login text-xs-left">
                <div class="md-form{{ $errors->has('username') ? ' has-error' : '' }}">
                  <i class="fa fa-user prefix"></i>
                  <input class="form-control" id="username" name="username" type="text" required>
                  <label class="active" for="username">ชื่อผู้ใช้</label>
                  @if ($errors->has('username'))
                    <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                    </span>
                  @endif
                </div>
                <br>
                <div class="md-form{{ $errors->has('password') ? ' has-error' : '' }}">
                  <i class="fa fa-lock prefix"></i>
                  <input class="form-control" id="password" name="password" type="password" required>
                  <label class="active" for="password">รหัสผ่าน</label>
                  @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
                </div>

                <div class="text-xs-center">
                  <button class="btn btn-primary waves-effect waves-light" type="submit">เข้าสู่ระบบ</button>
                </div>
              </div>
            </div>
          </form>
        </div>

      </div>
  </div>
  <!--/.Author box-->

  </div>
</main>
@stop