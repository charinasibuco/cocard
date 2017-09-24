/*<style type="text/css">
tr td{
    cursor: pointer;
}
</style>
<div class="table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
            <th width="20%">Start Date</th>
            <th width="20%">End Date</th>
{{--             <th width="20%">Volunteer Group Name</th> --}}
            <th width="10%" style="border-right:1px solid #CCC">Volunteers Needed</th>
          {{--   <th width="20%">Event Name</th>
            <th width="15%">Start Date</th>
            <th width="15%">End Date</th> --}}
        </thead>
        <tbody>
            <?php
            $x = 0;
            ?>

            @foreach($volunteer_groups as $volunteer_group)
            <?php
                $x++;
                $no_of_occurrence = $volunteer_group->no_of_occurrence;
            ?>

            <tr data-href="javascript:void(0);" id="event-{{ $volunteer_group->id }}" data-event_id="{{ $volunteer_group->event->id }}" onclick="loadEvent(this)" data-volunteer_id="{{ $volunteer_group->id }}"class="event-button"  style="color:#000;">
{{--                 <td>{{ $volunteer_group->id }}</td> --}}
                <span class="dataTz" id="vg_start_date_{{ $x }}" data-date="{{ $volunteer_group->start_date }}" style="display:none">&nbsp;</span>
                <span class="dataTz" id="vg_end_date_{{ $x }}" data-date="{{ $volunteer_group->end_date }}" style="display:none">&nbsp;</span>
                <td><span id="vg_start_date_timezone_{{ $x }}"></span></td>
                <td><span id="vg_end_date_timezone_{{ $x }}"></span></td>
      {{--           <td>{{ $volunteer_group->type}}</td> --}}
             {{--    {{ $volunteer_group->volunteers_approved }} --}}
                <td>{{ $volunteer_group->volunteers_approved}}/{{ $volunteer_group->volunteers_needed}}</td>
        {{--         <td>{{ $volunteer_group->event->name}}</td> --}}
    {{--             <td>{{ $volunteer_group->event->start_date}}</td> --}}
             {{--    <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $volunteer_group->event->start_date }}" data-recurring="{{ $volunteer_group->event->recurring }}" data-number_of_occurrence="{{$volunteer_group->no_of_occurrence}}" style="display:none"> &nbsp; </span> --}}
              {{--   <td id="start_date_timezone_{{ $x }}"></td> --}}
           {{--      <td>{{ $volunteer_group->event->end_date}}</td> --}}
         {{--        <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $volunteer_group->event->end_date }}" data-recurring="{{ $volunteer_group->event->recurring }}" data-number_of_occurrence="{{$volunteer_group->no_of_occurrence}}"  style="display:none"> &nbsp; </span>
                <td id="end_date_timezone_{{ $x }}"></td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="custom-pagination"></div>
@if(count($volunteer_groups) == 0)
    <div style="text-align:center; font-size:18px"><cite with="100%">No record to show</cite></div>
@endif
<script type="text/javascript">
// $('#back-main-list').css('display','block');
// $('#main-list-title').css('display','none');
// $('#list-title').css('display','block');
//$('#list-title h4').text('{{$volunteer_groups->first()->type}}'+' Schedule List');
//$('#list-title h6').text('Event Name: {{ $volunteer_groups->first()->Event->name }}');
// $('#list-title cite#start_date').text('Start Date: {{ $start_date}}');
// $('#list-title cite#end_date').text('End Date: {{ $end_date}}');

// $('#main').css('display','none');
// $('#group').css('display','block');
var render_page = '{!! $volunteer_groups->render() !!}';
// $('#pagination').html(render_page);
$('#custom-pagination').html(render_page);
$('.pagination a').click(function(e){
    e.preventDefault();
    var slug = '{{ $slug }}';
    var url = "{{ route('volunteer_table',$slug) }}";
    var volunteer_groups = new Object();
    volunteer_groups.event_name = '{{ $volunteer_groups->first()->event->name}}';
    volunteer_groups.volunteer_type ='{{ $volunteer_groups->first()->type }}';
    var event_id = '{{ $volunteer_groups->first()->event->id}}';
    var page = $(this).text();
    var start_date = '{{ $start_date }}';
    var end_date = '{{ $end_date }}';
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    // $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,slug:slug,page:page,event_id:event_id}).done(function(data){
    //     $('#table-content').empty().html(data);
    // });
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,slug:slug,page:page,event_id:event_id,start_date:start_date,end_date:end_date}).done(function(data){
        $('#table-content').empty().html(data);
    });
});
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
var number_of_days = [];
var convert_date = [];
var added_date = [];
var org_date = [];
var day =[];
var hour = [];
var months = [];
var year =[];
var minutes = [];
var a = [];
var all_date =[];
var date2= [];
var recurring_date =[];
var number_of_days_occurrence = [];
var check_month = [];
var recurring_month = [];
var result_month = [];
var answer = [];
var date = [];
var t = [];
var f = [];
var result_year = [];
var startDate=[];
var endDateMoment=[];
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
    $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
    if($(date+ i ).data('recurring') == 1){
         number_of_days_occurrence[i] = parseInt($(date+ i ).data('number_of_occurrence'));
         number_of_days[i] =  parseInt(7, 10);
         date2[i] =new Date($(date + 'timezone_'+ i ).text());

        startDate[i] = new Date(date2[i]);
        endDateMoment[i] = moment(startDate[i]);
        endDateMoment[i].add(number_of_days_occurrence[i], 'weeks');
        recurring_date[i] = new Date(endDateMoment[i]);

         hour[i]= recurring_date[i].getHours();
         minutes[i]= recurring_date[i].getMinutes();
         day[i]= recurring_date[i].getDate();
         months[i]= recurring_date[i].getMonth()+1;
         year[i]= recurring_date[i].getFullYear();
        a[i] ='';
        if(hour[i] > 12){
            a[i] = 'PM';
            hour[i] = (hour[i] - 12);

            if(hour[i] == 12){
                hour[i]= '00';
                a[i] = 'AM';
            }
        }
        else if(hour[i] < 12){
            a[i] = 'AM';
        }else if(hour[i] == 12){
            a[i] = 'PM';
        }else if(hour[i] == 0){
            hour[i] = '00';
        }
        if(minutes[i] < 10){
            minutes[i] = "0" + minutes[i];
        }
        if(hour[i]== 0){
            hour[i]= '12';
        }
        $(date + 'timezone_'+ i ).text(months[i] + '/' + day[i] + '/' + year[i] + ' ' + hour[i] + ':' + minutes[i] +' ' + a[i]);
    }else if($(date+ i ).data('recurring') == 2){
         date2[i] =new Date($(date + 'timezone_'+ i ).text());

         number_of_days_occurrence[i] = parseInt($(date+ i ).data('number_of_occurrence'));

         //recurring_date[i] = new Date($(date + 'timezone_'+ i ).text());
         // console.log(recurring_date[i] + ' ' + i + ' ' + $(date+ i ).data('number_of_occurrence') + ' ' + number_of_days[i]+' '+ date2[i]);
        startDate[i] = new Date(date2[i]);
        endDateMoment[i] = moment(startDate[i]);
        endDateMoment[i].add(number_of_days_occurrence[i], 'months');
        recurring_date[i] = new Date(endDateMoment[i]);
        //console.log(recurring_date[i]);

         hour[i]= recurring_date[i].getHours();
         minutes[i]= recurring_date[i].getMinutes();
         day[i]= recurring_date[i].getDate();
         months[i]= recurring_date[i].getMonth()+1;
         year[i]= recurring_date[i].getFullYear();
         a[i] ='';
         //console.log(result_month[i] +' '+ f[i]);
        if(hour[i] > 12){
            a[i] = 'PM';
            hour[i] = (hour[i] - 12);

            if(hour[i] == 12){
                hour[i]= '00';
                a[i] = 'AM';
            }
        }
        else if(hour[i] < 12){
            a[i] = 'AM';
        }else if(hour[i] == 12){
            a[i] = 'PM';
        }else if(hour[i] == 0){
            hour[i] = '00';
        }
        if(minutes[i] < 10){
            minutes[i] = "0" + minutes[i];
        }
        if(hour[i]== 0){
            hour[i]= '12';
        }
        // console.log(result_year[i]);
        $(date + 'timezone_'+ i ).text(months[i] + '/' + day[i] + '/' + year[i] + ' ' + hour[i] + ':' + minutes[i] +' ' + a[i]);
    }else if($(date+ i ).data('recurring') == 3){
         number_of_days_occurrence[i] = parseInt($(date+ i ).data('number_of_occurrence'));
         number_of_days[i] =  parseInt(365.25, 10);
         date2[i] =new Date($(date + 'timezone_'+ i ).text());
         //date2[i].setDate(date2[i].getDate() + number_of_days[i]);

         // recurring_date[i] = new Date(date2[i].setDate(date2[i].getDate() + (number_of_days[i] * number_of_days_occurrence[i])));
         //recurring_date[i] = new Date($(date + 'timezone_'+ i ).text());
        // console.log($(date+ i ).data('number_of_occurrence'));
        startDate[i] = new Date(date2[i]);
        endDateMoment[i] = moment(startDate[i]);
        endDateMoment[i].add(number_of_days_occurrence[i], 'years');
        recurring_date[i] = new Date(endDateMoment[i]);

         hour[i]= recurring_date[i].getHours();
         minutes[i]= recurring_date[i].getMinutes();
         day[i]= recurring_date[i].getDate();
         months[i]= recurring_date[i].getMonth()+1;
         year[i]= recurring_date[i].getFullYear();
        a[i] ='';
        if(hour[i] > 12){
            a[i] = 'PM';
            hour[i] = (hour[i] - 12);

            if(hour[i] == 12){
                hour[i]= '00';
                a[i] = 'AM';
            }
        }
        else if(hour[i] < 12){
            a[i] = 'AM';
        }else if(hour[i] == 12){
            a[i] = 'PM';
        }else if(hour[i] == 0){
            hour[i] = '00';
        }
        if(minutes[i] < 10){
            minutes[i] = "0" + minutes[i];
        }
        if(hour[i]== 0){
            hour[i]= '12';
        }
        $(date + 'timezone_'+ i ).text(months[i] + '/' + day[i] + '/' +year[i] + ' ' + hour[i] + ':' + minutes[i] +' ' + a[i]);
    }else{
       $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);

    }
    $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
    $(date + i ).hide();

    }
}

var volunteers_limit = {{ count($volunteer_groups)}};
// var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
var vg_start_date_var   = '#vg_start_date_';
var vg_end_date_var     = '#vg_end_date_';

// convertion(created_date_var, volunteers_limit);
convertion(start_date_var, volunteers_limit);
convertion(end_date_var, volunteers_limit);
convertion(vg_start_date_var, volunteers_limit);
convertion(vg_end_date_var, volunteers_limit);
$('.dataTz').css('display', 'none');
</script>
*/