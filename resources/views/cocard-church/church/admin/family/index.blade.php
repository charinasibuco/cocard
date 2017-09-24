@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
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
                        @can('add_family')
                        <a href="{{ url('/organization/'.$slug.'/administrator/family/create')}}" class="btn btn-darkblue float-right">
                            Add Family Group&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
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
        <div class="table-responsive">
        <div class="table-main panel panel-default">
            <table class="table table-striped" id="tabledata">
                <thead class="theader">
                    <th>Name</th>
                    <th>Description</th>
                    <th>Contact Number</th>
                    <th>Contact Email</th>
                    <th>Action</th>
                </thead>
                <tbody>
                @if(count($family_groups) < 1)
                    <tr>
                        <td><i>No family groups to show.</i></td>
                    </tr>
                @endif
                    @foreach($family_groups as $family_group)
                    <div class="delete-modal-container" data-id="{{$family_group->id}}">
                        <div class="modal-delete">
                            <div class="modal-header">
                                <h5>Family: Delete {{$family_group->name}}.</h5>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this family?</p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"href="{{ url('/organization/'.$slug.'/administrator/family/delete/'.$family_group->id) }}">
                                            YES
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$family_group->id}}">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <tr>
                        <td>{{ $family_group->name }}</td>
                        <td>{{ $family_group->description }}</td>
                        <td>{{ $family_group->primary_phone }}</td>
                        <td>{{ $family_group->primary_email }}</td>
                        <td>
                            <a href="{{ url('organization/'. $slug .'/administrator/family/view/'.$family_group->id) }}" title="View Family Group Details">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-eye fa-stack-1x"></i>
                                </span>
                            </a>
                            @can('view_family_members')
                            <a href="{{ url('organization/'.$slug.'/administrator/family/'.$family_group->id) }}" title="View Family Members">
                                <span class="fa-stack fa-lg icon-users" style="color: #012732;">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-users fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('edit_family')
                            <a href="{{ url('organization/'. $slug .'/administrator/family/edit/'. $family_group->id) }}" title="Edit Family Group">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-pencil fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('delete_family')
                            <a class="delete_modal" data-id="{{$family_group->id}}" style="cursor:pointer;" title="Delete Family Group">
                                <span class="fa-stack fa-lg icon-delete">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-trash-o fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
@endsection