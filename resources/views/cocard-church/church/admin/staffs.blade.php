@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section("style")
<style>
/* The switch - the box around the slider */
.switch {
    position: relative;
    display: inline-block;
    width: 30px;
    height: 17px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .2s;
    transition: .2s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 13px;
    width: 13px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    -webkit-transition: .2s;
    transition: .2s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
    -webkit-transform: translateX(13px);
    -ms-transform: translateX(13px);
    transform: translateX(13px);
}

/* Rounded sliders */
.slider.round {
    border-radius: 17px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>
@endsection
@section('content')
<div class="d-content">
    <div class="margin-mob-top" style="margin-bottom:10px;"></div>
    <div class="row">
        <div class="col-md-6">
            <h3>@if($display == "form") @if(isset($id))Edit @else Add @endif @endif Staff</h3>
        </div>
        <div class="col-md-6">
            <h3>
                <div class="clearfix">
                    @if($display == "form")
                    <a href="{{ $back_url }}" class="btn btn-red float-right">Cancel</a>
                    @else
                    @can('add_staff')
                    <a href="{{ url('/organization/'.$slug.'/administrator/staff/create')}}" class="btn btn-darkblue float-right">
                        Add Staff&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    @endcan
                    @endif
                </div>
            </h3>
        </div>
    </div>
    <div class="row">
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('message') }}
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-12">

            @if($display == "list")
            @include('cocard-church.user.templates.user_list')
            @endif
            @if($display == "form")
            <form id="user_form" role="form" enctype="multipart/form-data" action="{{ $action }}" method="post" class="form">
                <div class="col-sm-12">
                    <input class="form-control required" type="hidden" name="organization_id" value="{{ $organization_id }}">
                    @include('cocard-church.user.templates.user_fields')
                    {!! csrf_field() !!}
                    <div class="clearfix">
                        <div class="pull-right">
                            <input type="submit" class="btn btn-darkblue" value="Submit">
                            <a href="{{ $back_url }}" class="btn btn-red">Cancel</a>
                        </div>
                    </div>
                    <br>
                </div>
            </form>
            @endif
        </div>
    </div>

</div>
@endsection
