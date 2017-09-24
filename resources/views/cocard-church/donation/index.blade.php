@extends('layouts.app')
@include('cocard-church.church.admin.sidenavigation')
@section('content')
<div class="d-content">
	<ul class="nav nav-tabs">
		<li role="presentation" class="active"><a data-toggle="pill" href="#onetime">One-time Donation</a></li>
		<li role="presentation"><a data-toggle="pill" href="#recurring">Recurring Donation</a></li>
	</ul>

	<div class="tab-content">
		@include('cocard-church.donation.onetime')
		@include('cocard-church.donation.recurring')
	</div>
</div>

@endsection
