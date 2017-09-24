<!-- Modal -->
<div class="modal fade calendar-modal" id="event-modal" tabindex="-1" group="dialog" data-slug="{{ $slug }}" data-event_modal_url="{{ route('event_modal_details') }}" aria-labelledby="myModalLabel">
	<div class="modal-dialog" group="document">
		<div class="modal-content">

		</div>
	</div>
</div>


<div class="volunteer-group-filter-container">
	<div class="row">
		<div class="col-md-4 col-sm-5">
			<a href="{{ url('organization/'.$slug.'/events?slot_filter='.$alt_needing_volunteers) }}"  class="btn btn-darkblue btn-block" id="event-slot-filter">@if($needing_volunteers == "filtered")All Events @else Needs Volunteers @endif</a>
		</div>
		<div class="col-md-4 col-sm-2">
		</div>
		<div class="col-md-4 col-sm-5">
			<a href="#" type="button" class="btn btn-darkblue btn-block" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart <span class="badge badge-danger">{{count($cart)}}</span></a>
			{{-- <a href="#" type="button" class="btn btn-darkblue btn-block" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart</a>
			<div class="cart-count-event">
				<p>{{count($cart)}}</p>
			</div> --}}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div id="calendar"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="calendar-legend well text-center">
			<div class="row">
				<div class="col-xs-4">
					<i class="fa fa-square" style="color: #C0C0C0;"></i> Past Events
				</div>
				<div class="col-xs-4">
					<i class="fa fa-square" style="color: #2874A6;"></i> Today's Events
				</div>
				<div class="col-xs-4">
					<i class="fa fa-square" style="color: #056E0E;"></i> Future Events
				</div>
			</div>
		</div>
	</div>
</div>
<div class="load_icon"><!-- Place at bottom of page --></div>

@section('script')
<!-- temporary -->
<script>
$(document).ready(function() {
	$body = $("body");

	$(document).on({
		ajaxStart: function() { $body.addClass("loading");    },
		ajaxStop: function() { $body.removeClass("loading"); }
	});
	    $(".delete_modal").click( function (){
        $('.delete-confirmation').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation').css('display','none');
    });
	var filter_events = "{{ route('filter_events') }}";
	var token = "{{ csrf_token() }}";
	var fetch_events = "{{ route('fetch_events', [$organization_id, $needing_volunteers]) }}";
	$.get(fetch_events,function(data){
		//instanceCalendar('#calendar', data, token);
		$("#calendar").empty();
		//singleEvent('#calendar', data, token);
		// data = JSON.parse(data);		
		data['no_of_repetition'] = parseInt(data['no_of_repetition'],10);
		console.log('-----------------'+data['no_of_repetition']);
		recurringEvent('#calendar', data, token);

	});
	slot_filter = $("#event-slot-filter");
	$('#group-filter-input').change(function(){
		$.post(filter_events,{_token:token,slot_filter: slot_filter.data('toggle'),type: $("#group-filter-input").val()}).done(
			function(data){
				$("#calendar").empty();
				//instanceCalendar('#calendar', data);
				//singleEvent('#calendar', data, token);
				recurringEvent('#calendar', data, token);
			}
		);
	});
	$("#event_panel").hide();
    $("#donation_panel").hide();

    if($("#event_count").val() > 0){
        $("#event_panel").show();
    }

    if($("#donation_count").val() > 0){
        $("#donation_panel").show();
    }
});
</script>
@endsection
