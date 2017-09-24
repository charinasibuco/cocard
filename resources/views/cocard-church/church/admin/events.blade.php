@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<!-- Modal -->

<div class="modal fade calendar-modal" id="backofc-modal" tabindex="-1" role="dialog" data-slug="{{ $slug }}" data-event_modal_url="{{ url('/organization/'.$slug.'/administrator/event_modal') }}" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

		</div>
	</div>
</div>
<div class="modal fade calendar-modal-add" id="add-modal" tabindex="-1" role="dialog" data-slug="{{ $slug }}" data-event_modal="{{ url('/organization/'.$slug.'/administrator/add_event_modal') }}" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

		</div>
	</div>
</div>
<div class="d-content calendar-events">
	<div class="margin-mob-top"  style="margin-bottom:10px;">
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
		<div class="row">
			<div class="col-md-6">
				<h3>Events</h3>
			</div>
			<div class="col-md-6">
				<h3>
					<div class="clearfix">
						@can('add_event')
						<a href="{{ url('/organization/'.$slug.'/administrator/events/create')}}" class="btn btn-darkblue float-right">
							Add Events&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
						</a>
						@endcan
					</div>
				</h3>
			</div>
		</div>
		@if(Session::has('message'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('message') }}
		</div>
		@endif
		<div id="calendar-view">
			<div class="container content calendar">
				<div id="calendar"></div>
			</div>
		</div>
		<div class="row" style="text-align: center;">
			<div class="col-md-12 well">
				<h5>LEGEND:</h5>
				<div class="float-left" style="width: 33%;">
					<div style="height:20px; width:40px; background: #C0C0C0; float: left;margin-left: 20%;"></div>
					<div>&nbsp;Past Events</div>
				</div>
				<div class="float-left" style="width: 33%;">
					<div style="height:20px; width:40px; background: #2874A6; float: left;margin-left: 20%;"></div>
					<div>&nbsp;Today's Events</div>
				</div>
				<div class="float-left" style="width: 33%;">
					<div style="height:20px; width:40px; background: #056E0E; float: left;"></div>
					<div>&nbsp;Future Events</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="load_icon"><!-- Place at bottom of page --></div>
@endsection
@section('script')

<script type="text/javascript">
$(document).ready(function() { 
	$body = $("body"); 
	$(document).on({
		ajaxStart: function() { $body.addClass("loading");    },
		ajaxStop: function() { $body.removeClass("loading"); }
	});

	var filter_events = "{{ route('filter_events') }}";
	var token = "{{ csrf_token() }}";
	var fetch_events = "{{ route('fetch_events', $organization->id) }}";
	$.get(fetch_events,function(data){
		//instanceCalendar('#calendar', data, token);
		recurringEvent('#calendar', data, token);
		//singleEvent('#calendar', data, token);
		//console.log(data);
	});
	slot_filter = $("#event-slot-filter");
	$('#group-filter-input').change(function(){
		$.post(filter_events,{_token:token,slot_filter: slot_filter.data('toggle'),type: $("#group-filter-input").val()}).done(
			function(data){
				$("#calendar").empty();
				//instanceCalendar('#calendar', data);
				singleEvent('#calendar', data, token);
			}
		);
	});
	$('#event-slot-filter').click(function(){
		if(slot_filter.data("toggle") == "filtered"){
			slot_filter.data("toggle","unfiltered");
		}else{
			slot_filter.data("toggle","filtered");
		}

		if(slot_filter.hasClass("active")){
			slot_filter.removeClass("active");
		}else{
			slot_filter.addClass("active");
		}
		$.post(filter_events,{_token:token,slot_filter: slot_filter.data('toggle'),type: $("#group-filter-input").val()}).done(
			function(data){
				$("#calendar").empty();
				//instanceCalendar('#calendar', data);
				singleEvent('#calendar', data, token);
			}
		);
	});
});
</script>

@endsection
