@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Family</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('/organization/'.$slug.'/administrator/family/create')}}" class="btn btn-darkblue float-right">
                            Add Family Group&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            <table class="table table-striped" id="tabledata">
                <thead class="theader">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($family_groups as $family_group)
                    <tr>
                        <td>{{ $family_group->name }}</td>
                        <td>{{ $family_group->description }}</td>
                        <td>{{ $family_group->contact_number }}</td>
                        <td>{{ $family_group->address }}</td>
                        <td>
                            <a href="{{ url('organization/'. $slug .'/administrator/family/edit/'. $family_group->id) }}">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-pencil fa-stack-1x"></i>
                                </span>
                            </a>
                            <a href="{{ url('organization/'.$slug.'/administrator/family/delete/'.$family_group->id) }}">
                                <span class="fa-stack fa-lg icon-delete">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-trash-o fa-stack-1x"></i>
                                </span>
                            </a>
                            <a href="{{ url('organization/'.$slug.'/administrator/family/'.$family_group->id) }}">
                                <span class="fa-stack fa-lg icon-users">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-users fa-stack-1x"></i>
                                </span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
