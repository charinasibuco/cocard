@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Member</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <a href="{{ url('/organization/'.$slug.'/administrator/members') }}" class="btn btn-red float-right">
                        Cancel
                    </a>
                </h3>
             </div>
        </div>
        <div class="table-main panel panel-default">
            <form class="form" enctype="multipart/form-data" method="POST" action="{{  $action }}">
                <input class="form-control required" type="hidden" name="organization_id" value="{{ $organization_id }}">
                @include('cocard-church.user.templates.user_fields')
                {!! csrf_field() !!}
                <div class="clearfix">
                    <div class="pull-right">
                        <input type="submit" class="btn btn-darkblue" value="Submit">
                        <a href="{{ url('/organization/'.$slug.'/administrator/members') }}" class="btn btn-red">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
