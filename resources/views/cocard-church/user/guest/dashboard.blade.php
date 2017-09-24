@extends('layouts.app')
@include('cocard-church.user.guest.navigation')
@section('content')
<div class="d-content administrators">
    <div class="margin-mob-top">
        <br>
        <br>
        <h1 style="text-align:center;">{{ $organization->name }}</h1>
        <h1 style="text-align:center;">Dashboard</h1>
    </div>
</div>
@endsection
