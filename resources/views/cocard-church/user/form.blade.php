@extends('layouts.app')
<?php $header = ($action_name == 'Add') ? "Add User" : "Edit" . $first_name ;?>
@section('title', $header)
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('js/extensions/fancybox/source/jquery.fancybox.css') }}" media="screen" />
<style type="text/css">
input[type="file"] {
    display: none;
}
.custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
}
.uneditable-input {
    padding: 6px 12px;
    min-width: 206px;
    font-size: 14px;
    font-weight: normal;
    height: 34px;
    color: #333;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    }
.inline-file
{
    position:absolute;
    top: 0;
    right:0;
    margin-right: 14px;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="navbar navbar-default bootstrap-admin-navbar-thin">
            <ol class="breadcrumb bootstrap-admin-breadcrumb">
                <li><a href="">Dashboard</a></li>
                <li><a href="{{ route('users') }}">Users</a></li>
                @if($action_name != 'Add')
                    <li><a href="{{ route('user_show') }}">Profile</a></li>
                @endif
                <li class="active">@if($action_name == 'Add')Add User @else {{ $first_name }} {{ $last_name }} Personal Details @endif</li>
            </ol>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default bootstrap-admin-no-table-panel">
            <div class="panel panel-default bootstrap-admin-no-table-panel">
                <div class="panel-heading">
                    <div class="text-muted bootstrap-admin-box-title">@if($action_name == 'Add')Add User @else Edit {{ $first_name  }} {{ $last_name  }} @endif</div>
                </div>
                <div class="bootstrap-admin-no-table-panel-content bootstrap-admin-panel-content collapse in">
                    <div class="row">
                        <div class="col-md-12">

                            @if(Session::has('message'))
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('message') }}
                                </div>
                            @elseif(count($errors) > 0)
                                @foreach ($errors->all() as $error)
                                    <div class="alert alert-warning alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <li style="list-style:none">{{ $error }}</li>
                                    </div>
                                @endforeach
                            @endif
                            <form id="user_form" role="form" action="{{ $action }}" method="post" class="form idealforms">
                                <div class="row">
                                    <div class="col-sm-3">
                                        &nbsp;
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading"><i class="fa fa-user"></i>&nbsp;</span>Personal Details</div>

                                            <div class="panel-body">
                                                <fieldset>
                                                    @include('templates.user_fields')
                                                    <div class="form-group">
                                                        <label for="last-name">Role:</label>
                                                        <select required class="form-control required" name="role" class="required prefill">
                                                            @foreach($roles as $role)
                                                                <option value="{{ $role->id }}" {{--@if($)--}}>{{ $role->title  }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input type="submit" class="btn btn-lg btn-primary" style="background-color:#084E9A; margin-left:10px;" value="Submit">
                                                <a href="{{ $back_url  }}" class="btn btn-lg btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        &nbsp;
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script type="text/javascript" src="{{ asset('js/lib/jquery.validate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/user_form.js') }}"></script>
    {{--<script type="text/javascript" src="{{ asset('filemanager/plugin.min.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('js/extensions/fancybox/source/jquery.fancybox.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/extensions/fancybox/source/jquery.fancybox.pack.js') }}"></script>
    <script>
        $(document).ready(function(){
            $('.iframe-btn').fancybox({
                'width'		: 900,
                'height'	: 600,
                'type'		: 'iframe',
                'autoScale'    	: false,
                afterClose : function() {
                    var $image = $('#fieldID').val();
                    $('#prev_image').prop('src',document.getElementById("fieldID").value).show();
//                    console.log('clossing')
//                    $('#sub_cont').hide(250, function() {
//                        $('#IDsearchform input').val('');
//                    });
                }
            });

            $(".password-toggle").click(
                    function() {
                        $("#password_field").find("input").each(function(){
                            if (!$("#password_field").is(":visible")) {
                                $(this).addClass("required");
                            }else{
                                $(this).removeClass("required");
                            }
                        });

                    }
            );
            $("#user_form").submit(function(){
                $count = 0;
                $password_count = 0;
                $check = $(this).find(".required").each(function () {
                    if ($(this).val() == "") {
                        $count++;
                        $(this).css({'box-shadow': "0px 0px 5px red"});
                    }

                    if($(this).prop("type") == "password"){
                        if($("input[name=password]").val() != $("input[name=confirm]").val()){
                            $password_count++;
                        }
                    }
                });
                if($count > 0 ){
                    alert("Some fields are required!");
                    return false;
                }

                if($password_count > 0 ){
                    alert("Passwords do not match!");
                    return false;
                }
            });


            function OnMessage(e){
                var event = e.originalEvent;
                // Make sure the sender of the event is trusted
                if(event.data.sender === 'responsivefilemanager'){
                    if(event.data.field_id){
                        console.log('on select image')
                        var fieldID=event.data.field_id;
                        var url=event.data.url;
                        $('#'+fieldID).val(url).trigger('change');
//                        $('#prev_image').prop('src',url).trigger('change');
                        $.fancybox.close();

                        // Delete handler of the message from ResponsiveFilemanager
                        $(window).off('message', OnMessage);
                    }
                }
            }

            function responsive_filemanager_callback(field_id){
                console.log(field_id);
                var url=jQuery('#'+field_id).val();
               // alert('update '+field_id+" with "+url);
                //your code
            }
            // Handler for a message from ResponsiveFilemanager
            $('.opener-class').on('click',function(){
                $(window).on('message', OnMessage);
            });



            $('#download-button').on('click', function() {
                ga('send', 'event', 'button', 'click', 'download-buttons');
            });

            $('.toggle').click(function(){
                var _this=$(this);
                $('#'+_this.data('ref')).toggle(200);
                var i=_this.find('i');
                if (i.hasClass('icon-plus')) {
                    i.removeClass('icon-plus');
                    i.addClass('icon-minus');
                }else{
                    i.removeClass('icon-minus');
                    i.addClass('icon-plus');
                }
            });

        });


    </script>
@stop