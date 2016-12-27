@extends('layouts.master') @section('main')
<main class="pt-6">
    <div class="container text-xs-center">
        <section class="section">
            <h5>Account Manangement </h5>
            <br>

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {{ csrf_field() }}
                <!--Shopping Cart table-->
                <div class="table-responsive">
                    <table class="table product-table">
                        <!--Table head-->
                        <thead>
                            <tr>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th></th>
                            </tr>
                        </thead>
                        <!--/Table head-->

                        <!--Table body-->
                        <tbody>

                            <!--First row-->
                            <tr>
                                <td>Parinya</td>
                                <td>Jankrut</td>
                                <td>iPopular</td>
                                <td>xxxxxx</td>
                                <td>adb@gmail.com</td>
                                <td>Admin</td>
                                <td>
                                    <button title="" class="btn btn-sm btn-primary waves-effect waves-light" type="button"      data-original-title="Remove item" data-toggle="tooltip" data-placement="top">X
                                    </button>
                                </td>
                            </tr>
                            <!--/First row-->

                            <!--Third row-->
                            <tr>
                                
                                <td>
                                    <input class="form-control" id="firstname" name="firstname" type="text" placeholder="Firstname" value="" required>
                                    @if ($errors->has('firstname'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('firstname') }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <input class="form-control" id="lastname" name="lastname" type="text" placeholder="Lastname" value="" required>
                                    @if ($errors->has('lastname'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('lastname') }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <input class="form-control" id="username" name="username" type="text" placeholder="Username" value="" required>
                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <input class="form-control" id="password" name="password" type="password" placeholder="Password" value=""  required>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <input class="form-control" id="email" name="email" type="email" placeholder="Email" value="" required>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <select id="user_role_id" name="user_role_id" class="form-control select-border" autocomplete="off">
                                        <option value="">Please select role</option>
                                        <option value="1">Staff</option>
                                        <option value="2">Admin</option>
                                        <option value="3">Owner</option>
                                    </select>
                                </td>
                                <td>
                                    <button title="" class="btn btn-sm btn-primary waves-effect waves-light" type="button"      data-original-title="Remove item" data-toggle="tooltip" data-placement="top">X
                                    </button>
                                </td>
                                
                            </tr>
                            <!--/Third row-->

                            <!--Fourth row-->
                            <tr>
                                <td colspan="5"></td>
                                <td colspan="3"><button class="btn btn-primary waves-effect waves-light" type="submit"><i class="fa fa-plus right"></i>   Add</button></td>
                            </tr>
                            <!--/Fourth row-->

                        </tbody>
                        <!--/Table body-->
                    </table>
                </div>
                <!--/Shopping Cart table-->
            </form>
        </section>
        
    </div>
</main>
@stop