@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content administrators-org">
    <div class="margin-mob-top">
        <!-- @foreach ($errors->all() as $error)
        @if(strpos($error, 'required') == false)
        <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
        @endforeach -->
        <h3>
            @if(isset($organization_name)) {{ $organization_name }} - @endif @if($display == "form") @if(isset($id)) @lang('dashboard_details.edit') @else @lang('dashboard_details.add') @endif @endif
            @lang('dashboard_details.administrators')
        </h3>
        @if(session('success'))
        <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session("success") }}
        </div>
        @endif
        @if(session('failed'))
        <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ session("failed") }}
        </div>
        @endif


        @if(isset($display) && $display == "form")
        <form class="form" enctype="multipart/form-data" method="POST" action="{{  $action }}">
            <input type="hidden" name="url_id" value="{{ $url_id }}">
            @include("cocard-church.user.templates.user_fields")
            {!! csrf_field() !!}
            <div class="row">
                <div class="admin-buttons">
                    <div class="col-sm-12">
                        <input type="submit" class="btn btn-darkblue" value="@lang('dashboard_details.submit')">
                        <a href="{{ url('/superadmin/organization-list-of-administrators',$url_id) }}" class="btn btn-red">@lang('dashboard_details.cancel')</a>
                    </div>
                </div>
            </div>
            <br>
        </form>
        @else
        @foreach($organizations as $organization)
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $organization->name }}</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <a href="{{ route('administrator_create',$organization->id) }}" class="btn btn-darkblue float-right">
                        @lang('dashboard_details.add') @lang('dashboard_details.administrators')&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </h3>
                <h3>
                    <a style="margin-right: 20px;" href="{{ url('/organizations') }}" class="btn btn-darkblue float-right">
                        <i class="icon-arrow-left">&nbsp;</i>@lang('dashboard_details.back')
                    </a>
                </h3>
            </div>
        </div>
        <br>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <th> @lang('dashboard_details.action')</th>
                    <th>@lang('dashboard_details.status')</th>
                    <th><a title = "Sort by  Name" href="{{route('organization_admin_users').'?order_by=first_name&sort='. $sort}}">@lang('dashboard_details.name')</a></th>
                    <th>@lang('dashboard_details.email')</th>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <div class="delete-modal-container deactivate-confirmation" data-id="{{$admin->id}}">
                        <div class="modal-delete">
                            <div class="modal-header">
                                <h5>Organization: Deactivate {{ $admin->first_name }} {{ $admin->last_name }}.</h5>
                            </div>
                            <?php
                                $user_roles = App\AssignedUserRole::where('user_id', $admin->id)->where('status', 'Active')->get();
                            ?>
                            <div class="modal-body">
                                @if(count($user_roles) > 1)
                                    <p><b>WARNING! This user has many roles.</b> Are you sure you want to delete this?</p>
                                @else
                                    <p>Are you sure you want to delete this?</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ route('administrator_delete',$admin->id) }}">
                                            YES
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_deactivate" data-id="{{$admin->id}}">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <tr>
                        <td class="col-md-2">
                            <a  href="{{ route('administrator_edit',$admin->id)  }}"  title="Edit">
            					<span class="fa-stack fa-lg icon-edit">
            						<i class="fa fa-square fa-stack-2x"></i>
            						<i class="fa fa-pencil fa-stack-1x"></i>
            					</span>
            				</a>
                            <a class="delete_modal" data-id="{{$admin->id}}" style="cursor:pointer;" title="Deactivate">
            					<span class="fa-stack fa-lg icon-delete">
            						<i class="fa fa-square fa-stack-2x"></i>
            						<i class="fa fa-trash-o fa-stack-1x"></i>
            					</span>
            				</a>
                        </td>

                        <td class="col-md-2">
                            {!! csrf_field() !!}

                            <span class="status-container">{{ $admin->status }}</span>
                        </td>
                        <td class="col-md-3">{{ $admin->first_name }} {{ $admin->last_name }}</td>
                        <td>{{ $admin->email or ""  }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @endforeach
        @endif

    </div>
</div>
@endsection

@section('script')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$("form.form").validate({
    rules : {
        password : {
            minlength : 5
        },
        confirm : {
            minlength : 5,
            equalTo : "#password"
        }
    }
});
var change_status_url = "{{ route('change_status')  }}";
var token = "{{ csrf_token() }}";
$(".staff-status").click(function(){
    var switch_ = $(this);
    var value = switch_.closest("td").find(".status-container").html();
    $.post(change_status_url,{id:switch_.data("staff_id"),status:value,_token:token}).done(function(data){
        switch_.closest("td").find(".status-container").html(data);
    });
});
</script>
@endsection
