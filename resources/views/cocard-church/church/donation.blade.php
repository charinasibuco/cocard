@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="content donation">
	<div class="container">
		<div class="margin-mob-top">
			<h3 style="text-align: center;">{{$organization->name}} List of Donations</h3>
			<br><button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart <span class="badge badge-danger">{{count($cart)}}</span></button>
			@include('cocard-church.donation.modal')
			<a href="{{ url('/organization/'.$slug.'/home')}}" class="btn btn-darkblue float-right">
				<i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
			</a>
			<div class="tab-content donation-layout">
				@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{{ Session::get('message') }}
				</div>
				@endif
			</div>
			<div class="search form-group">
	            <form method="GET" action="">
	                <div class="search form-group">
	                    <div class="input-group">
	                        <span class="input-group-addon" id="basic-addon1">
	                            <i class="fa fa-search"></i>
	                        </span>
	                        <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="{{ $search }}">
	                        <span class="input-group-btn">
	                            <button class="btn btn-darkblue" type="submit">Go</button>
	                        </span>
	                    </div>
	                </div>
	            </form>
            </div>
			<div class="">
				<table class="table table-striped" style="width:100%;">
					<thead>
						<th>Donation Name</th>
						<th>Description</th>
						<th></th>
					</thead>
					@foreach($donationList as $q)
					<tr>
						<td>{{ $q->name }}</td>
						<td>{{ $q->description }}</td>
						<td><a href="{{ url('/organization/'.$slug.'/donationrecurring',$q->id) }}" class="btn btn-darkblue">Donate</a></td>
					</tr>
					@endforeach
				</table>
				{{ $donationList->render() }}
			</div>
		</div>
	</div>
</div>
@endsection
@section("script")
@if(!empty(Session::get('error_code')) && Session::get('error_code') == 5)
<script type="text/javascript">
$(function() {
	$('#large-modal').modal('show');
});
</script>
@endif
@endsection
