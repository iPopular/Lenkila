@extends('layouts.master') @section('main')
<main class="pt-6">
    <div class="container text-xs-center">
        <section class="section">
            @if(Session::has('success_msg'))

                    <strong><h2>{{ Session::get('success_msg') }}</h2></strong>

                <!--toastr.info('Hi! I am info message.');-->
            @elseif (Session::has('error_msg'))

                    <strong><h2>{{ Session::get('error_msg') }}</h2></strong>

            @endif
            <h5>Account Manangement </h5>
            <br>          
                
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
                            @foreach($users as $user)                           
                            <tr>
                                <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/update-account/{{ $user->username }}">
                                    {{ csrf_field() }}
                                    <td>
                                        <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                            {{ $user->firstname }}
                                        </div>
                                        <input class="form-control form-table input-row input-row-{{$user->id}}" id="firstname-{{$user->id}}" name="firstname" type="text" placeholder="Firstname" value="{{$user->firstname}}" style="display:none;" required>
                                        @if ($errors->has('firstname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('firstname') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                            {{ $user->lastname }}
                                        </div>
                                        <input class="form-control form-table input-row input-row-{{$user->id}}" id="lastname-{{$user->id}}" name="lastname" type="text" placeholder="Lastname" value="{{$user->lastname}}" style="display:none;" required>
                                        @if ($errors->has('lastname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('lastname') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                            {{ $user->username }}
                                        </div>
                                        <input class="form-control form-table input-row input-row-{{$user->id}}" id="username-{{$user->id}}" name="username" type="text" placeholder="Username" value="{{$user->username}}" style="display:none;" required>
                                        @if ($errors->has('username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button title="" id="btn-password-{{$user->id}}" class="btn btn-sm btn-primary waves-effect waves-light btn-password" type="button" data-original-title="Change Password" data-toggle="tooltip" data-placement="top">Change Password
                                        </button>
                                        <div id="div-password-{{$user->id}}" class="div-password" style="display:none;">
                                            <input class="form-control input-password" id="password-{{$user->id}}" name="password" type="password" placeholder="Password" value="">
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="div-row div-row-{{$user->id}}" style="display:block;">
                                            {{ $user->email }}
                                        </div>
                                        <input class="form-control form-table input-row input-row-{{$user->id}}" id="email-{{$user->id}}" name="email" type="email" placeholder="Email" value="{{$user->email}}" style="display:none;" required>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <!--<div class="div-row div-row-{{$user->id}}" style="display:block;">
                                            
                                        </div>-->
                                        <select id="role_id-{{$user->id}}" name="role_id" class="form-control select-border" autocomplete="off" disabled>
                                            <option value="">Please select role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <script>
                                            $('#role_id-{{$user->id}}').val({{ $user->role_id }});
                                        </script>
                                    </td>
                                    <td>
                                        <button id="btn-edit-{{$user->id}}" class="btn btn-xs btn-warning waves-effect waves-light btn-table btn-edit" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i id="icon-edit-{{$user->id}}" class="fa fa-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button title="" class="btn btn-xs btn-danger waves-effect waves-light btn-table" type="button" data-original-title="Remove item" data-toggle="tooltip" data-placement="top"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </form>
                            </tr>
                             @endforeach
                            <!--/First row-->

                            <!--Add row-->
                            <tr>
                                <form class="form-horizontal" role="form" method="POST" action="/{{ $stadium }}/add-account">
                                {{ csrf_field() }}
                                    <td>
                                        <input class="form-control form-table" id="firstname" name="firstname" type="text" placeholder="Firstname" value="" required>
                                        @if ($errors->has('firstname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('firstname') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input class="form-control form-table" id="lastname" name="lastname" type="text" placeholder="Lastname" value="" required>
                                        @if ($errors->has('lastname'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('lastname') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input class="form-control form-table" id="username" name="username" type="text" placeholder="Username" value="" required>
                                        @if ($errors->has('username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input class="form-control form-table" id="password" name="password" type="password" placeholder="Password" value=""  required>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <input class="form-control form-table" id="email" name="email" type="email" placeholder="Email" value="" required>
                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <select id="role_id" name="role_id" class="form-control select-border" autocomplete="off">
                                            <option value="">Please select role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('role_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('role_id') }}</strong>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-primary waves-effect waves-light" type="submit"><i class="fa fa-plus right"></i></button>
                                    </td>
                                </form>
                            </tr>
                            <!--/Add row-->

                        </tbody>
                        <!--/Table body-->
                    </table>
                </div>
                <!--/Shopping Cart table-->
            
        </section>
        
    </div>
</main>
@stop