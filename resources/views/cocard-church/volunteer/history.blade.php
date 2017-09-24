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
<div class="table-responsive">
<table class="table table-striped" id="tabledata">
	<thead class="theader">
		<th>Event</th>
		<th>Volunteer Role</th>
		<th>Start Date</th>
		<th>End Date</th>
	</thead>
	<tbody>
		<?php
            $x = 0;
        ?>
		@foreach($volunteers as $volunteer)
		<?php
            $x++;
        ?>		
        @if(isset($volunteer->volunteer_group))
			<tr>
				<td>{{ $volunteer->volunteer_group->event->name }}</td>
				<td>{{ $volunteer->volunteer_group->type }}</td>
				<span class="dataTz" id="start_date_{{ $x }}" data-date="{{$volunteer->volunteer_group->event->start_date}}" style="display:none"> &nbsp; </span>
                <td id="start_date_timezone_{{ $x }}"></td>
				<span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $volunteer->volunteer_group->event->end_date }}" style="display:none"> &nbsp; </span>
                <td id="end_date_timezone_{{ $x }}"></td>
			</tr>
        @endif
		@endforeach
	</tbody>
</table>
</div>
@section('script')
<script type="text/javascript">
// var volunteers_limit = {{ count($volunteers)}};
//     for (var i = 1; i <= volunteers_limit; i++) {
//         var start_date = moment.utc($('#start_date_'+ i ).data('date'));
//         var end_date = moment.utc($('#end_date_'+ i ).data('date'));
//         var created_date = moment.utc($('#created_date_'+ i ).data('date'));
//         var offset = new Date().getTimezoneOffset();
//         var converted_start_date = new Date(start_date.utcOffset(offset * -1));
//         var converted_end_date = new Date(end_date.utcOffset(offset * -1));
//         var converted_created_date = new Date(created_date.utcOffset(offset * -1));

//         var month = converted_start_date.getMonth()+1;
//         var day   = converted_start_date.getDate();
//         var year  = converted_start_date.getFullYear();
//         var hour  = converted_start_date.getHours();
//         var minutes = converted_start_date.getMinutes();
//         var A = '';
//         if(hour == 0){
//           var result = '12'; 
//         }
//         else{
//           var result  = hour;  
//         }
//         if(hour > 12){
//             A = 'PM';
//             hour = (hour - 12);

//             if(hour == 12){
//                 hour = '00';
//                 A = 'AM';
//             }
//         }
//         else if(hour < 12){
//             A = 'AM';   
//         }else if(hour == 12){
//             A = 'PM';
//         }else if(hour == 0){
//             hour = '00';
//         }
//         if(minutes < 10){
//             minutes = "0" + minutes; 
//         }
//         $('#start_date_timezone_'+ i ).text(month + '/' + day + '/' + year + ' ' + hour + ':' + minutes +' ' + A);
//         $('#start_date_'+ i ).hide();


//         var month2 = converted_end_date.getMonth()+1;
//         var day2   = converted_end_date.getDate();
//         var year2  = converted_end_date.getFullYear();
//         var hour2  = converted_end_date.getHours();
//         var minutes2 = converted_end_date.getMinutes();
//         var A2 = '';
//         if(hour2 == 0){
//           var result2 = '12'; 
//         }
//         else{
//           var result2  = hour2;  
//         }
//         if(hour2 > 12){
//             A2 = 'PM';
//             hour2 = (hour2 - 12);

//             if(hour2 == 12){
//                 hour2 = '00';
//                 A2 = 'AM';
//             }
//         }
//         else if(hour2 < 12){
//             A2 = 'AM';   
//         }else if(hour2 == 12){
//             A2 = 'PM';
//         }else if(hour2 == 0){
//             hour2 = '00';
//         }
//         if(minutes2 < 10){
//             minutes2 = "0" + minutes2; 
//         }
//         $('#end_date_timezone_'+ i ).text(month2 + '/' + day2 + '/' + year2 + ' ' + hour2 + ':' + minutes2 +' ' + A2);
//         $('#end_date_'+ i ).hide();
// }
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
        $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
        $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
        $(date + i ).hide();

        }
    }
$("#export_events").click(function(){
     $("#submit_event").trigger('click');
    return false;
    });
var volunteers_limit = {{ count($volunteers)}};
var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(created_date_var, volunteers_limit);
convertion(start_date_var, volunteers_limit);
convertion(end_date_var, volunteers_limit);
$('.dataTz').css('display', 'none');
</script>
@endsection
