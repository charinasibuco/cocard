@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-8">
                <div class="col-md-3 col-lg-2">
                    <h3>
                        <div class="clearfix">
                            <a style="float:left;" href="{{ url('/organization/'.$slug.'/administrator/family')}}" class="btn btn-darkblue float-right">
                                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                            </a>
                        </div>
                    </h3>
                </div>
                <div class="col-md-9 col-lg-10">
                    <h3>{{ $name }} Family Members</h3>
                </div>
            </div>
            <div class="col-md-4">
                <h3>
                    <div class="clearfix">
                        @can('add_user_family_member')
                        <a href="{{ url('organization/'. $slug .'/administrator/family/'.$family_id.'/family-member/create') }}"  class="btn btn-darkblue float-right">
                            Add Family Member&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
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
            <table class="table table-striped" id="tabledata">
                <thead class="theader">
                    <th>First name</th>
                    <th>Middle name</th>
                    <th>Last name</th>
                    <th>Birthdate</th>
                    <th>Relationship</th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @if(count($family_members) < 1)
                        <tr>
                            <td><i>No family members to show.</i></td>
                        </tr>
                    @endif
                    @foreach($family_members as $family_member)
                    <div class="delete-modal-container" data-id="{{$family_member->id}}">
                        <div class="modal-delete">
                            <div class="modal-header">
                                <h5>Family Member: Delete {{ $family_member->first_name }} {{ $family_member->last_name }}.</h5>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this family member?</p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('/organization/'.$slug.'/administrator/family-member/delete/'.$family_member->id) }}">
                                            YES
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$family_member->id}}">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <tr>
                        <td>{{ $family_member->first_name }}</td>
                        <td>{{ $family_member->middle_name }}</td>
                        <td>{{ $family_member->last_name }}</td>
                        @if($family_member->birthdate == '0000-00-00')
                        <td>-/-/----</td>
                        @else
                        <td>{{Carbon\Carbon::parse($family_member->birthdate)->format('n/d/Y')}}</td>
                        @endif
                        <td>{{ $family_member->relationship }}</td>
                        <td>
                            @can('view_specific_family_member')
                            <a href="{{ url('organization/'. $slug .'/administrator/family/family-member/'.$family_member->id) }}" title="View Family Member Details">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-eye fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('edit_user_family_member')
                            <a href="{{ url('organization/'. $slug .'/administrator/family/family-member/edit/'.$family_member->id) }}" title="Edit Family Member">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-pencil fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('delete_family_members')
                            <a class="delete_modal" data-id="{{$family_member->id}}" style="cursor:pointer;" title="Delete Family Member">
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
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    $(".delete_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation[data-id="' + id + '"]').css('display','none');
    });
});
</script>
@endsection
