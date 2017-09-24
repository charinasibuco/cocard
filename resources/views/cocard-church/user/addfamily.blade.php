@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <h3>Family Members</h3>
        <div class="table-main panel panel-default">
            @include('cocard-church.family.form')
        </div>
    </div>
</div>
@endsection
