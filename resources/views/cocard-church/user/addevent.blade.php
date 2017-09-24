@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <h3 class="permissiontitle">Add Event</h3>
        <div class="table-main panel panel-default">
            <div class="clearfix btn-right margin-bot-10">
                <a href="{{ url('/organization/'.$slug.'/user/events')}}">
                    <button type="submit" class="btn btn-blue float-right">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
                    </button>
                </a>
            </div>
            <div>
                @include('cocard-church.event.form')
            </div>
        </div>
    </div>
</div>
@endsection
