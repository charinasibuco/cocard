@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content administrators">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h2>User @if(isset($action_name)) {{ $action_name }} @endif</h2>
            </div>
        </div>
        @if(count($errors) > 0)
        @foreach ($errors->all() as $error)
        @if(strpos($error, 'required') == false)
        <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
        @endforeach
        @endif
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('message') }}
        </div>
        @endif
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
        @if($display == "list")
        @include('cocard-church.user.templates.user_list')
        @else
        <form class="form" enctype="multipart/form-data" method="POST" action="{{ $action }}">
            <input class="form-control required" type="hidden" name="organization_id" value="{{ $organization_id }}">
            @include('cocard-church.user.templates.user_fields')
            {!! csrf_field() !!}
            <div class="row" style="float:right;margin-bottom:20px;">
                <div class="col-sm-12" >
                    <input type="submit" class="btn btn-darkblue" style="margin-left:10px;" value="Submit">
                    <a href="{{ url('/organization/'.$slug.'/administrator/users') }}" class="btn btn-darkblue">Cancel</a>
                </div>
            </div>
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
