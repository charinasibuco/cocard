@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Users List</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('/organization/'.$slug.'/administrator/users/create')}}"  class="btn btn-darkblue float-right">
                            Add User&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="table-main panel panel-default">
            @include('cocard-church.user.index_back_office')
        </div>
    </div>
</div>
@endsection
