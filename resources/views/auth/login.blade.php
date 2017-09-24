@extends('layouts.app')
@include('includes.navigation')
@section('content')
<div class="bg-banner">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="transparent-box">
                    <img src="{{asset('images/icons/user_icon.png')}}" alt="user icon" />
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/superadmin-login') }}">
                        {!! csrf_field() !!}
                        <div class="row">
                         @if(Session::has('error'))
                         <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ Session::get('error') }}
                        </div>
                        @endif
                            <div class="col-md-offset-2 col-md-8"> <h4 style="text-align:center;">SUPER ADMIN LOGIN<h4>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                        <input type="hidden" name="organization_id" value="0">
                                        <input placeholder="Email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                    </div>
                                    @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                                        <input placeholder="Password" type="password" class="form-control" name="password" required>
                                        <!-- <div class="input-group-addon">.00</div> -->
                                    </div>
                                    @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <a class="btn btn-link" href="{{ url('/password/send-reset-password-link') }}">Forgot Your Password?</a>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <br><br><br>
                                    <button type="submit" class="btn btn-primary btn-green center">
                                        <i class="fa fa-btn fa-sign-in"></i>&nbsp;Login
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
