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
<div class="table-responsive" id="table-content">
	<div align="center" style="height:100%; padding-top:10%" class="loading"></div>
    <table class="table table-hover table-bordered" id="main-table">
        <thead class="theader">
        	<td>Event Name</td>
        	<td>Start Date</td>
        	<td>End Date</td>
        	<td  style="display:none">Pending</td>
        </thead>
        <tbody>
        	<?php $x= 0 ?>
        	@foreach($events as $event)
            	<tr class="volunteer_row" id="row_{{ $x }}" data-id="{{ $x }}"onclick="showVolunteerGroupsByType(this)"  data-event_name="{{ $event->name }}"  data-event_id="{{ $event->id }}">
            		<td>{{ $event->name}}</td>
                    <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $event->start_date }}" style="display:none"> &nbsp; </span>
                    <td data-id="data_start_{{ $x }}" id="start_date_timezone_{{ $x }}"></td>
                    <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $event->voluteerRecurringEndDate($event->recurring_end_date) }}" style="display:none"> &nbsp; </span>
                    <td data-id="data_end_{{ $x }}" id="end_date_timezone_{{ $x }}"></td>
            	</tr>
                <tr  class="table table-striped header" id="volunteer-group-head-{{ $x }}">
                    <td  style="padding-left:20px">Volunteer Group Name</td>
                    <td>Required</td>
                    <td></td>
                </tr>
                @foreach($event->VolunteerGroupByType as $volunteer_group)
                    <tr class="sub-item" id="volunteer-group-row-{{ $x }}" data-id="{{ $x }}" data-event_name="{{ $event->name }}" data-volunteer_type="{{ (str_replace(' ', '-', strtolower($volunteer_group->type)))}}" data-volunteer_group_id="{{ $volunteer_group->id }}" data-event_id="{{ $event->id}}" onclick="showVolunteerGroups(this)">
                        <td style="padding-left:20px;">
                        &nbsp;&nbsp;<i class="fa fa-caret-right" id="arrow-{{ $volunteer_group->id }}" aria-hidden="true"></i>&nbsp;{{ $volunteer_group->type}}<br>
                        </td>
                        <td>{{ $volunteers->allVolunteersApproved($event->id,$volunteer_group->type) }}/{{ ($volunteer_group->allVolunteerGroupsNeeded($event,$volunteer_group) == '') ? '0' : $volunteer_group->allVolunteerGroupsNeeded($event,$volunteer_group)}}</td>
                        <td></td>
                    </tr>
                    <?php $y =0; ?>
                    <tr  class="table table-striped header volunteer-group {{ $event->strToLower($event->name)}}_{{ $event->id }}" id="volunteer-group_{{ $x }}_{{ $event->id }}_{{ $volunteer_group->strToLower($volunteer_group->type) }}">
                        <td  style="padding-left:40px">Start Date</td>
                        <td>End Date</td>
                        <td>Volunteers Needed</td>
                    </tr>
                    @foreach($event->volunteerGroupsUnderType($volunteer_group->type) as $vg)
                        <tr class="sub-item volunteer-group {{ $event->strToLower($event->name)}}_{{ $event->id }}" id="volunteer-group-{{ $vg->strToLower($vg->type) }}-{{ $event->id }}" data-id="{{ $x }}" data-volunteer_type="{{ $vg->type }}" data-volunteer_id="{{ $vg->id }}" data-count="{{ count($event->volunteerGroupsUnderType($volunteer_group->type))}}" onclick="loadEvent(this)">
                            <span class="dataTz" id="vg_start_date_{{ $y }}_{{ $event->id }}_{{ $vg->strToLower($vg->type) }}" data-vg_id="{{ $vg->id }}" data-date="{{ $vg->start_date }}" style="display:none">&nbsp;</span>
                            <span class="dataTz" id="vg_end_date_{{ $y }}_{{ $event->id }}_{{ $vg->strToLower($vg->type) }}" data-vg_id="{{ $vg->id }}" data-date="{{ $vg->end_date }}" style="display:none">&nbsp;</span>
                            <td style="padding-left:40px;"><span id="vg_start_date_timezone_{{ $y }}_{{ $event->id }}_{{ $vg->strToLower($vg->type) }}"></span></td>
                            <td><span id="vg_end_date_timezone_{{ $y }}_{{ $event->id }}_{{ $vg->strToLower($vg->type) }}"></span></td>
                            <td>{{$vg->volunteers_approved}}/{{ $vg->volunteers_needed}}</td>
                        </tr>
                        <?php $y++?>
                     @endforeach
                @endforeach
        	<?php $x++?>
        	@endforeach
        </tbody>
    </table>
    @if(count($volunteer_group_by_field) == 0)
        <div style="text-align:center">
            <cite>No Records to show</cite>
        </div>
    @endif
    <div id="pagination"></div>
</div>

<script type="text/javascript">
$('.loading').css('display','none');
var render_page = '{!! $volunteer_group_by_field->render() !!}';
$('#pagination').html(render_page);
$('#back-main-list').css('display','none');
$('#main').css('display','block');
$('#group').css('display','none');
$('#main-list-title').css('display','block');
$('#list-title').css('display','none');
groupRow = function(e){
	var slug = '{{ $slug }}';
	var url = "{{ route('volunteer_table',$slug) }}";
	var volunteer_groups = new Object();
    volunteer_groups.event_name = $(e).data('event_name');
    volunteer_groups.volunteer_type = $(e).data('volunteer_type');
    volunteer_groups.event_id = $(e).data('event_id');
    var x = $(e).data('id');
    var start_date = $('#start_date_timezone_'+x).text();
    var end_date = $('#end_date_timezone_'+x).text();
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,slug:slug,event_id:volunteer_groups.event_id,start_date:start_date,end_date:end_date}).done(function(data){
    	$('#table-content').empty().html(data);
    });
    timeoutLoader();
}
var limit = '{{ count($events)}}';
for (var x = 0; x < limit; x++) {
    var vg_head = '#volunteer-group-head-'+x;
    var vg_row  = '#volunteer-group-row-'+x;
    var vg_row_label      = '#volunteer-group-'+x;
    $(vg_row+' td').hide();
    $(vg_row_label+' td').hide();
    $(vg_head).hide();
} 
var events = '{{ $events}}';

$('.volunteer-group').each(function (){ 
  var id = '#'+$(this).attr('id');
  $(id+' td').hide();
});
// events.forEach()
showVolunteerGroupsByType = function(e){
    var x = $("#"+ e.id).data('id');
    var vg_head = '#volunteer-group-head-'+x;
    var vg_row = '#volunteer-group-row-'+x;
    var name = $(e).data('event_name');
    name = name.replace(/\s+/g, '-').toLowerCase();
    var id   = $(e).data('event_id');
    $(vg_row+' td').toggle(400);
    console.log(name+'_'+id);
    $(vg_head).toggle(400);
    //$('.'+ name+'_'+id).hide();
    $('.'+ name+'_'+id+' td').hide();
    $('td').removeClass('open_row');
    $('tr').removeClass('open_row');
    // $(vg_row+' td').attr();
    $(vg_row+' td i').attr('class','fa fa-caret-right');
}

showVolunteerGroups = function(e){
    var type = $(e).data('volunteer_type');
    var volunteer_group = $(e).data('volunteer_group_id');
    //var volunteer_id = $(e).data('volunteer_id');

    type = type.replace(/\s+/g, '-').toLowerCase();
    var event_id = $(e).data('event_id');
    var vg   = '#volunteer-group-'+type+'-'+event_id;
    var id = '#'+($(e).attr('id'));
    var loop_id = $(e).data('id');
    var volunteer_id = $(vg).data('volunteer_id');
    var count =  $(vg).data('count');
    var attribute = '_'+event_id+'_'+type;
    // $('#volunteer-group_'+loop_id+attribute).show();
    $('#volunteer-group_'+loop_id+attribute+' td').toggle(400);
    $('#click_attribute').text('volunteer-group_'+loop_id+attribute);
    $(vg+' td').toggle(400);
    var i = $(id+' td i#arrow-'+volunteer_group).attr('class');
    toggleFunction(i,'fa fa-caret-right','fa-caret-down','fa-caret-right',id+' td i#arrow-'+volunteer_group);

    $(vg+' td').addClass('open_row');
    $('#volunteer-group_'+loop_id+attribute).addClass('open_row');
    //$('.open_row').show();
    var vg_start_date_var   = '#vg_start_date_';
    var vg_end_date_var     = '#vg_end_date_';

    convertion(vg_start_date_var, count, attribute);
    convertion(vg_end_date_var, count, attribute);
}
toggleFunction = function(i,current,class1,class2,attr){
    if(i == current){
        $(attr).addClass(class1);
        $(attr).removeClass(class2);
    }else{
        $(attr).addClass(class2);
        $(attr).removeClass(class1);
    }
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
    var month = [];
    var day   = [];
    var year  = [];
    var date = '';
    convertion = function(date, limit, attribute){

        for (var i = 0; i <= limit; i++) {
            if(attribute == ''){
                var pick_date = $(date+ i);
            }else{
                var pick_date = $(date+ i+attribute);
            }
            console.log(pick_date.data('date')+ 'not modal------------');

            created_date[i] = moment.utc(pick_date.data('date'));
            offset = new Date().getTimezoneOffset();
            converted_created_date[i] = new Date(created_date[i].utcOffset(offset * -1));
            month[i] = converted_created_date[i].getMonth()+1;
            day[i]   = converted_created_date[i].getDate();
            year[i]  = converted_created_date[i].getFullYear();
    

            if(pick_date.data('date') != '0000-00-00 00:00:00'){
                $(date + 'timezone_'+ i+attribute ).text(month[i] + '/' + day[i] + '/' + year[i]);
            }else{
                $(date + 'timezone_'+ i+attribute ).text('-------');
            }

        }
    }
var limit = '{{ count($events)}}';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(start_date_var, limit,'');
convertion(end_date_var, limit,'');
</script>