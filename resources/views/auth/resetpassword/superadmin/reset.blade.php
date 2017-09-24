@extends('layouts.app')
@include('includes.navigation')
@section('content')
<div class="bg-banner">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="transparent-box">
                	<h1 style="text-align:center">iSteward</h1>
                    <img src="{{asset('images/icons/user_icon.png')}}" alt="user icon" />
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset-password/'.$token) }}">
                    	<div class="row">
                    	<h4 style="text-align:center;">RESET PASSWORD<h4>
                        @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="col-md-offset-2 col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                        <input type="hidden" name="id" value="0">
                                        <input placeholder="Email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong style="color: red;font-size: 15;"">{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                 <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input placeholder="Password" type="password" class="form-control" name="password" value="{{ old('password') }}">
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong style="color: red;font-size: 15;"">{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                </div>
                                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <input placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation" value={{ old('password_confirmation') }}>
                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong style="color: red;font-size: 15;"">{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                        @endif
                                </div>
                                <div class="form-group">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-green btn-full center">
                                        <i class="fa fa-btn fa fa-refresh"></i>&nbsp;Reset Password
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