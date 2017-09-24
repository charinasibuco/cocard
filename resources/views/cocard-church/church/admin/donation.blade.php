@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<ul class="nav nav-tabs">
			<li role="presentation" id="cat" class="active"><a data-toggle="pill" href="#donation_category">Donation Category</a></li>
			<li role="presentation" id="list"><a data-toggle="pill" href="#category_list">Donation List</a></li>
		</ul>
		<br>
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
		<div class="tab-content" style="margin-top:0;">
			@can('view_donation_category')
			@include('cocard-church.donation.donationcategory')
			@endcan
			@can('view_donation_list')
			@include('cocard-church.donation.donationlist')
			@endcan
		</div>
	</div>
</div>
@endsection
@section('script')

<script type="text/javascript">
$(document).ready(function() {
	var hash = (location.href.split("#")[1] || "");
	if (location.hash === "#category_list") {
		$('.nav-tabs a[href="#'+hash+'"]').tab('show');
    }
});
</script>
@endsection
