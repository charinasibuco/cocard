@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>
                    User Profile
                </h3>
            </div>
            <div class="col-md-6">
                    <h3>
                        <div class="clearfix">
                            <a href="{{ url('/organization/'.$slug.'/user/family')}}"  class="btn btn-darkblue float-right">
                                View Family
                            </a>
                        </div>
                    </h3>
            </div>
        </div>
        <div class="panel panel-primary panel-information" style="margin-top:10px;">
            <div class="panel-heading personal-information">
                <div class="row">
                    <div class="col-md-6" style="font-size: 24px;">
                        <i class="fa fa-user" aria-hidden="true" style="color:#fff;"></i> &nbsp;Details
                    </div>
                    <div class="col-md-6">
                        <a href="{{ url('organization/'.$slug.'/user/edit-profile/'.$user->id) }}" class="float-right">
                            <i class="fa fa-pencil color-blue"  style="color:#fff; font-size: 24px;" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body"> 
                <div class="row profile_page">
                    <div class="col-md-2 col-xs-12">
                        <img src="{{ asset('images/'.(is_null(Auth::user()->image) ? 'user.png' : Auth::user()->image )) }}">
                    </div>
                    <div class="col-md-10 col-xs-12">
                        <h1>{{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}</h1>
                        <h4>Birthdate: <strong>{{Carbon\Carbon::parse($user->birthdate)->format('n/d/Y')}}</strong></h4>
                        <h4>Gender: <strong>{{ $user->gender }}</strong></h4>
                        <h4>Marital Status: <strong>{{ $user->marital_status }}</strong></h4>
                        <h4>Phone: <strong>{{ $user->phone }}</strong></h4>
                        <h4>Email: <strong>{{ $user->email }}</strong></h4>
                        <h4>Address: <strong>{{ $user->address }}</strong></h4>
                        <h4>City: <strong>{{ $user->city }}</strong></h4>
                        <h4>State: <strong>{{ $user->state }}</strong></h4>
                        <h4>Zipcode: <strong>{{ $user->zipcode }}</strong></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
