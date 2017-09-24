@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="bg-banner">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="transparent-box">
                    <h1 style="text-align:center">{{ ($organization)?$organization->name:''}}</h1>
                    <img src="{{asset('images/icons/user_icon.png')}}" alt="user icon" />
                    @if(Session::has('message'))
                    <div class="col-md-offset-1 col-md-10">
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ Session::get('message') }}
                        </div>
                    </div>
                    @endif
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/organization/login') }}">                   
                        <div class="row">
                            <div class="col-md-offset-2 col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                        <input type="hidden" name="slug" value="{{ ($organization)?$organization->url:'' }}">
                                        <input type="hidden" name="id" value="{{ ($organization)?$organization->id:'' }}">
                                        <input placeholder="Email or Phone" type="text" class="form-control" name="emailphone" value="{{ old('emailphone') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></div>
                                        <input placeholder="Password" type="password" class="form-control" name="password">
                                     {{--    <input type="hidden" name="json" value="true"> --}}
                                        <!-- <div class="input-group-addon">.00</div> -->
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-green btn-full center">
                                        <i class="fa fa-btn fa-sign-in"></i>&nbsp;Login
                                    </button>
                                </div>
                                <div class="form-group">
                                    <a class="btn-link center text-center" href="{{ route('request-resetpassword-link', $slug) }}">Forgot Your Password?</a>
                                    <a class="btn-link center text-center" href="{{ url('/organization/'.$organization->url.'/register') }}">No Account? Register Here.</a>

                                </div>
                                <div class="form-group">
                                        <a href="{{ url('/organization/'.$slug.'/home') }}" class="btn btn-green btn-full">
                                            Continue as Guest
                                        </a>
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
