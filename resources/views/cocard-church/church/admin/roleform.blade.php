@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="table-main panel panel-default">
            <div class="row">
                <div class="col-md-6">
                    <h3>{{ $action_name }} Role</h3>
                </div>
                <div class="col-md-6">
                    <h3>
                        <div class="clearfix">
                            <a href="{{ url('organization/'.$slug.'/administrator/role/') }}" class="btn btn-red float-right" class="btn btn-darkblue btn-lg float-right">
                                Cancel
                            </a>
                        </div>
                    </h3>
                </div>
            </div>
            <br>
            <div style="margin-bottom:20px;">
                @include('cocard-church.role.form')
            </div>
        </div>
    </div>
</div>
@endsection
