@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<div class="row">
			<div class="col-md-3">
				<h3>My Donations</h3>
			</div>
			<div class="col-md-7">
				<h3>
					<form method="GET" action="">
				        <div class="col-md-6">
				            <div class="form-group">
				               	<select class="form-control" name="donation_type">
				                    <option value="All" @if($donation_type == 'All') selected @endif>All Donations</option>
				                    <option value="One-Time" @if($donation_type == 'One-Time') selected @endif>One-Time Donations</option>
				                    <option value="Recurring" @if($donation_type == 'Recurring') selected @endif>Recurring Donations</option>
				                </select>
				            </div>
				        </div>
				        <div class="col-md-4">
				            <button type="submit" class="btn btn-default">Go</button>
				        </div>
				    </form>
				</h3>
			</div>
			<div class="col-md-2">
				<h3>
					<div class="clearfix">
						<a href="" class="btn btn-darkblue float-right" id="export_donation">
							Export My Donations&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>
						</a>
					</div>
				</h3>
			</div>
		</div>
		<div class="table-main panel panel-default">
		<div class="table-responsive"  id="paginated_donation">
			<table class="table table-striped table-responsive" id="tabledata">
				<thead class="theader">
					<th>Type</th>
					<th>Category</th>
					<th>Name</th>
					<th>Frequency</th>
					<th>Amount</th>
					<th>Date of Transaction</th>
					<th>Status</th>
					<th></th>
				</thead>
				<tbody>
					<?php
			            $x = 0;
			        ?>
					@foreach($donations as $donation)
					<?php
			            $x++;
			        ?>	
					<div class="delete-modal-container" data-id="{{$donation->id}}">
					    <div class="modal-delete">
					        <div class="modal-header">
					            <h5>My Donation: Cancel {{$donation->Name}}.</h5>
					        </div>
					        <div class="modal-body">
					            <p>Are you sure you want to cancel this donation?</p>
					        </div>
					        <div class="modal-footer">
					            <div class="row">
					                <div class="col-md-6">
					                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ url('organization/'.$slug.'/user/donation/cancel-donation/'.$donation->id) }}">
					                        YES
					                    </a>
					                </div>
					                <div class="col-md-6">
					                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$donation->id}}">Cancel</button>
					                </div>
					            </div>
					        </div>
					    </div>
					</div>

					<tr>
						
						@if($donation->DonationListId != 0)
							<td>{{$donation->Type}}</td>
							<td>{{$donation->Category}}</td>
							<td>{{$donation->Name}}</td>
							<td>{{isset($donation->Frequency)? $donation->Frequency : 'N/A'}}</td>
							<td>{{$donation->Amount }}</td>
		{{-- 					<td>{{ $newdate }} </td> --}}
							<span  style="display:none" class="dataTz" id="created_date_{{ $x }}" data-date="{{  $donation->Date }}"> &nbsp; </span>
	                        <td id="created_date_timezone_{{ $x }}"></td>
	                      {{--   <td><input type="text" value="" name="input_created_date_timezone[]" id="input_created_date_timezone_{{ $x }}"></td> --}}
							<td>
								@if($donation->Type == "Recurring")
									@if($donation->status == "Active")
										On Going
									@elseif($donation->status == "InActive")
										Cancelled
									@else
										{{ $donation->status }}
									@endif
								@else
									{{ $donation->status }}
								@endif
							</td>
							@if($donation->Type == "Recurring" && $donation->status == "Active")
							<td>
	                            <a class="delete_modal" data-id="{{$donation->id}}" style="cursor:pointer;" title="Cancel Donation">
	            					<span class="fa-stack fa-lg icon-delete">
	            						<i class="fa fa-square fa-stack-2x"></i>
	            						<i class="fa fa-times fa-stack-1x"></i>
	            					</span>
	            				</a>
							</td>
							@endif
						@endif

					</tr>
					@endforeach
				</tbody>
			</table>
			{{ $donations->render() }}
		</div>
		@include('cocard-church.user.all_donation')
		</div>
	</div>
</div>

@endsection
@section('script')
<script type="text/javascript">
	var limit = '';
	var created_date = [];
	var offset = [];
	var converted_created_date = [];
	var month3 = [];
	var day3   = [];
	var year3  = [];
	var hour3  = [];
	var minutes3 = [];
	var A3 = [];
	var result3 = [];
	var date = '';
	convertion = function(date, limit){
		for (var i = 1; i <= limit; i++) {
		created_date[i] = moment.utc($(date+ i ).data('date'));
		offset = new Date().getTimezoneOffset();
		converted_created_date[i] = new Date(created_date[i].utcOffset(offset * -1));
	    month3[i] = converted_created_date[i].getMonth()+1;
	    day3[i]   = converted_created_date[i].getDate();
	    year3[i]  = converted_created_date[i].getFullYear();
	    hour3[i]  = converted_created_date[i].getHours();
	    minutes3[i] = converted_created_date[i].getMinutes();
	    A3[i] = '';
	    if(hour3[i] == 0){
	      result3[i] = '12'; 
	    }
	    else{
	      result3[i]  = hour3[i];  
	    }
	    if(hour3[i] > 12){
	        A3[i] = 'PM';
	        hour3[i] = (hour3[i] - 12);

	        if(hour3[i] == 12){
	            hour3[i] = '00';
	            A3[i] = 'AM';
	        }
	    }
	    else if(hour3[i] < 12){
	        A3[i] = 'AM';   
	    }else if(hour3[i] == 12){
	        A3[i] = 'PM';
	    }else if(hour3[i] == 0){
	        hour3[i] = '00';
	    }
	    if(minutes3[i] < 10){
	        minutes3[i] = "0" + minutes3[i]; 
	    }
	    if(hour3[i] == 0){
            hour3[i] = '12';
        }
	    $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
	    $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
	    $(date + i ).hide();

		}
	}
// });
$("#export_donation").click(function(){
     $("#submit_donations").trigger('click');
    return false;
    });
var donations_limit = {{ count($donations_all)}};
var created_date_var = '#created_date_';
convertion(created_date_var, donations_limit);
</script>
@endsection