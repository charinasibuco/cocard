@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		@if(Session::has('success'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('success') }}
		</div>
		@elseif(Session::has('error'))
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('error') }}
		</div>
		@endif
		<h2>{{$action_name}} Donation List</h2>
		<div class="table-main panel panel-default">
			<div class="well">
				<form id="event-form" class="form-horizontal" method="POST" action="{{ $action }}" >
					<div class="form-group" style="margin-top:19px;">
						<label for="inputEmail3" class="col-sm-3 control-label">Category</label>
						<div class="col-sm-8">
							<select class="form-control" name="donation_category_id">
								@foreach($donationCategory as $row)
								<option value="{{ $row->id }}" {{ ($row->id == $donation_category_id) ? 'selected="selected"' : ' '}}>{{ $row->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
						<label for="inputEmail3" class="col-sm-3 control-label">Donation name</label>
						<div class="col-sm-8">
							<input type="hidden" name="slug" value="{{ $slug }}" >
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
					<div class="form-group">
						{{--<label class="col-sm-3 control-label">Mode of Donation</label>--}}
						<div class="col-sm-1">
						</div>
						<div class="col-sm-4">
							<input type="hidden" class="" name="recurring" value="0"  style="margin-top:10px;"> {{--One Time Only Donations--}}
						</div>
						<div class="col-sm-4">
							<input type="hidden" class="" checked="checked"name="recurring" value="1" @if($recurring == 1) checked="checked" @endif style="margin-top:10px;"> {{--One Time & Recurring Donations--}}
						</div>
					</div>
				</div>
				<div class="clearfix">
					{!! csrf_field() !!}
					<div class="pull-right">
						<button type="submit" id="event-submit" class="btn btn-darkblue">
							Submit
						</button>
						<a style="margin-right:10px;"href="{{ url('/organization/'.$slug.'/administrator/donation-list')}}" class="btn btn-red">
							Cancel
						</a>
					</div>
					<br>
				</div>
			</div>
		</form>

	</div>
</div>
</div>
@endsection
