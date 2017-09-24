@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<br>
<div class="modal fade calendar-modal" id="event-modal" tabindex="-1" role="dialog" data-slug="{{ $slug }}" data-event_modal_url="{{ route('event_modal_details') }}" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
<br/>
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-12">
                <div class="volunteer-role-filter-container">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="control-label" for="role-filter-input">Filter by Role:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select id="role-filter-input" class="form-control" type="text" name="volunteer_role_id">
                                    <option value="" selected>All</option>
                                    @foreach($volunteer_role_titles as $title)
                                    <option value="{{ $title }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-7">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="event-list-container"></div>
    </div>
</div>

@endsection
@section('script')
<script type="text/javascript">
var filter_events = "{{ route('filter_events',[TRUE,$slug]) }}";
var token = "{{ csrf_token() }}";
function filterEvents(post_url,title,token){
    $.post(post_url,{_token:token,role_title: title,slot_filter: "filtered"}).done(
        function(data){
            $("#event-list-container").empty().html(data);
        }
    );
}


loadEvent = function(e){
    var event = new Object();
    event.id = $("#"+ e.id).data('event_id');
    event.start = $("#"+"start_date_"+e.id).data('date');
    event.end = $("#"+"end_date_"+e.id).data('date');
    loadEventDetails(event);
}

$(document).ready(function(){
    filterEvents(filter_events,"",token);
});
//alert(events);


$('#role-filter-input').change(function(){
    filterEvents(filter_events,$("#role-filter-input").val(),token);
});
</script>
@endsection
