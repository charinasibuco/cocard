@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
	@include('cocard-church.donation.modal')
	<div class="row">
		<div class="col-md-3 col-sm-4">
			<a href="#" class="btn btn-darkblue btn-block" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart
				<span class="badge badge-danger">{{count($cart)}}</span>
			</a>
		</div>
		<div class="col-md-6 col-sm-4"></div>
		<div class="col-md-3 col-sm-4">
			<a href="{{ url('/organization/'.$slug.'/user/donate')}}" class="btn btn-darkblue btn-block">
				<i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
			</a>
		</div>

		<div class="col-sm-12">
			<h1 class="hidden-xs">Donation Details</h1>
			<h3 class="visible-xs">Donation Details</h3>
			<form id="onetime-form" method="post" action="{{ url('/organization/'.$slug.'/user/donate/add-donation/') }}">
			<div class="well onetime-donate">
				<div class="row">
					<div class="col-sm-8">
						<h2 class="hidden-xs">Enter your donation</h2>
						<h4 class="visible-xs">Enter your donation</h4>

						<input type="hidden" value="{{$donationList->id}}" name="donation_category_id">
						<input type="hidden" value="One-Time" id="donation_type"name="donation_type">
						<input type="hidden" value=" {{ $user->id}} " name="user_id">

						<div class="input-group">
						  <span class="input-group-addon large" id="basic-addon1">$</span>
						  <input class="this_input donation_input"oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "20" type = "number" step="any" value=""name="amount" id="donationAmnt" required autofocus>
						</div>
						<br>
						<textarea name="note" form="onetime-form" placeholder="Enter note here..." style="width:100%; height:80px; resize: none;"></textarea>
					</div>
					<div class="col-sm-4">
						<h4>Fund to donate</h4>
						<hr>
						<h5>{{ $donationList->name}}</h5>
						<p>{{ $donationList->description}}</p>
					</div>

					<div class="col-sm-12">
						<br>
						@if($donationList->recurring == 1)
						<label class="control-label">
							<input type="checkbox" name="cb_recurring" class=" js-recurring" id="cb_recurring" value="{{ $donationList->recurring }}">
							Create a Recurring Donation
						</label>
						@endif
					</div>
				</div>

				<div class="row" id="recurring_details" style="display:none;margin-top:10px;">
					<div class="col-sm-8">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="" class="control-label">Frequency</label>
								<select class="form-control" name="frequency_id">
									@foreach($frequency as $row)
									<option value="{{$row->id}}">{{$row->title}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="radio">
								<label for="" class="control-label">
									<input type="radio" name="recurring_type" class="js_recurring_type" id="recurring_date" value="0" checked="checked"> Make a Donation From:
								</label>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="col-sm-3 control-label">Start date</label>
								<div class='input-group startdp'>
									<input id="date_from" type="text" placeholder="M/D/YYYY" class="form-control xsdate" name="start_date" >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="col-sm-3 control-label">End date</label>
								<div class='input-group enddp'>
									<input id="date_to" type="text" placeholder="M/D/YYYY"class="form-control xedate" name="end_date" >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="radio">
								<label for="" class="control-label">
									<input type="radio" name="recurring_type" class="js_recurring_times" id="recurring_times" value="1"> Make it a Fixed Number of Times
								</label>
							</div>
						</div>
						<div class="col-sm-12">
							<input type="number" min="0"  id="repetition" placeholder="No. of Payments"class="form-control" name="no_of_payments" >
							<br>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label class="col-sm-3 control-label">Start date</label>
								<div class='input-group startdp'>
									<input id="date_start" type="text" placeholder="M/D/YYYY" class="form-control xsdate sdateFixedNoPayment" name="start_date" >
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
					</div>
				</div>
				<div class="row">
					<div class="col-sm-9">

					</div>
					<div class="col-sm-3">
						{!! csrf_field() !!}
						<br><br>
						<button type="submit" name="button" style="display:none;" id="cart-submit"></button>
						<button class="btn btn-darkblue btn-block add_to_cart">Add to Cart &nbsp;<i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
