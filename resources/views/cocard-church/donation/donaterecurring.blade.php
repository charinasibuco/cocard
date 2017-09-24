@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')

<div class="content">
	<div class="container">
		<button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart <span class="badge badge-danger">{{count($cart)}}</span></button>

		@include('cocard-church.donation.modal')
		<a href="{{ url('/organization/'.$slug.'/donations')}}" class="btn btn-darkblue float-right">
			<i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
		</a>
		<h1>DONATION DETAILS</h1>
		<div class="well onetime-donate">
			<div class="row">
				<div class="col-md-1" style="margin-top:50px;">
					<h1 style="font-size:100px;">$</h1>
				</div>
				<form id="onetime-form" method="post" action="{{ url('/organization/'.$slug.'/donation/add-donation/') }}">
					<div class="col-md-8">
						<h2 >Enter your donation</h2>
						<input type="hidden" value="{{$donationList->id}}" name="donation_category_id">
						<input type="hidden" value="One-Time" id="donation_type"name="donation_type">
						<input type="hidden" value="0" name="user_id">
						<input class="this_input donation_input"oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "20" type = "number" step="any"value=""name="amount" id="donationAmnt" required autofocus>
						<br><br>
						<textarea name="note" form="onetime-form" placeholder="Enter note here..." style="width:700px; height:80px;"></textarea>
						<br><br>
					</div>
					<div class="col-md-3">
						<h4>Fund to donate</h4>
						<hr>
						<h2>{{ $donationList->name}}</h2>
						<h5>{{ $donationList->description}}</h5>
					</div>
				</div>
				@if($donationList->recurring == 1)
				<div class="row">
					<div class="col-md-offset-1 col-md-11">
						<div class="clearfix">
							<div class="pull-left" style="margin-right: 10px;">
								<input type="checkbox" name="cb_recurring" class=" js-recurring"  id="cb_recurring" value="{{ $donationList->recurring }}">
							</div>
							<div class="pull-left">
								<h4 class="margin-0">Create a Recurring Donation </h4>
							</div>
						</div>
					</div>
				</div>
				@endif
				<div class="row" id="recurring_details" style="display:none;margin-top:10px;margin-left: 10%;">
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
				<div>
					<div class="col-md-offset-6 col-md-3">
						{!! csrf_field() !!}
						<br><br>
						<button type="submit" name="button" style="display:none;" id="cart-submit"></button>
						<button type="submit" style="float:right; margin-left:0; margin-right:0;"class="btn btn-darkblue add_to_cart">Add to Cart &nbsp;<i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@include('includes.footer')
@endsection
@section("script")
<script type="text/javascript">
// $(document).ready(function() {
// 	$('#cb_recurring').click(function(){
// 		if($(this).prop("checked") == true){
// 			var r = confirm("Please confirm that you are making a recurring donation.");
// 			//alert("kek");
			
// 			if (r == true) {
// 				// $('.date').prop("disabled",false);
// 				$('#recurring_details').show();
// 				$('#donation_type').val('Recurring');
// 			} else {
// 				//$('.date').prop("disabled",true);
// 				document.getElementById('cb_recurring').checked=false;
// 			}
// 		}else{
// 			//alert("kek2");
// 			$('#recurring_date').prop('checked',true);
// 			$('#recurring_details').hide();
// 			$('#donation_type').val('One-Time');

// 		}
// 	});
// });
</script>
@endsection
