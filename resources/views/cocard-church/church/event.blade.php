@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
@if($user == "")
  <div class="container calendar">
    <div class="clearfix" style="margin:20px 0;">
        <div class="pull-right">
            <a href="{{ url('/organization/'.$slug.'/home') }}" class="btn btn-green">
                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
            </a>
        </div>
    </div>
@else
<div class="d-content margin-mob-top calendar">
@endif

    @include('cocard-church.event.calendar')
    @include('cocard-church.donation.modal')
</div>
@endsection
