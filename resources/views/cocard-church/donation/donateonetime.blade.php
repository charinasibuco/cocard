@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')

<div class="content">
	<div class="container">
			<button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart</button>
			@include('cocard-church.donation.modal')
			<a href="{{ url('/organization/'.$slug.'/donations')}}" class="btn btn-darkblue float-right">
				<i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
			</a>
		<h1>ONE-TIME DONATION</h1>
		<div class="well onetime-donate">
			<div class="row">
				<div class="col-md-1" style="margin-top:50px;">
					<h1 class="donation-dollarsign">$</h1>
				</div>
				<form id="onetime-form" method="post" action="{{ url('/organization/'.$slug.'/donation/add-donation/') }}">
					<div class="col-md-8">
						<h2 >Enter your donation</h2>
						<input type="hidden" value="{{$donationList->id}}" name="donation_category_id">
						<input type="hidden" value="One-Time" name="donation_type">
						<input type="hidden" value="0" name="user_id">
							<input class="this_input donation_input" min="1" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "20" type = "number" value="" step="any" name="amount" id="donationAmnt" autofocus>
							<br>
							<br>
							{!! csrf_field() !!}
							<button type="submit" class="btn btn-darkblue btn-lg add_to_cart">Add to Cart &nbsp;<i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
					</div>
					<div class="col-md-3">
						<h4>Fund to donate</h4>
						<hr>
						<h2>{{ $donationList->name}}</h2>
						<h5>{{ $donationList->description}}</h5>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


@include('includes.footer')
@endsection
