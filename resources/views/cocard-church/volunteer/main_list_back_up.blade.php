<style type="text/css">
tr td{
    cursor: pointer;
}
</style>
<div class="table-responsive" id="table-content">
	<div align="center" style="height:100%; padding-top:10%" class="loading"></div>
    <table class="table table-hover table-bordered" id="main-table">
        <thead class="theader">
        	<td>Volunteer Group</td>
        	<td>Event</td>
        	<td>Start Date</td>
        	<td>End Date</td>
        	<td>Required</td>
        	<td>Pending</td>
        </thead>
        <tbody>
        	<?php $x= 0 ?>
        	@foreach($volunteer_group_by_field as $group)
        	<tr class="volunteer_row" id="row_{{ $x }}" onclick="groupRow(this)"  data-event_name="{{ $group->event->name }}" data-event_id="{{ $group->event->id }}" data-volunteer_type="{{ $group->type }}">
        		<td>{{ $group->type}}</td>
        		<td>{{ $group->event->name}}</td>
        		{{-- <td>{{ $group->event->dateFormat($group->event->start_date)}}</td> --}}
                <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $group->event->start_date }}" style="display:none"> &nbsp; </span>
                <td data-id="data_start_{{ $group->id }}" id="start_date_timezone_{{ $x }}"></td>
        		{{-- <td>{{ $group->event->dateFormat($group->event->recurring_end_date)}}</td> --}}
                <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $group->event->voluteerRecurringEndDate($group->event->recurring_end_date) }}" style="display:none"> &nbsp; </span>
                <td data-id="data_end_{{ $group->id }}" id="end_date_timezone_{{ $x }}"></td>
        		<td>{{ ($volunteer_groups->allVolunteersNeeded($group) == '') ? '0' : $volunteer_groups->allVolunteersNeeded($group)}}</td>
        		<td>{{ ($volunteers->allPending($group) == '')?'0': $volunteers->allPending($group) }}</td>
        	</tr>
        	<?php $x++?>
        	@endforeach
        </tbody>
    </table>
    @if(count($volunteer_group_by_field) == 0)
        <div style="text-align:center; font-size:18px"><cite with="100%">No record to show</cite></div>
    @endif
    {{$volunteer_group_by_field->render()}}
</div>
<script type="text/javascript">
$('.loading').css('display','none');
$('#back-main-list').css('display','none');
groupRow = function(e){
	var url = "{{ route('church_volunteer_list',$slug) }}";
	var volunteer_groups = new Object();
    volunteer_groups.event_name = $("#"+ e.id).data('event_name');
    volunteer_groups.volunteer_type = $("#"+ e.id).data('volunteer_type');
    volunteer_groups.event_id = $("#"+ e.id).data('event_id');
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,event_id:volunteer_groups.event_id}).done(function(data){
    	$('#table-content').empty().html(data);
    });
    timeoutLoader();
}
timeoutLoader = function(){
    var timeout;
    function startLoad() {
        
        $('#main-table').css('display','none');
        $('.loading').css('display','block');
        $('.loading').css('width','100%');

        $('.loading').html('<img style="height:30px; width:30px" src="{{asset('images/ajax-loader.gif')}}"/>');
    }
    function loaded() {
        $('.loading').html('Please try Again.');
    }
    clearTimeout(timeout);
    timeout = setTimeout(loaded, 20500);
    $('.volunteer_row').click(startLoad);
    startLoad();
}
// groupRow();

    var limit = '';
    var created_date = [];
    var offset = [];
    var converted_created_date = [];
    var month3 = [];
    var day3   = [];
    var year3  = [];
    var date = '';
    convertion = function(date, limit){
        for (var i = 0; i <= limit; i++) {
            created_date[i] = moment.utc($(date+ i ).data('date'));
            offset = new Date().getTimezoneOffset();
            converted_created_date[i] = new Date(created_date[i].utcOffset(offset * -1));
            month3[i] = converted_created_date[i].getMonth()+1;
            day3[i]   = converted_created_date[i].getDate();
            year3[i]  = converted_created_date[i].getFullYear();
    

            if($(date+ i ).data('date') != '0000-00-00 00:00:00'){
                $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i]);
                $(date + 'reminder_'+ i ).val(month3[i] + '/' + day3[i] + '/' + year3[i]);
            }else{
                $(date + 'timezone_'+ i ).text('-------');
            }
            $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
            $(date + i ).hide();

        }
    }
var limit = '{{ count($volunteer_group_by_field)}}';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';

convertion(start_date_var, limit);
convertion(end_date_var, limit);
</script>