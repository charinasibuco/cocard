@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		@if(Session::has('message'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('message') }}
		</div>
		@elseif(Session::has('error'))
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('error') }}
		</div>
		@endif
		<h2>{{$action_name}} Donation Category</h2>

		<div class="table-main panel panel-default">
			<div class="well" style="height:170px;">
				<form id="event-form" class="form-horizontal" method="POST" action="{{ $action }}" style="margin-top:19px;">
					<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
						<label for="inputEmail3" class="col-sm-3 control-label">Donation name</label>
						<div class="col-sm-8">
							<input type="hidden" name="organization_id" value="{{ $organization_id }}" >
							<input type="hidden" name="slug" value="{{ $slug }}" >
							<input type="hidden" name="cb_val" value="" id="cb_val">
							<input type="hidden" name="dcid" value="{{$id}}" id="dcid">
							<input type="text" class="form-control" name="name"  value="{{$name}}" required>
							@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
						<label class="col-sm-3 control-label">Description</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="description" value="{{$description}}"  required>
							@if ($errors->has('description'))
							<span class="help-block">
								<strong>{{ $errors->first('description') }}</strong>
							</span>
							@endif
						</div>
					</div>
					@if($id >0)
					@else
					<div id="cbdiv" class="form-group float-right" style="margin-right: 7%;">
						<div class="row">
							<div class="col-sm-12">
								<input type="checkbox" name="save_another" id="cbx">&nbsp;Save and Add Another
							</div><br>
						</div>
					</div>
					@endif

				</div>
				<div class="clearfix">
					{!! csrf_field() !!}
					<div class="pull-right">
						<button type="submit" id="event-submit" class="btn btn-darkblue">
							Submit
						</button>
						<a style="margin-right:10px;" href="{{ url('/organization/'.$slug.'/administrator/donation-category') }}" class="btn btn-red">
							Cancel
						</a>
					</div>
					<br>
				</div>
			</form>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.6/jstz.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
         $('#cbx').click(function(){
            if($(this).prop("checked") == true){
                	document.getElementById("cb_val").value = "1";
            } else {
            	document.getElementById('cb_val').value = "0";
            }
        });
    });
</script>
@endsection
