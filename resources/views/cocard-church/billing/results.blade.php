@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="d-content">
	<div class="row">
		<div class="col-md-12">
			<div class="page-header">
				<h2>Thank you. This is your Transaction Summary.</h2>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="well">
						<form action="{{ url('/organization/'.$slug.'/transaction-receipt-pdf')}}" method="post">
							@if($ret['code'] == 1)
								<?php
									$transaction_id = App\Transaction::where('transaction_key', $ret['trans_id'])->first();
									$donations 		= App\Donation::where('transaction_id', $transaction_id->id)->get();
									$transaction_details = App\TransactionDetails::where('transaction_id', $transaction_id->id)->get();
									$events 		= App\TransactionDetails::where('event_id','!=', 0)->where('transaction_id', $transaction_id->id)->get();
									$product        = (isset($res->product)?$res->product : '') ;
									$donation = App\TransactionDetails::where('transaction_id', $transaction_id->id)->where('event_id','0')->get();
								?>

							<div class="col-md-12" style="text-align:center">
								<h4 style="text-transform:uppercase">{{ $organization['name']}}</h4>
								<input type="hidden" name="organization_name" value="{{ $organization['name']}}">
								<h5>{{ $organization['contact_number']}}</h5>
								<input type="hidden" name="organization_contact_number" value="{{ $organization['contact_number']}}">
								<h5>{{ $organization['email']}}</h5>
								<input type="hidden" name="organization_email" value="{{ $organization['email']}}">
								<h5>TRANSACTION ID: {{ $ret['trans_id'] }}</h5>
								<input type="hidden" name="transaction_id" value="{{ $ret['trans_id'] }}">
								<h5>DATE: <div id="now"></div></h5></br></br>
								<input type="hidden" value="" id="date_now" name="date_now">
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary" id="event_panel" style="display:none">
										<div class="panel-heading">EVENT</div>
										<div class="panel-body">
										@foreach($cart as $item)
										<input type="hidden" value="{{ count($events) }}" id="event_count">
										<?php 
											$data_cart =  json_encode($item);
										?>
											<div class="row">
												<div class="col-md-12">
													@if($item->donation_category_id == 0 && $item->event_id > 0)
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Authorization Code:</br>
															</div>
															<div class="col-md-6">
																{{ $item->id}}
																<input type="hidden" value="{{ $item->id}}" name="event_product[]">
															</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event Name:
															</div>
															<div class="col-md-6">
																{{ $item->name }} 
																<input type="hidden" value="{{ $item->name}}" name="event_name[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event Description:
															</div>
															<div class="col-md-6">
																{{ $item->description }}
																<input type="hidden" value="{{ $item->description}}" name="event_description[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event Fee:
															</div>
															<div class="col-md-6">
																${{ $item->fee }}
																<input type="hidden" value="{{ $item->fee}}" name="fee[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Ticket Qty:
															</div>
															<div class="col-md-6">
																{{ $item->qty }}
																<input type="hidden" value="{{ $item->qty }}" name="qty[]">
															</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event Start Date:</br>
															</div>
															<div class="col-md-6">
																{{ $item->start_date}}
																<input type="hidden" value="{{ $item->start_date}}" name="event_start_date[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event End Date:</br>
															</div>
															<div class="col-md-6">
																{{ $item->end_date}}
																<input type="hidden" value="{{ $item->end_date}}" name="event_end_date[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													@if($item->recurring > 0)
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event No. of Repetition:</br>
															</div>
															<div class="col-md-6">
																{{ $item->no_of_repetition }}</br>
																<input type="hidden" value="{{ $item->no_of_repetition}}" name="no_of_repetition[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Event Recurring End Date:</br>
															</div>
															<div class="col-md-6">
																{{ $item->recurring_end_date }}</br>
																<input type="hidden" value="{{ $item->recurring_end_date}}" name="recurring_end_date[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																 Recurring Event Frequency:</br>
															</div>
															<div class="col-md-6">
																@if($item->recurring == 1)
																Weekly
																@elseif($item->recurring == 2)
																Monthly
																@elseif($item->recurring == 3)
																Yearly
																@else
																-----
																@endif
																<input type="hidden" value="{{ $item->recurring}}" name="recurring[]">
																</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
												@endif
												<div class="row">
													<div class="col-md-2">
													</div>
													<div class="col-md-8">
														<div class="col-md-6">
															<b>Amount:</b></br>
														</div>
														<div class="col-md-6">
															<b>${{number_format($item->amount, 2, '.', '')}}</b>
															<input type="hidden" value="{{number_format($item->amount, 2, '.', '')}}" name="event_total[]">
														</div>
													</div>
													<div class="col-md-2">
													</div>
												</div>
												<div class="row">
													<div class="col-md-12" style="text-align:center">
														--------------------------------------------------------
													</div>
												</div>
											@endif
										</div>
									</div>
									@endforeach
									</div>
								</div>
							</div>
							{{-- Donation --}}
							<div class="col-md-12">
									<div class="panel panel-primary" id="donation_panel" style="display:none">
										<div class="panel-heading">DONATION</div>
										<div class="panel-body">
										@foreach($cart as $item)
										<input type="hidden" value="{{ count($donation) }}" id="donation_count">
										<?php 
											$data_cart =  json_encode($item);
										?>
											<div class="row">
												<div class="col-md-12">
													@if($item->donation_category_id > 0 && $item->event_id == 0)
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Authorization Code:</br>
															</div>
															<div class="col-md-6">
																{{ $item->id}}
																<input type="hidden" value="{{ $item->id}}" name="donation_product[]">
															</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Donation Type:
															</div>
															<div class="col-md-6">
																{{ $item->donation_type }} 
																<input type="hidden" value="{{ $item->donation_type}}" name="donation_type[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Fund to Donate:
															</div>
															<div class="col-md-6">
																{{ $item->donationList_title }}
																<input type="hidden" value="{{ $item->donationList_title}}" name="donationList_title[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Details:
															</div>
															<div class="col-md-6">
																{{ $item->description }}
																<input type="hidden" value="{{ $item->description}}" name="donation_description[]">
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													@if($item->donation_type == 'Recurring')
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Start Date:</br>
															</div>
															<div class="col-md-6" id="donation_start">
																{{($item->start_date !== '0000-00-00 00:00:00')? $item->start_date : '------------'}}
																<input type="hidden" value="{{($item->start_date !== '0000-00-00 00:00:00')? $item->start_date : '------------'}}"  name="donation_start_date[]">
																</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																End Date:</br>
															</div>
															<div class="col-md-6" id="donation_end">
																{{($item->end_date !== '0000-00-00 00:00:00')? $item->end_date : '------------'}}
																<input type="hidden" value="{{($item->end_date !== '0000-00-00 00:00:00')? $item->end_date : '------------'}}" name="donation_end_date[]">
																</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													<div class="row">
														<div class="col-md-2">
														</div>
														<div class="col-md-8">
															<div class="col-md-6">
																Frequency:</br>
															</div>
															<div class="col-md-6" id="donation_end">
																{{ $item->{'frequency_title'} }}
																<input type="hidden" value="{{ $item->frequency_title}}" name="frequency_title[]">
																</br>
															</div>
														</div>
														<div class="col-md-2">
														</div>
													</div>
													@endif
												<div class="row">
													<div class="col-md-2">
													</div>
													<div class="col-md-8">
														<div class="col-md-6">
															<b>Amount:</b></br>
														</div>
														<div class="col-md-6">
															<b>${{number_format($item->amount, 2, '.', '')}}</b>
															<input type="hidden" value="{{number_format($item->amount, 2, '.', '')}}" name="donation_total[]">
														</div>
													</div>
													<div class="col-md-2">
													</div>
												</div>
												<div class="row">
													<div class="col-md-12" style="text-align:center">
														--------------------------------------------------------
													</div>
												</div>
											@endif
										</div>
									</div>
									@endforeach
									</div>
								</div>
							</div>
								<br>
									<div class="row">
										<div class="col-md-2">
										</div>
										<div class="col-md-4">
											<div class="col-md-12">
												<p><b>TOTAL AMOUNT: </b></p>
												<input type="hidden" name="total_amount" value="{{ $ret['amount'] }}">
											</div>
										</div>
										<div class="col-md-4">
											<div class="col-md-12" style="padding-left:0">
												<b>${{ $ret['amount'] }}</b>
											</div>
										</div>
										<div class="col-md-2">
										</div>
									</div>
									<div class="row">
										<div class="col-sm-2">
										</div>
										<div class="col-sm-8" align="center">
											<button type="submit" class="btn btn-default">Print</button>
											<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">Send Email</button>
											<button type="submit" class="btn btn-default" data-toggle="modal" data-target="#myModal">Print & Email Receipt</button>
										</div>
										<div class="col-sm-2">
										</div>
									</div>
							{{-- Donation --}}
						</form>
					@else
					<div class="alert alert-success" role="alert">{{ $ret['text'] }}.</div>
					@endif
					<div class="col-sm-4"></div>
					<div class="col-sm-4">
						@if(Auth::guest())
						<a href="{{ url('/organization/'.$slug) }}" class="btn btn-darkblue" style="margin: 10px 0 0 0;display: block;width:auto;">Back to Homepage</a>
						@else
						<a href="{{ url('/organization/'.$slug.'/user/dashboard') }}" class="btn btn-darkblue" style="margin: 10px 0 0 0; width:auto;">Back to Homepage</a>
						@endif
					</div>
					<div class="col-sm-4"></div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Send Email Receipt</h4>
				</div>
				<form action="{{ url('/organization/'.$slug.'/send-email-transaction')}}" method="post">
					<div class="modal-body">
						Email:<input type="text" class="form-control" name="email" required>
						<input type="hidden" value="{{ $email }}" name="user_email">
						<input type="hidden" value="{{ $ret['code'] }}" name="code">
						<input type="hidden" value="{{ (isset($ret['trans_id'])?$ret['trans_id'] : '') }}" name="trans_id">
						<input type="hidden" value="{{ (isset($ret['amount'])?$ret['amount'] : '') }}" name="amount">
						<input type="hidden" value="{{ (isset($ret['text'])?$ret['text']:'') }}" name="text">
						<input type="hidden" value="{{ $organization['name'] }}" name="organization_name">
						<input type="hidden" value="{{ $organization['contact_number'] }}" name="organization_contact_number">
						<input type="hidden" value="{{ $organization['email'] }}" name="organization_email">
						<input type="hidden" value="" name="date_now_to_email" id="date_now_to_email">
						@if(isset($cart))
							@foreach($cart as $item2)
							@if($item2->donation_category_id == 0 && $item2->event_id > 0)
							<input type="hidden" value="{{ $item2->id}}" name="event_product[]">
							<input type="hidden" value="{{ $item2->name}}" name="event_name[]">
							<input type="hidden" value="{{ $item2->description}}" name="event_description[]">
							<input type="hidden" value="{{ $item2->fee}}" name="fee[]">
							<input type="hidden" value="{{ $item2->qty }}" name="qty[]">
							<input type="hidden" value="{{ $item2->start_date}}" name="event_start_date[]">
							<input type="hidden" value="{{ $item2->end_date}}" name="event_end_date[]">
							<input type="hidden" value="{{ $item2->no_of_repetition}}" name="no_of_repetition[]">
							<input type="hidden" value="{{ $item2->recurring_end_date}}" name="recurring_end_date[]">
							<input type="hidden" value="{{ $item2->recurring}}" name="recurring[]">
							<input type="hidden" value="{{ $item2->amount}}" name="event_total[]">
							@endif
							@if($item2->donation_category_id > 0 && $item2->event_id == 0)
							<input type="hidden" value="{{ $item2->id}}" name="donation_product[]">
							<input type="hidden" value="{{ $item2->donation_type}}" name="donation_type[]">
							<input type="hidden" value="{{ $item2->donationList_title}}" name="donationList_title[]">
							<input type="hidden" value="{{ $item2->description}}" name="donation_description[]">
							<input type="hidden" value="{{($item2->start_date !== '0000-00-00 00:00:00')? $item2->start_date : '------------'}}"  name="donation_start_date[]">
							<input type="hidden" value="{{($item2->end_date !== '0000-00-00 00:00:00')? $item2->end_date : '------------'}}" name="donation_end_date[]">
							<input type="hidden" value="{{ $item2->frequency_title}}" name="frequency_title[]">	
							<input type="hidden" value="{{number_format($item2->amount, 2, '.', '')}}" name="donation_total[]">
							@endif
							<input type="hidden" value="{{ count($donation) }}" name="donation_count">
							<input type="hidden" value="{{ count($events)}}" name="event_count">
							@endforeach
							<input type="hidden" name="total_amount" value="{{ $ret['amount'] }}">
						@endif
					</div>
					<div class="modal-footer">
						<button type="submit" id="submit_email" class="btn btn-default">Send</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="sent" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Send Email Receipt</h4>
				</div>
				<div class="modal-body">
					Message Sent!
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	@endsection
	@section('script')
	<script>
	$("#event_panel").hide();
	$("#donation_panel").hide();

	if($("#event_count").val() > 0){
		// $("#event_panel").show();
		$("#event_panel").css('display', 'block');
	}

	if($("#donation_count").val() > 0){
		$("#donation_panel").css('display', 'block');
	}

		var ret_code ='{{ $ret["code"] }}';
		if(ret_code == 1){
			var transaction_details_count = '{{ (isset($transaction_details) ? count($transaction_details) : '')}}';
		}else{
			var transaction_details_count = '';
		}
		var offset = new Date().getTimezoneOffset();
		var now = moment.utc(new Date());
		var converted_now = new Date(now.utcOffset(offset * -1));
		var now_month = converted_now.getMonth()+1;
		var now_day = converted_now.getDate();
		var now_year = converted_now.getFullYear();
		var now_hour = converted_now.getHours();
		var now_minutes = converted_now.getMinutes();
		var now_A = '';
		if(now_hour == 0){
		  var now_result = '12';
		}
		else{
		  var now_result  = now_hour;
		}
		if(now_hour > 12){
		    now_A = 'PM';
		    now_hour = (now_hour - 12);

		    if(now_hour == 12){
		        now_hour = '00';
		        now_A = 'AM';
		    }
		}
		else if(now_hour < 12){
		    now_A = 'AM';
		}else if(now_hour == 12){
		    now_A = 'PM';
		}else if(now_hour == 0){
		    now_hour = '00';
		}
		if(now_minutes < 10){
		    now_minutes = "0" + now_minutes;
		}
		$('div#now').text(now_month+'/'+now_day+'/'+now_year+' ' + now_hour + ':' + now_minutes + ' ' + now_A);
		$('#date_now').val($('#now').text());
		$('#date_now_to_email').val($('#now').text());
		for (var i= 0; i < transaction_details_count; i++) {
			var start_date_id = moment.utc($('#start_date_id_'+ i).data('date'));
			var converted_start_date = new Date(start_date_id.utcOffset(offset * -1));
			var start_date_month = converted_start_date.getMonth()+1;
			var start_date_day = converted_start_date.getDate();
			var start_date_year = converted_start_date.getFullYear();
			var start_date_hour = converted_start_date.getHours();
			var start_date_minutes = converted_start_date.getMinutes();
			var start_date_A = '';
			if(start_date_hour == 0){
			  start_date_hour = '00';
			}
			else{
			  var start_date_result  = start_date_hour;
			}
			if(start_date_hour > 12){
			    start_date_A = 'PM';
			    start_date_hour = (start_date_hour - 12);

			    if(start_date_hour == 12){
			        start_date_hour = '00';
			        start_date_A = 'AM';
			    }
			}
			else if(start_date_hour < 12){
			    start_date_A = 'AM';
			}else if(start_date_hour == 12){
			    start_date_A = 'PM';
			}else if(start_date_hour == 0){
			    start_date_hour = '00';
			}
			if(start_date_minutes < 10){
			    start_date_minutes = "0" + start_date_minutes;
			}

				$('div#start_'+i).text(start_date_month+'/'+start_date_day+'/'+start_date_year+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A);
			// $('#event_start_date').val($('#start').text());
				$('#event_start_date_'+i).attr('value', $('#start_'+i).text());
				$('#start_date_timezone').attr('value', $('#start_'+i).text());
				$('#start_date_to_email_' + i).attr('value', $('#start_'+i).text());
				
		};


		for (var e= 0; e < transaction_details_count; e++) {
			var end_date_id = moment.utc($('#end_date_id_'+e).data('date'));
			var offset = new Date().getTimezoneOffset();
			var converted_end_date = new Date(end_date_id.utcOffset(offset * -1));
			var end_date_month = converted_end_date.getMonth()+1;
			var end_date_day = converted_end_date.getDate();
			var end_date_year = converted_end_date.getFullYear();
			var end_date_hour = converted_end_date.getHours();
			var end_date_minutes = converted_end_date.getMinutes();
			var end_date_A = '';
			if(end_date_hour == 0){
			  end_date_hour= '00';
			}
			else{
			  var end_date_result  = end_date_hour;
			}
			if(end_date_hour > 12){
			    end_date_A = 'PM';
			    end_date_hour = (end_date_hour - 12);

			    if(end_date_hour == 12){
			        end_date_hour = '00';
			        end_date_A = 'AM';
			    }
			}
			else if(end_date_hour < 12){
			// start_date_result = ((start_date_hour < 10) ? "0" + start_date_hour : start_date_hour);
			    end_date_A = 'AM';
			}else if(end_date_hour == 12){
			    end_date_A = 'PM';
			}else if(end_date_hour == 0){
			    end_date_hour = '00';
			}
			if(end_date_minutes < 10){
			    end_date_minutes = "0" + end_date_minutes;
			}
			$('div#end_'+e).text(end_date_month+'/'+end_date_day+'/'+end_date_year+' ' + end_date_hour + ':' + end_date_minutes + ' ' + end_date_A);
			$('#event_end_date_'+e).attr('value', $('#end_'+e).text());
			$('#end_date_to_email_' + e).attr('value', $('#end_'+e).text());
		};
		$('#span').hide();
	//    $('#submit_email').click(function()
	// {
	// 	url = "{{ url('/organization/'.$slug.'/send-email-transaction')}}";
	//           console.log(url);
	//     $.ajax({
	//         url: url,
	//         type:'POST',
	//         dataType: 'json',
	//         data:
	//         {
	//             email: 'charina10181990@gmail.com',
	//             message: 'gg',
	//             _token:'{{ csrf_token()}}'
	//         },
	//         success: function(msg)
	//         {
	//             alert('Email Sent');
	//         }
	//     });
	// });
	//TO DO:to fix start_date and end date on PDF 
	// var start_date_id_donation = moment.utc($('#start_date_id_donation').data('date'));
	// var converted_start_date_donation = new Date(start_date_id_donation.utcOffset(offset * -1));
	// var start_date_month_donation = converted_start_date_donation.getMonth()+1;
	// var start_date_day_donation = converted_start_date_donation.getDate();
	// var start_date_year_donation = converted_start_date_donation.getFullYear();
	// var start_date_hour_donation = converted_start_date_donation.getHours();
	// var start_date_minutes_donation = converted_start_date_donation.getMinutes();
	// var start_date_A_donation = '';
	// if(start_date_hour_donation == 0){
	//   start_date_hour_donation = '00';
	// }
	// else{
	//   var start_date_result_donation  = start_date_hour_donation;
	// }
	// if(start_date_hour_donation > 12){
	//     start_date_A_donation = 'PM';
	//     start_date_hour_donation = (start_date_hour_donation - 12);

	//     if(start_date_hour_donation == 12){
	//         start_date_hour_donation = '00';
	//         start_date_A_donation= 'AM';
	//     }
	// }
	// else if(start_date_hour_donation < 12){
	//     start_date_A_donation = 'AM';
	// }else if(start_date_hour_donation == 12){
	//     start_date_A_donation = 'PM';
	// }else if(start_date_hour_donation == 0){
	//     start_date_hour_donation = '00';
	// }
	// if(start_date_minutes_donation < 10){
	//     start_date_minutes_donation = "0" + start_date_minutes_donation;
	// }
	// $('div#donation_start').text(start_date_month_donation+'/'+start_date_day_donation+'/'+start_date_year_donation+' ' + start_date_hour_donation + ':' + start_date_minutes_donation + ' ' + start_date_A_donation);
	// $('#donation_start_date').val($('#donation_start').text());


	// var end_date_id_donation = moment.utc($('#end_date_id_donation').data('date'));
	// var offset = new Date().getTimezoneOffset();
	// var converted_end_date_donation = new Date(end_date_id_donation.utcOffset(offset * -1));
	// var end_date_month_donation = converted_end_date_donation.getMonth()+1;
	// var end_date_day_donation = converted_end_date_donation.getDate();
	// var end_date_year_donation = converted_end_date_donation.getFullYear();
	// var end_date_hour_donation = converted_end_date_donation.getHours();
	// var end_date_minutes_donation = converted_end_date_donation.getMinutes();
	// var end_date_A_donation = '';
	// if(end_date_hour_donation == 0){
	//   end_date_hour_donation = '00';
	// }
	// else{
	//   var end_date_result_donation  = end_date_hour_donation;
	// }
	// if(end_date_hour_donation > 12){
	//     end_date_A_donation = 'PM';
	//     end_date_hour_donation = (end_date_hour_donation - 12);

	//     if(end_date_hour_donation == 12){
	//         end_date_hour_donation = '00';
	//         end_date_A_donation = 'AM';
	//     }
	// }
	// else if(end_date_hour_donation < 12){
	// // start_date_result = ((start_date_hour < 10) ? "0" + start_date_hour : start_date_hour);
	//     end_date_A_donation = 'AM';
	// }else if(end_date_hour_donation == 12){
	//     end_date_A_donation = 'PM';
	// }else if(end_date_hour_donation == 0){
	//     end_date_hour_donation = '00';
	// }
	// if(end_date_minutes_donation < 10){
	//     end_date_minutes_donation = "0" + end_date_minutes_donation;
	// }
	// $('div#donation_end').text(end_date_month_donation+'/'+end_date_day_donation+'/'+end_date_year_donation+' ' + end_date_hour_donation + ':' + end_date_minutes_donation + ' ' + end_date_A_donation);
	// $('#donation_end_date').val($('#end').text());
	</script>
	@if(!empty(Session::get('success')))
	<script>
	$(function() {
		$('#sent').modal('show');
	});
	</script>
	@endif
	@endsection
