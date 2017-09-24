@extends('layouts.app')
@include('cocard-church.user.navigation')
<style type="text/css">
thead{
    padding: 5px;
}
</style>
@section('content')
<div class="modal fade calendar-modal" id="event-modal" tabindex="-1" role="dialog" data-slug="{{ $slug }}" data-event_modal_url="{{ route('volunteer_group_modal_details') }}" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
<div class="d-content" id="volunteer-group">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6" id="main-list-title">
                <h3>Events with Volunteer Groups</h3>
            </div>
            <div class="col-md-6" id="list-title" style="display:none">
                <h4></h4>
                <h6></h6>
                <cite id="start_date"></cite></br>
                <cite id="end_date"></cite></br>
            </div>
            <div class="col-md-6">
                <div class="clearfix">
                    <a id="back-main-list" class="btn btn-darkblue float-right" style="display:none">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                    </a>
                </div>
            </div>
        </div>
        </br>
        <div class="row">
            <div class="col-sm-12" id="volunteer-main-table">
                
            </div>
        </div>
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
function volunteerTable(){
    var slug ='{{ $slug }}';
    var volunteer_group_id= '{{ $volunteer_groups }}';
    $.get('{{ route("volunteer_table",$page) }}',{slug:slug}).done(function(data){
        $('#volunteer-table').empty().html(data);
    });
    $('#pagination').css('display','block');
}
function volunteerMainTable(){
    var slug ='{{ $slug }}';
    var volunteer_group_id= '{{ $volunteer_groups }}';
    $.get('{{ route("volunteer_main_table",$page) }}',{slug:slug}).done(function(data){
        $('#volunteer-main-table').empty().html(data);
    });
    $('#pagination').css('display','block');
}

loadEvent = function(e){
    var event = new Object();
    // event.id = $("#"+ e.id).data('volunteer_id');
    event.id = $(e).data('volunteer_id');
    // event.volunteer_group_id = $("#"+ e).data('volunteer_id');
    event.volunteer_group_id = $(e).data('volunteer_id');
    loadEventDetails(event);
}

$(document).ready(function(){
    filterEvents(filter_events,"",token);
    //volunteerTable();
    volunteerMainTable();
});
//alert(events);
$('#pagination').css('display','none');

$('#role-filter-input').change(function(){
    filterEvents(filter_events,$("#role-filter-input").val(),token);
});
$('#event-modal').on('hidden.bs.modal', function () {
     volunteerTable();
});
$('#back-main-list').on('click',function(){
    volunteerMainTable();
});

</script>
@endsection

