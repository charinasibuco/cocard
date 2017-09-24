@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<br>
<div class="d-content calendar">
    <div class="margin-mob-top">
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('message') }}
        </div>
        @endif
        @include('cocard-church.event.calendar')
        @include('cocard-church.donation.modal')
    </div>
</div>
@endsection
