@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        @if(count($errors) > 0)
        <div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @foreach ($errors->all() as $error)
        @if(strpos($error, 'required') == false)
        <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
        @endforeach
        @endif
        <div class="row">
            <div class="col-md-6">
                <h3>Members Directory</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        @can('add_member')
                        <a href="{{ url('/organization/'.$slug.'/administrator/members/add-member')}}" class="btn btn-darkblue float-right">
                            Add Member&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                        @endcan
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
            @include('cocard-church.members.index')
        </div>
    </div>
</div>
@endsection
