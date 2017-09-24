@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <h3>
                <div class="clearfix">
                    @can('edit_member')
                    <a href="{{ url('/organization/'.$slug.'/administrator/members/edit-member', $id) }}" class="btn btn-darkblue float-right">
                        <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit
                    </a>
                    @endcan
                    <a href="{{ url('/organization/'.$slug.'/administrator/members')}}" class="btn btn-darkblue float-left">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                    </a>
                </div>
            </h3>
        </div>
        <div class="panel panel-primary panel-information" style="margin-top:10px;">
            <div class="panel-heading personal-information"><i class="fa fa-user" aria-hidden="true"></i> &nbsp;Details</div>
            <div class="panel-body">
                <div class="row profile_page">
                    <div class="col-md-2 col-xs-12">
                        <img src="{{ asset('images/'.(is_null($member->image) ? 'user.png' : $member->image )) }}">
                    </div>
                    <div class="col-md-10 col-xs-12">
                        <h1>{{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}</h1>
                        <h4>Organization: <strong>{{ $organization->name }}</strong></h4>
                        <h4>Birthdate: <strong>{{Carbon\Carbon::parse($member->birthdate)->format('n/d/Y')}}</strong></h4>
                        <h4>Gender: <strong>{{ $member->gender }}</strong></h4>
                        <h4>Marital Status: <strong>{{ $member->marital_status }}</strong></h4>
                        <h4>Phone: <strong>{{ $member->phone }}</strong></h4>
                        <h4>Email: <strong>{{ $member->email }}</strong></h4>
                        <h4>Address: <strong>{{ $member->address }}</strong></h4>
                        <h4>City: <strong>{{ $member->city }}</strong></h4>
                        <h4>State: <strong>{{ $member->state }}</strong></h4>
                        <h4>Zipcode: <strong>{{ $member->zipcode }}</strong></h4>
                    </div>
                </div> 
                </div>
            </div>
        </div>
    </div>
    @endsection
