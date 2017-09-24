@if(count($events) > 0)
	<table class="table">
		<thead>
		<tr>
			<th>Event Name</th>
			<th>Volunteers Needed</th>
			<th>Start Date</th>
			<th>End Date</th>
		</tr>
		</thead>
		<tbody>
			
			<?php
                $x = 0;
            ?>
			@foreach($events as $event)
			<?php
                $x++;
            ?>
				<tr>
					<td><a href="javascript:void(0);" id="event-{{ $event->id }}" data-event_id="{{ $event->id }}" onclick="loadEvent(this)" class="event-button"  style="color:#000;">{{ $event->name }}</a></td>
					<td>
						<span style="@if($event->volunteers->count() >= $event->volunteer_number) color: red @endif" >{{ $event->volunteers->count() }} / {{ $event->volunteers_needed }}</span>
					</td>
					<span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $event->start_date}}" style="display:none"> &nbsp; </span>
	                <td id="start_date_timezone_{{ $x }}"></td>
	                <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $event->end_date}}" style="display:none"> &nbsp; </span>
	                <td id="end_date_timezone_{{ $x }}"></td>
					</tr>
			@endforeach
		</tbody>
	</table>
@else
	<h4>No events to display.</h4>
@endif
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

            if($(date+ i ).data('date') != '0000-00-00 00:00:00'){
                $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
                
            }else{
                $(date + 'timezone_'+ i ).text('-------');
            }
            $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
            $(date + i ).hide();

        }
    }
$("#export_events").click(function(){
     $("#submit_event").trigger('click');
    return false;
    });
var limit = '{{ count($events)}}';
var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(created_date_var, limit);
convertion(start_date_var, limit);
convertion(end_date_var, limit);
$('.dataTz').css('display', 'none')
</script>
