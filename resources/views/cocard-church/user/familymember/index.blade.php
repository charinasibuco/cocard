@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-8">
                <div class="col-md-2">
                    <h3>
                        <div class="clearfix">
                            <a style="float:left;" href="{{ url('/organization/'.$slug.'/user/family')}}" class="btn btn-darkblue float-right">
                                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                            </a>
                        </div>
                    </h3>
                </div>
                <div class="col-md-8">
                    <h3>{{ $name }} Family Members</h3>
                </div>
            </div>
            <div class="col-md-4">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('organization/'. $slug .'/user/family/'.$family_id.'/family-member/create') }}"  class="btn btn-darkblue float-right">
                            Add Family Member&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        @foreach ($errors->all() as $error)
            <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endforeach
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
                    @if(count($family_members) == 0)
                        <td><i>No family members to show.</i></td>
                    @endif
                    @foreach($family_members as $family_member)
                    <div class="delete-modal-container" data-id="{{$family_member->id}}">
                        <div class="modal-delete">
                            <div class="modal-header">
                                <h5>Family Member: Delete {{ $family_member->first_name }} {{ $family_member->last_name }}.</h5>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this member?</p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('organization/'. $slug .'/family-member/delete/'.$family_member->id) }}">
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
                        <td>{{ date("n/d/Y", strtotime($family_member->birthdate)) }}</td>
                        <td>{{ $family_member->relationship }}</td>
                        <td>
                            <a href="{{ url('organization/'. $slug .'/user/family/family-member/'.$family_member->id) }}" title="View Family Member">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-eye fa-stack-1x"></i>
                                </span>
                            </a>
                            <a href="{{ url('organization/'. $slug .'/user/family/family-member/edit/'.$family_member->id) }}" title="Edit Family Member"> 
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-pencil fa-stack-1x"></i>
                                </span>
                            </a>
                            @if($family_member->user_id == 0)
                            <a href="" title="Add to Member Directory" data-toggle="modal" data-target="#{{ $family_member->id  }}_family_modal">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-user-plus  fa-stack-1x"></i>
                                </span>
                            </a>
                            @endif
                            <a class="delete_modal" data-id="{{$family_member->id}}" style="cursor:pointer;" title="Delete Family Member">
            					<span class="fa-stack fa-lg icon-delete">
            						<i class="fa fa-square fa-stack-2x"></i>
            						<i class="fa fa-trash-o fa-stack-1x"></i>
            					</span>
            				</a>
                        </td>
                    </tr>
                    <div id="{{ $family_member->id  }}_family_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"><label for="status">Input Required Fields</label></h4>
                                </div>
                                <div class="modal-body">
                                   <form action="{{ route('family_member_update', $family_member->id) }}" method="post">
                                      <input type="hidden" name="add_member" value="{{ $add_member }}">
                                      <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                                      <input type="hidden" name="slug" value="{{ $organization->slug }}">
                                      <input type="hidden" name="family_id" value="{{ $family_member->family_id }}">
                                      <input type="hidden" name="first_name" value="{{ $family_member->first_name }}">
                                      <input type="hidden" name="last_name" value="{{ $family_member->last_name }}">
                                      <input type="hidden" name="middle_name" value="{{ $family_member->middle_name }}">
                                      <input type="hidden" name="birthdate" value="{{ $family_member->birthdate }}">
                                      <input type="hidden" name="gender" value="{{ $family_member->gender }}">
                                      <input type="hidden" name="image" value="{{ $family_member->img }}">
                                      <div>
                                            <label>Phone Number:</label>
                                            <input class="form-control tel" type="text" placeholder="Phone Number" name="phone" value="{{ $phone }}">
                                      </div>
                                      <br/>

                                      <div>
                                            <label>Email Address:</label>
                                            <input class="form-control" type="email" name="email" value="{{ $email }}">
                                      </div>
                                      <br/>

                                      <div>
                                            <label>Password:</label>
                                            <input class="form-control" type="password" name="password" value="{{ $password }}">
                                      </div>
                                      <br/>
                                </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                                {!! csrf_field() !!}
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
            {{ $family_members->render() }}
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
