@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-sm-12">
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
			<button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart</button>
			<table>
				<thead>
					<th>Name</th>
					<th>Description</th>
					<th>Capacity</th>
					<th>Pending Participants</th>
					<th>Fee</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Reminder Date</th>
					<th>Volunteer Number</th>
					<th>Pending Volunteers</th>
					<th>Action</th>
				</thead>
				<tbody>
					@foreach($events as $event)
					<tr>
						<td>{{ $event->name }}</td>
						<td>{{ $event->description }}</td>
						<td>{{ $event->capacity }}</td>
						<td>{{ $event->pending }}</td>
						<td>{{ $event->fee }}</td>
						<td>{{ $event->start_date }}</td>
						<td>{{ $event->end_date }}</td>
						<td>{{ $event->reminder_date }}</td>
						<td>{{ $event->volunteer_number }}</td>
						<td>{{ $event->volunteer_number}}</td>
						<td>
							<button type="button" data-toggle="modal" data-id="{{$event->id}}" class="modalLink join" data-target="#joinModal{{$event->id}}">Join</button>
							<button type="button" data-toggle="modal" data-target="#volunteerModal{{$event->id}}">Volunteer</button>
						</td>
					</tr>
					<!--Join Modal -->
					<div id="joinModal{{$event->id}}" class="modal fade join {{$event->id}}" role="dialog">
					  <div class="modal-dialog">
					    <!-- Modal content-->
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">Event Details</h4>
					      </div>
					      <div class="modal-body">
					      		{{-- <form action="{{ route('post_join')}}" method="post" class="event" id="event"> --}}
					      		<form action="{{ url('/organization/'.$slug.'/event/add-to-cart/') }}" method="post" class="event" id="event">
						      	<label>Name of Event:</label>
						      	{{$event->name}}</br>
						      	<label>Description:</label>
						      	{{$event->description}}</br>
						      	<label>Available Slots:</label>
						      	{{$event->capacity - $event->pending}}</br>
						      	<label>Fee:</label>
						      	{{$event->fee}}</br>
						      	<label>Start Date:</label>
						      	{{$event->start_date}}</br>
						      	<label>End Date:</label>
						      	{{$event->end_date}}</br>
						      	<div class="row">
						      		<div class="col-sm-4">
							        	<input class="form-control qty" id="qty{{ $event->id}}" name="qty" placeholder="Number of Tickets" value="">
							        	<input type="hidden" name="slug" value="{{ $slug }}">
							        	<input type="hidden" name="event_id" id="event_id" value="{{ $event->id }}">
							        	<input type="hidden" class="fee" id="fee{{ $event->id}}" value="{{$event->fee}}" >
							        </div>
							        <div class="col-sm-4">
							        	<input class="form-control total" id="total{{ $event->id}}" name="total" placeholder="Total Due" value="">
							        </div>
							        <div class="col-sm-4">
							        </div>
							        <input type="hidden" name="user_id" value="{{ (Auth::user())? Auth::user()->id : 0 }}">
							        {!! csrf_field() !!}
							    </div>
							    <div class="row">
							    	<div class="col-sm-12">
								    	{{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								        <input type="submit" name="join" class="btn btn-default"> --}}
								        <button type="button" class="btn btn-default" id="clear{{ $event->id}}">Clear</button>
								        <button type="submit" class="btn btn-default">Add to Cart</button>
								        {{-- <input type="submit" name="join" class="btn btn-default"> --}}
								    </div>
							    </div>
					        </form>
					      </div>
					      <div class="modal-footer">
					      </div>
					    </div>
					  </div>
					</div>
					<!--Volunteer Modal -->
					<div id="volunteerModal{{$event->id}}" class="modal fade" role="dialog">
					  <div class="modal-dialog">
					    <!-- Modal content-->
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">Volunteer Details</h4>
					      </div>
					      <div class="modal-body">
					        <p>Volunteer Modal.</p>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      </div>
					    </div>
					  </div>
					</div>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@include('cocard-church.donation.modal')
@endsection
@section('script')
	<script type="text/javascript">
	$(".modalLink").click(function () {
	var passedID = $(this).attr('data-id');
		$('form.event').each(function() {
			var event_id = passedID;
			$('#qty'+ event_id +', #fee'+ event_id).on('input',function() {
			    var qty = parseInt($('#qty'+ event_id).val());
			    var fee = parseFloat($('#fee'+ event_id).val());
			    $('#total'+ event_id).val((qty * fee ? qty * fee : 0).toFixed(2));

			});
			$('#clear'+ event_id).on('click', function() {
				$('#qty'+ event_id).val('');
				$('#total'+ event_id).val('0.00');
			})
		});

	 });
	</script>
@stop
