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
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/send_email') }}">             
                        <div class="row">
                        @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="col-md-offset-2 col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                        <input type="hidden" name="slug" value="{{ ($organization)?$organization->url:'' }}">
                                        <input type="hidden" name="id" value="{{ ($organization)?$organization->id:'' }}">
                                        <input placeholder="Email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong style="color: red;">{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="form-group">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-green btn-full center">
                                        <i class="fa fa-btn fa fa-envelope"></i>&nbsp;Send Password Reset Link
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