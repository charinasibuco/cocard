@extends('layouts.app')
<?php $header = ($action_name == 'Add') ? "Add Frequency" : "Edit" . $title ;?>
@section('title', $header)
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('js/extensions/fancybox/source/jquery.fancybox.css') }}" media="screen" />
<div class="row">
    <div class="col-lg-12">
        <div class="navbar navbar-default bootstrap-admin-navbar-thin">
            <ol class="breadcrumb bootstrap-admin-breadcrumb">
                <li><a href="">Dashboard</a></li>
                <li><a href="{{ route('frequency') }}">Frequency</a></li>
               
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default bootstrap-admin-no-table-panel">
            <div class="panel panel-default bootstrap-admin-no-table-panel">
                <div class="panel-heading">
                    <div class="text-muted bootstrap-admin-box-title">@if($action_name == 'Add')Add Frequency @else Edit {{ $title  }} {{ $description  }} @endif</div>
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
                                            <div class="panel-heading"><i class="fa fa-sliders"></i>&nbsp;</span>Frequency Details</div>
                                            <div class="panel-body">
                                                <br/>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="pull-right">Title</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="form-control required" name="title" placeholder="Title" class="required prefill" value="{{ $title }}"><span class="error"></span></br>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label class="pull-right">Description</label>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="form-control required" name="description" placeholder="Description" class="required prefill" value="{{ $description }}"><span class="error"></span></br>
                                                    </div>
                                                </div>
                                			</div>
                                		</div>
                                		  {!! csrf_field() !!}
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input type="submit" class="btn btn-lg btn-primary" style="background-color:#084E9A; margin-left:10px;" value="Submit">
                                                <a href="{{ route('frequency')}}" class="btn btn-lg btn-danger">Cancel</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        &nbsp;
                                    </div>
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