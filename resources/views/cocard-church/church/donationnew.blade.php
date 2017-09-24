@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')

<div class="content">
	<div class="container">
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

		<button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart <span class="badge badge-danger">{{count($cart)}}</span></button>
		@include('cocard-church.donation.modal')

		<ul class="nav nav-tabs nav-justified">
			<li role="presentation" class="active"><a data-toggle="pill" href="#onetime">One-time Donation</a></li>
			<li role="presentation"><a data-toggle="pill" href="#recurring">Recurring Donation</a></li>
		</ul>

		<div class="tab-content donation-layout">
			@include('cocard-church.donation.templates.onetime_item')
			@include('cocard-church.donation.recurring')
		</div>
</div>
{{--<ipp:connectToIntuit></ipp:connectToIntuit>--}}
@endsection
