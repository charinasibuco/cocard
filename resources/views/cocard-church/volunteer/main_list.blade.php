<style type="text/css">
tr td{
    cursor: pointer;
}
.sub-item{
    font-size: 12px;
    font-style: italic;
}
.header td{
    font-size: 12px;
    text-decoration: underline;
}
</style>
<div align="center" style="height:100%; padding-top:10%" class="loading"></div>
<div class="table-responsive" id="table-content">
	
   <table  class="table table-striped">
        <thead>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Pending</th>
        </thead>
        <tbody>
            <?php $x = $i = 0;  ?>
             @foreach($events as $event)
              @if(count($event->VolunteerGroup) > 0)
                    <tr id="event-row-{{ $x }}" data-id="{{ $x }}" onclick="showVolunteerGroups(this)">
                        <td>{{ $event->name }}</td>
        {{--                 <td>{{ $event->start_date }}</td> --}}
                        <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $event->start_date }}" style="display:none"> &nbsp; </span>
                        <td data-id="data_start_{{ $x }}" id="start_date_timezone_{{ $x }}"></td>
                 {{--        <td>{{ $event->end_date }}</td> --}}
                        <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $event->voluteerRecurringEndDate($event->recurring_end_date) }}" style="display:none"> &nbsp; </span>
                        <td data-id="data_end_{{ $x }}" id="end_date_timezone_{{ $x }}"></td>
                        <td>{{ ($volunteers->allPending($event,'event') == '')?'0': $volunteers->allPending($event,'event') }}</td>
                    </tr>
                    <tr  class="table table-striped header" id="volunteer-group-head-{{ $x }}">
                        <td style="padding-left:20px">Volunteer Group Name</td>
                        <td>Required</td>
                        <td>Pending</td>
                        <td></td>
                    </tr>

                    @foreach($event->VolunteerGroupByTypeAdmin as $volunteer_group)
                        <tr class="sub-item" id="volunteer-group-row-{{ $x }}" data-id="{{ $x }}" data-event_name="{{ $event->name }}" data-volunteer_type="{{ $volunteer_group->type}}" data-event_id="{{ $event->id}}" onclick="groupRow(this)" >
                            <td style="padding-left:20px">
                            &nbsp;&nbsp;<i class="fa fa-caret-right" aria-hidden="true"></i>&nbsp;{{ $volunteer_group->type}}<br>
                            </td>
                            <td>{{ $volunteers->allVolunteersApproved($event->id,$volunteer_group->type) }}/{{ ($volunteer_group->allVolunteerGroupsNeeded($event,$volunteer_group) == '') ? '0' : $volunteer_group->allVolunteerGroupsNeeded($event,$volunteer_group)}}</td>
                            <td>{{ ($volunteers->allPending($volunteer_group,'volunteer_group') == '')?'0': $volunteers->allPending($volunteer_group,'volunteer_group') }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                
             @endif
             <?php $x++; ?>
            @endforeach
        </tbody>
    </table>
    {{--     @foreach($events as $event)
            @if(count($event->VolunteerGroup) > 0)
                {{ $event->name }}<br>
                    @foreach($event->VolunteerGroupByType as $volunteer_group)
                        --{{ $volunteer_group->type}}<br>
                    @endforeach
            @endif
        @endforeach
    --}}
    @if(count($events) == 0)
        <div style="text-align:center; font-size:18px"><cite with="100%">No record to show</cite></div>
    @endif
    {{$events->render()}}
</div>
<script type="text/javascript">
//on load execute this function
onLoadPage = function(){
    $('.loading').css('display','none');
    $('#back-main-list').css('display','none');
    $('#main-list-title').css('display','block');
    $('#list-title').css('display','none');
}
onLoadPage();
//Show rows of schedule date under each Volunteer Group Listed
groupRow = function(e){
	var url = "{{ route('church_volunteer_list',$slug) }}";
	var volunteer_groups = new Object();
    var x = $(e).data('id');
    var start_date = $('#start_date_timezone_'+x).text();
    var end_date = $('#end_date_timezone_'+x).text();
    volunteer_groups.event_name = $(e).data('event_name');
    volunteer_groups.volunteer_type = $(e).data('volunteer_type');
    volunteer_groups.event_id = $(e).data('event_id');
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    timeoutLoader();
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,event_id:volunteer_groups.event_id,start_date:start_date,end_date:end_date}).done(function(data){
        $('.loading').css('display','none');
        $('#table-content').css('display','block');
    	$('#table-content').empty().html(data);
    });
    
}

//to hide all details under each Event on initial load
var limit = '{{ count($events)}}';
for (var x = 0; x < limit; x++) {
    var vg_head = '#volunteer-group-head-'+x;
    var vg_row = '#volunteer-group-row-'+x;
    $(vg_row+' td').hide();
    $(vg_head).hide();
} 

//to show Volunteer Group under each Event
showVolunteerGroups = function(e){
    var x = $("#"+ e.id).data('id');
    // var vg_row = '#volunteer-group-table-'+x;
    // $(vg_row).toggle(200);
    var vg_head = '#volunteer-group-head-'+x;
    var vg_row = '#volunteer-group-row-'+x;
    $(vg_row+' td').toggle(400);
    $(vg_head).toggle(400);
}

//gif loader while still processing groupRow function
timeoutLoader = function(){
    var timeout;
    function startLoad() {
        
        $('#main-table').css('display','none');
        $('#table-content').css('display','none');
        $('.loading').css('display','block');
        $('.loading').css('width','100%');

        $('.loading').html('<img style="height:30px; width:30px" src="{{asset('images/ajax-loader.gif')}}"/>');
    }
    function loaded() {
        $('.loading').html('Please try Again.');
    }
    clearTimeout(timeout);
    timeout = setTimeout(loaded, 30500);
    $('.volunteer_row').click(startLoad);
    startLoad();
}

// this section is for date time convertion from UTC date time(fom database) 
// to user browser timezone shown to this blade
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

var limit = '{{ count($events)}}';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';

convertion(start_date_var, limit);
convertion(end_date_var, limit);
</script>