@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<div class="row">
			<div class="col-xs-offset-3 col-sm-offset-3 col-md-offset-2 col-xs-10 col-sm-10 col-md-10 ">
				<ul class="nav nav-tabs nav-justified">
					<li role="presentation" class="active"><a data-toggle="pill" href="#onetime">One-time Donation</a></li>
					<li role="presentation"><a data-toggle="pill" href="#recurring">Recurring Donation</a></li>
				</ul>
				<div class="tab-content">
					@include('cocard-church.donation.onetime')
					@include('cocard-church.donation.recurring')
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
