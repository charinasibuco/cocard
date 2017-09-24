@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">

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
        <div class="row">
            <div class="col-xs-offset-3 col-sm-offset-3 col-md-offset-2 col-xs-10 col-sm-10 col-md-10 ">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Event Volunteers ({{ count($event->volunteers) }}/{{ $event->volunteers_needed }})</h3>
                    </div>
                    <div class="col-md-6">
                        <h3>
                            <div class="clearfix">
                                <a href="{{ url('/organization/'.$slug.'/administrator/events')}}" class="btn btn-darkblue float-right">
                                    <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                                </a>
                            </div>
                        </h3>
                    </div>
                </div>
                @foreach($event->volunteer_groups as $group)
                <div class="panel panel-primary panel-information">
                    <div class="panel-heading personal-information"><h4>{{ $group->type }} ({{ count($group->volunteers) }}/{{ $group->volunteers_needed  }})</h4></div>
                    <div class="panel-body">
                        <span>{{ $group->note }}</span>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group->volunteers as $volunteer)
                                <tr>
                                    @if($volunteer->user_id)
                                    <td>{{ $volunteer->user->full_name }}</td>
                                    <td>{{ $volunteer->user->email }}</td>
                                    @else
                                    <td>{{ $volunteer->name }}</td>
                                    <td>{{ $volunteer->email }}</td>
                                    @endif
                                    <td>
                                        <a href="{{ route("volunteer_delete",$volunteer->id)  }}" class="delete-link" title="Remove Volunteer">
                                            <i class="fa fa-trash-o fa-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(".delete-link").click(function(){
    $delete_link = $(this);
    bootbox.confirm({
        size: "small",
        message: "Are you sure you want to remove this Volunteer?",
        callback: function(result){
            if(result){
                window.location.href = $delete_link.prop("href");
            }
        }
    });
    return false;
});
</script>
@endsection
