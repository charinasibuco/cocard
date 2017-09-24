<style type="text/css">
img {
    width: 100px;
}
.done{
   opacity: 0.5 !important;
}
</style>
<div class="row">
{{--     <form method="GET" action="{{ route('church_volunteer',$slug)}}"> --}}
      {{--   <div class="col-md-8">
            <div class="form-group">
                Filter Groups per Event:  <select class="form-control" name="search_volunteer_group" id="search_volunteer_group">
                    <option value="All"  @if($search_volunteer_group == 'All') selected @endif>All Events</option>
                    @foreach($events as $row)
                    <option {{ $row->status }} value="{{ $row->id }}" title="{{ $row->name }}" @if($search_volunteer_group == $row->id) selected @endif>{{$row->name}}</option>
                    @endforeach

                </select>
            </div>
        </div> --}}
{{--         <div class="col-md-4">
            <button style="margin-top: 20px;" type="submit" class="btn btn-default">Go</button>
        </div> --}}
{{--     </form> --}}
</div>
<div class="table-responsive">
    <table class="table table-striped" id="tabledata">
        <thead class="theader">
{{--             <th width="20%">Type</th>
            <th width="20%">Event Name</th> --}}
            <th width="15%">Start Date</th>
            <th width="15%">End Date</th>
            <th width="5%">Volunteer Needed</th>
            <th width="5%">Pending</th>
            <th width="20%">Action</th>
        </thead>
        <tbody>
            @if(($volunteer_groups->count() == 0))
            <tr>
                <td>No records to show</td>
            </tr>
            @else
            <?php
                $x = 0;
            ?>
            @foreach($volunteer_groups as $row)
            <tr id="row_{{ $row->id }}" class="{{ $row->status_volunteers_needed}}">
              {{--   <td>{{$row->type}}</td>
                <td>{{$row->Event->name}}</td> --}}
                <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $row->start_date }}" style="display:none"> &nbsp; </span>
                <td data-id="data_start_{{ $row->id }}" id="start_date_timezone_{{ $x }}"></td>
              {{--   <input id="start_date_reminder_{{ $x }}" type="text" value=""> --}}
                <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $row->end_date }}" style="display:none"> &nbsp; </span>
                <td data-id="data_end_{{ $row->id }}" id="end_date_timezone_{{ $x }}"></td>
               {{--  <input id="end_date_reminder_{{ $x }}" type="text" value=""> --}}
                <td>{{ $row->volunteers_approved }}/{{$row->volunteers_needed}}</td>
                <td>{{ $row->pending}}</td>
                <td><a title="View all {{$row->type}} volunteers under {{$row->Event->name}} Event" class="btn btn-success  {{ (count($row->Volunteers) == 0)? 'disabled' : ''}}" href="{{ url('/organization/'.$slug.'/administrator/volunteer/filter-by-event/'.$row->id.'/view-volunteers-by-event',$row->event_id) }}">
                    <i class="fa fa-users" aria-hidden="true"></i>
                </a>
                <a title="Send Group Message to  {{$row->Event->name}} Event" class="btn btn-default {{ (count($row->approved_volunteers) == 0)? 'disabled' : ''}}" href="" data-toggle="modal" data-target="#volunteer_{{ $row->id }}" id="save_volunteer" name="save_value"  data-role="disabled">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </a>
                <a title="Send Group Reminder Message to  {{$row->Event->name}} Event" class="btn btn-warning {{ (count($row->approved_volunteers) == 0)? 'disabled' : ''}}" href="" data-toggle="modal" data-target="#send_reminder_message_{{ $row->id }}" >
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                </a>
            </td>
        </tr>
        <!-- Modal for Send Message  -->
        <div class="modal fade" id="volunteer_{{ $row->id }}" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    {{-- <h4 class="modal-title">Send Message to {{ (isset($volunteer_groups ? $volunteer_groups->first()->type : ''))}} Group< /h4>--}}
                        <h4 class="modal-title">Send Message to {{ $row->type}} Group </h4>

                    </div>
                    <div class="modal-body">
                            <div id="sent_{{ $row->id}}" style="display:none" align="center" style="text-align:center">Message sent</div>
                            <div id="form_{{ $row->id }}">
                                <label>To:</label>
                                @foreach($row->approved_volunteers as $volunteer)
                                {{$volunteer->email}},
                                @endforeach
                                <br>
                                <label>Subject:</label>
                                <input class="form-control not_empty" id="subject_{{$row->id}}"value="" name="subject" required>
                                <label>Message:</label>
                                <textarea class="form-control not_empty" id="message_{{$row->id}}" value="" name="message" required></textarea>
                            </div>
                            <div class='loading' align="center" style="display:none"></div>
                    </div>
                    <div class="modal-footer">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-default send" data-reminder="false" data-id="{{$row->id}}" data-subject="" data-message="" id="group_{{ $row->id }}" data-button="group_message" onclick="sendMessage(this)">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div> 
                </div>
            </div>
        </div>
        <!-- Modal for Send Reminder Message  -->
        <div class="modal fade" id="send_reminder_message_{{ $row->id }}" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Send Reminder Message For {{ $row->type }} Group</h4>
                    </div>
                    <div class="modal-body">
                        <div id="sent_reminder_{{ $row->id}}" style="display:none" align="center" style="text-align:center">Success! Message will be send based on your Reminder date!</div>
                        <div id="form_reminder_{{ $row->id}}">
                            <label>Volunteer Group Name:</label>
                            <input type="hidden" name="event_date" id="event_date_{{ $row->id }}" value="{{ $row->Event->start_date }}">
                           {{--  <input type="hidden" name="reminder_date" id="reminder_date_{{ $row->id }}" value="{{ $row->Event->reminder_date }}"> --}}
                            <input type="hidden" name="event_id" id="event_id_{{ $row->id }}" value="{{ $row->Event->id }}">
                            <input type="hidden" name="volunteer_group_id" id="volunteer_group_id_{{ $row->id }}" value="{{ $row->id }}">
                            <input class="form-control" id="type_{{ $row->id }}" name="volunteer_group_name" value="{{$row->type}}" disabled>
                            </br>
                            <label>To:</label>
                            @foreach($row->approved_volunteers as $volunteer)
                            {{ $volunteer->email}},
                            <input type="hidden" value="{{ $volunteer->email}}" name="email[]" id="email_reminder" required>
                            @endforeach
                            </br>
                            <label>Subject:</label>
                            <input class="form-control not_empty" value="" name="subject" id="reminder_subject_{{$row->id}}" required>
                            <label>Message:</label>
                            <textarea class="form-control not_empty" value="" name="message" id="reminder_message_{{$row->id}}" required></textarea>
                            <label>When to send Reminder?</label>
                            <div class='input-group datepicker'>
                                <input type="text" value="{{ $row->Event->reminder_date }}" placeholder="Reminder Date" class="form-control not_empty" name="reminder_date" id="reminder_date_{{ $row->id}}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class='loading' align="center" style="display:none"></div>  
                    </div>
                    <div class="modal-footer">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-default" data-button="reminder" data-reminder="true" data-id="{{$row->id}}" id="reminder_{{ $row->id }}" onclick="sendMessageReminderMessage(this)">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php
    $x++;
?>
@endforeach
</tbody>
</table>
</div>
<div class="modal fade" id="sent" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="margin-top:20%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                {{--  <h4 class="modal-title">Send Email</h4> --}}
            </div>
            <div class="modal-body">
                {{ Session::get('message') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="custom-pagination"></div>
@endif
{{-- <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script> --}}
<script type="text/javascript">
$('#back-main-list').css('display','block');
$('.datepicker').datetimepicker({
    format: 'M/D/YYYY',
});
var change_status_url = "{{ route('change_volunteer_status')  }}";
var render_page = '{!! $volunteer_groups->render() !!}';
$('#custom-pagination').html(render_page);

$('#main-list-title').css('display','none');
$('#list-title').css('display','block');
$('#list-title h6').text('Event Name: {{ $volunteer_groups->first()->Event->name }}');
$('#list-title cite#start_date').text('Start Date: {{ $start_date}}');
$('#list-title cite#end_date').text('End Date: {{ $end_date}}');
$('#list-title h4').text('{{$volunteer_groups->first()->type}}'+' Schedule List');

$('.pagination a').click(function(e){
    e.preventDefault();
    var slug = '{{ $slug }}';
    var url = "{{ route('church_volunteer_list',$slug) }}";
    var volunteer_groups = new Object();
    volunteer_groups.event_name = '{{ $volunteer_groups->first()->event->name}}';
    volunteer_groups.volunteer_type ='{{ $volunteer_groups->first()->type }}';
    var event_id = '{{ $volunteer_groups->first()->event->id}}';
    var page = $(this).text();
    var start_date = '{{ $start_date }}';
    var end_date = '{{ $end_date }}';
     //alert(url+'-----'+volunteer_groups.volunteer_type+'-----'+slug+'-----'+page);
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,slug:slug,page:page,event_id:event_id,start_date:start_date,end_date:end_date}).done(function(data){
        $('#table-content').empty().html(data);
    });
});

$(".volunteer-status").click(function(){
    var switch_ = $(this);
    var value = switch_.closest("td").find(".status-container").html();
    $.post(change_status_url,{id:switch_.data("volunteer_id"),status:value,_token:"rj29r8498rit"}).done(function(data){
        switch_.closest("td").find(".status-container").html(data);
    });
});
var url = "{{ route('church_volunteer_list',$slug) }}";
var search_volunteer_group = "{{ $search_volunteer_group }}";
// var search_volunteer_group_value = $('#search_volunteer_group').val();
$('#search_volunteer_group').on('change', function(){
    var search_volunteer_group_value = $('#search_volunteer_group').val();
    $.get(url,{search_volunteer_group:search_volunteer_group_value}).done(function(data){
        $('#volunteer-list').empty().html(data);
    });
});
var removeLastChar = function(value, char){
    var lastChar = value.slice(-1);
    if(lastChar == char) {
        value = value.slice(0, -1);
    }
    return value;
}
$(function(){
    $('#save_volunteer').click(function(){
        var val = [];
        $('input[name="row_selected_volunteer"]:checked').each(function(i){
            val[i] = $(this).val();
        });
        $('#multiple_volunteers').val(val);
        var nums = $('#multiple_email').val();
        var result = removeLastChar(nums, ',');
        $('#multiple_email').val(result);
    });
});
$(".btn").on("click", function (event) {
    if ($(this).hasClass("disabled")) {
        event.stopPropagation()
    } else {
        $('#applyRemoveDialog').modal("show");
    }
});

// this section is for date time convertion from UTC date time(fom database) 
// to user browser timezone shown to this blade
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
    for (var i = 0; i <= limit; i++) {
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
            $(date + 'reminder_'+ i ).val(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
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
var limit = {{ count($volunteer_groups)}};
var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(created_date_var, limit);
convertion(start_date_var, limit);
convertion(end_date_var, limit);

 $('.subject').on('input',function(){
        var dInput = this.value;
        $('.subject').attr('value',dInput);
 });
 $('.message').on('input',function(){
        var dInput = this.value;
        $('.message').attr('value',dInput);
 });

 $('.not_empty').on('input',function(){
        $('.not_empty').css('border','solid rgb(204, 204, 204) 1px');
 });

 $('.datepicker').on('dp.change', function(){
     $('.reminder_date').css('border','solid rgb(204, 204, 204) 1px');
 });

sendMessage = function(e){
   //alert($("#"+ e.id).data('reminder'));
   var details = new Object();
   // alert(e.data('id'));
   var button =$("#"+ e.id).data('button');
   var dataId = $("#"+ e.id).data('id');
   // alert(dataId);
   var message = $('#message_'+dataId).val();
   var subject = $('#subject_'+dataId).val();
    
   if(message != '' && subject != ''){
        var send_url = "{{ url('/organization/'.$slug.'/administrator/volunteer/send-email-to-volunteer-group/')}}" +'/'+ dataId; 
        $.post(send_url,{message:message,subject:subject}).done(function(){
         // alert('message sent');
        $('#sent_'+dataId).css('display','block');
        $('#form_'+dataId).css('display','none');
        $('#group_'+dataId).css('display','none');
        $('.loading').css('display','none');
       });
        timeoutLoader(dataId,button);
   }else{
        if($('#message_'+dataId).val() == ''){
            $('#message_'+dataId).css('border','solid red 1px');
        }
        if($('#subject_'+dataId).val() == ''){
            $('#subject_'+dataId).css('border','solid red 1px');
        }
   }
   
}
sendMessageReminderMessage = function(e){
    var button =$("#"+ e.id).data('button');
    var dataId = $("#"+ e.id).data('id');
    $('tr#row_'+dataId).each(function() {
        var start_date_timezone = $(this).find('[data-id=data_start_' + dataId + ']').text(); 
        var end_date_timezone = $(this).find('[data-id=data_end_' + dataId + ']').text(); 
        //console.log(date_timezone);  
       // alert(start_date_timezone +' '+ end_date_timezone);
        // var start_date = $('#start_date_reminder_'+dataId).val(); 
        // var end_date = $('#end_date_reminder_'+dataId).val();
        var event_id = $('#event_id_'+dataId).val();
        var type= $('#type_'+dataId).val();
        var volunteer_group_id= $('#volunteer_group_id_'+dataId).val();
        var reminder_subject = $('#reminder_subject_'+dataId).val();
        var reminder_message = $('#reminder_message_'+dataId).val();
        var reminder_date    = $('#reminder_date_'+dataId).val();
        var reminder_url = "{{ url('/organization/'.$slug.'/administrator/volunteer/send-reminder-message-to-volunteer-group/')}}" +'/'+dataId;
        if(reminder_subject == ''){
            $('#reminder_subject_'+dataId).css('border','solid red 1px');
        } 
        if(reminder_message == ''){
            $('#reminder_message_'+dataId).css('border','solid red 1px');
        }
        if(reminder_date == ''){
            $('#reminder_date_'+dataId).css('border','solid red 1px');
        }
        
        if(reminder_subject != '' && reminder_message != '' && reminder_date != ''){
            $.post(reminder_url,{reminder_message:reminder_message,reminder_subject:reminder_subject,start_date:start_date_timezone,end_date:end_date_timezone,dataId:dataId,event_id:event_id,type:type,volunteer_group_id:volunteer_group_id,reminder_date:reminder_date}).done(function(){
                $('#sent_reminder_'+dataId).css('display','block');
                $('#form_reminder_'+dataId).css('display','none');
                $('#reminder_'+dataId).css('display','none');
                $('.loading').css('display','none');
            });
            timeoutLoader(dataId,button);
        }
     });
}

timeoutLoader = function(dataId,button){
    var timeout;
    function startLoad() {
        if(button == 'reminder'){
            $('#form_reminder_'+dataId).css('display','none'); 
        }else if(button == 'group_message'){
            $('#form_'+dataId).css('display','none');
        }
        
        $('.loading').css('display','block');
        $('.loading').html('<img src="{{asset('images/ajax-loader-small.gif')}}"/>');
    }
    function loaded() {
        $('.loading').html('Fail sending. Please try Again.');
    }
    clearTimeout(timeout);
    timeout = setTimeout(loaded, 20500);
    $('.send').click(startLoad);
    startLoad();
}
$('.dataTz').css('display', 'none')
</script>
@if(!empty(Session::get('message')))
<script>
$(function() {
    $('#sent').modal('show');
});
</script>
@endif
