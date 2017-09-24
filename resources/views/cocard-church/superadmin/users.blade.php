@extends('layouts.app')
@include('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content administrators">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>User @if(isset($action_name)) {{ $action_name }} @endif</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <a href="{{ route('user_create')}}" class="btn btn-darkblue float-right">
                        Add User&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                </h3>
            </div>
        </div>
        @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div>
        @endif
        @if($display == "list")
        @include('cocard-church.user.templates.user_list')
        @else
        <form class="form" enctype="multipart/form-data" method="POST" action="{{  $action }}">
            <input class="form-control required" type="hidden" name="organization_id" value="{{ $organization_id }}">
            @include('cocard-church.user.templates.user_fields')
            {!! csrf_field() !!}
            <div class="row">
                <div class="admin-buttons">
                    <div class="col-sm-12">
                        <input type="submit" class="btn btn-darkblue" value="Submit">
                        <a href="{{ $back_url }}" class="btn btn-red">Cancel</a>
                    </div>
                </div>
            </div><br>
        </form>
        @endif
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('js/extensions/jquery-validation/src/core.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/extensions/jquery-validation/src/ajax.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/extensions/bootbox.min.js') }}"></script>
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
$(".delete-link").click(function(){
    $delete_link = $(this);
    bootbox.confirm({
        size: "small",
        message: "Are you sure you want to delete this User?",
        callback: function(result){
            if(result){
                window.location.href = $delete_link.prop("href");
            }
        }
    });
    return false;
});
var change_status_url = "{{ route('change_status')  }}";
$(".staff-status").click(function(){
    var switch_ = $(this);
    var value = switch_.closest("td").find(".status-container").html();
    $.post(change_status_url,{id:switch_.data("staff_id"),status:value,_token:"rj29r8498rit"}).done(function(data){
        switch_.closest("td").find(".status-container").html(data);
    });
});
</script>
@endsection
