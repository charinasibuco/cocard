@extends('layouts.app')

@include('cocard-church.church.admin.navigation')

@section('content')
<div class="d-content" id="app">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Event</h3>
                <input type="hidden" value="{{ $action_name}}" id="action_name">
            </div>
            <div class="col-md-6">

                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
        </div>
		<div class="table-main panel panel-default well">
            <form id="event-form" class="form-horizontal" method="POST" action="{{ $action }}" >
                <input type="hidden" id="instance" name="instance" value="{{ @$instance }}">
                <div class="form-group">
                    <div class="col-sm-9">
                        <input type="hidden" class="form-control" name="organization_id" value="{{ $organization_id }}" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Event name</label>
                    <div class="col-sm-9">
                        <input type="hidden" name="slug" value="{{ $slug }}" >
                        <input type="text" class="form-control" name="name" value="{{ $name }}" required>
                        @if ($errors->has('name'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <input type="hidden" value="" name="hash" class="hash">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" value="{{ $description }}" required>
                        @if ($errors->has('description'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('description') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Capacity</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="capacity" value="{{ $capacity }}" required>
                        @if ($errors->has('capacity'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('capacity') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Fee</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                             <span class="input-group-addon">$</span>
                            <input type="number" class="form-control" name="fee" value="{{ $fee }}" required>
                        </div>
                        @if ($errors->has('fee'))
                            <span class="help-block">
                                <strong style="color:red;">{{ $errors->first('fee') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Start date</label>
                    <div class="col-sm-9">
                        <div class="input-group startdtp" id="startdtp">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="start_date2" id="start_date" value="{{ (old('start_date2'))? old('start_date2') :  $start_date }}" required="required">
                            <span class="input-group-addon">
                                <span class="glyphicon-calendar glyphicon"></span>
                            </span>
                        </div>
                        
                        @if ($errors->has('start_date2'))
                        <span class="help-block">
                            <strong style="color:red;">Start date field is required</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">End date</label>
                    <div class="col-sm-9">
                        <div class='input-group enddtp' id="enddtp">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="end_date2"  value="{{ (old('end_date2'))? old('end_date2') :  $end_date }}" id="end_date" required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
  
                        @if ($errors->has('end_date2'))
                        <span class="help-block">
                            <strong style="color:red;">End date field is required</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Reminder date</label>
                    <div class="col-sm-9">
                        <div class="input-group reminderdtp" >
                            <input type="text" class="form-control" name="reminder_date" placeholder="M/D/YYYY"  value="{{ $reminder_date}}" id="reminder_date_get">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>

                        @if ($errors->has('reminder_date'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('reminder_date') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/main.js') }}"></script>
@endsection
