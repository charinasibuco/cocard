<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">{{ $event->name }}</h3>
    {{--{{$event->check_participants_per_occurence}}--}}
   
</div>
<div class="modal-body">
    <div class="delete-modal-container" data-id="{{$event->id}}">
        @if($event->recurring == 0)
        <div class="modal-delete-event">
            <div class="modal-header">
                <h5>Event: Delete {{ $event->name }}.</h5>
            </div>
            <form method="get" action="{{  url('/organization/'.$slug.'/administrator/events/delete') }}">
                <div class="center" style="margin-left:5%;">
                    <br>
                    <input type="hidden" name="id" value="{{$event->id}}">
                    <input type="radio" name="delete_event" value= "3" checked="checked"> Delete Event<br>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this event?</p>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{--<a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"href="{{ url('/organization/'.$slug.'/administrator/events/delete/'.$event->id) }}">
                                    YES
                                </a>--}}
                                <button type="submit" class="btn btn-red btn-full" >YES</button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$event->id}}">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @else        
            <div class="modal-delete-event">
                <div class="modal-header">
                    <h5>Event: Delete {{ $event->name }}.</h5>
                </div>
                <form method="get" action="{{  url('/organization/'.$slug.'/administrator/events/delete') }}">
                    <div class="center" style="margin-left:5%;">
                        <br>
                        <input type="hidden" name="occurrence_modal" id="occurrence_modal" value="">
                        <input type="hidden" value=""  name="past_date" class="past_date_delete">
                        <input type="hidden" name="id" value="{{$event->id}}">
                        <input type="radio" name="delete_event" value= "1" checked="checked">This Event only<br>
                        <input type="radio" name="delete_event" value= "2"> This Event and succeeding occurrences <br>
                        <input type="radio" name="delete_event" value= "3"> All<br>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this event?</p>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-6">
                                {{--<a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"href="{{ url('/organization/'.$slug.'/administrator/events/delete/'.$event->id) }}">
                                        YES
                                    </a>--}}
                                    <button type="submit" class="btn btn-red btn-full" >YES</button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$event->id}}">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
    <input type="hidden" value="" class="past_date">

    @if(session('success'))
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session("success") }}
    </div>
    @endif
    @if(session('failed'))
    <div class="alert alert-danger alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ session("failed") }}
    </div>
    @endif
    <h5>{{ $event->description }}</h5>
    <div class="row">
        <div class="col-md-6">
            <input type="hidden" class="occurrence" value="">
                Capacity:
         </div>
         <div class="col-md-6">
            <input type="hidden" class="occurrence" value="">
                  <p id="event_total_participants" style="float:left;"></p>/{{ $event->capacity }}
         </div>
     </div>
     <div class="row">
        <div class="col-md-6">
                Volunteers Needed:
         </div>
         <div class="col-md-6" id="no_volunteer">
         </div>
     </div>
     <div id="volunteer-group-table"></div>
     <div class="row">
        <div class="col-md-6">
                Fee:
         </div>
         <div class="col-md-6">
            ${{ $event->fee }}
         </div>
     </div>
     <div class="row">
        <div class="col-md-6">
                Start Date:
         </div>
         <div class="col-md-6">
                <p style ="margin-bottom:0" id="start"></p>  
         </div>
     </div>
     <div class="row">
        <div class="col-md-6">
                End Date:
         </div>
         <div class="col-md-6">
           <p style ="margin-bottom:0" id="end"></p>
         </div>
     </div>
     <div id="span">
        <span class="dataTz" id="start_date_id" data-date="{{ $event->start_date }}"> &nbsp; </span>
        <span class="dataTz" id="end_date_id" data-date="{{ $event->end_date }}"> &nbsp; </span>
    </div>
    <input type="hidden" id="date_pass_current_start_date" value="">
    <input type="hidden" value="" class="occur_count">
</div>
<div class="modal-footer">
    <input class="form-control" type="hidden" name="slug" value="tastradesoft" id="modal-slug">   
    @can('edit_event')     
    <a href="{{ url('/organization/'.$slug.'/administrator/events/duplicate/'.$event->id.'/') }}" class="btn btn-darkblue js-edit-event-hash" >
        <i class="fa fa-clone" aria-hidden="true"></i>&nbsp;Duplicate
    </a>
    @endcan
    @can('edit_event')
    <a href="{{ url('/organization/'.$slug.'/administrator/events/edit/'.$event->id.'/') }}" class="btn btn-darkblue js-edit-event-hash_modify">
        <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit
    </a>
    @endcan
    @can('delete_event')
    <a class="btn btn-red delete_modal" data-id="{{$event->id}}" style="cursor:pointer;">
        <i class="fa fa-trash-o" aria-hidden="true"></i>&nbsp;Delete
    </a>
    @endcan
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    @if(count($event->Participant) > 0)
    <a href="" data-toggle="modal" data-target="#event_id{{ $event->id }}" id="{{ $event->id }}">
        <span class="fa-stack fa-lg icon-edit">
            <i class="fa fa-square fa-stack-2x"></i>
            <i class="fa fa-envelope-o fa-stack-1x"></i>
        </span>
    </a>
    @endif
</div>
    <div class="form-send-message">
        <div class="modal-header">

            <h4 class="modal-title">Send Message to {{ $event->name }} Event Participants</h4>
        </div>
        <div class="modal-body">
            <form action="{{ url('/organization/'.$slug.'/administrator/event/send-email-to-participant')}} " method="post" >
                <label>To:</label>
                @foreach($event->Participant as $participant)
                {{$participant->email}},
                <input type="hidden" name="email[]" value="{{ $participant->email}}">
                @endforeach
                <br>
                <label>Subject:</label>
                <input class="form-control" value="" name="subject" required>
                <label>Message:</label>
                <textarea class="form-control" value="" name="message" required></textarea>
                {!! csrf_field() !!}
            </br>
            <button type="submit" class="btn btn-default">Send</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

$(document).ready(function() {
     //load again the page(modal) to fetch the value passed by from calendar js
    $.post('{{ route("event_modal",$slug)}}', function(){
        var occur_count2 = $(".occur_count2").val();
        //this is for participants per eventoccurence
        var get_occurence = "{{ route('get_occurence') }}";
        var token = "{{ csrf_token() }}";
        var event_id = "{{ $event->id }}";
        var occur_count = $(".occur_count").val();
        $('#event_total_participants').text('0'); 
        $('#event_remaining').text('0'); 
        $.post(get_occurence,{_token: token,occurences: occur_count, event_id: event_id}, function(data){
            console.log(occur_count);
            $('#event_total_participants').text(data);//total participants for each occurence in the event
            //assign value to Remaining events field
            var total_capacity  = "{{$event->capacity}}";
            var remaining_slots = total_capacity - data;
            $('#event_remaining').text(remaining_slots);
            $('.remaining_slots').val(parseInt(remaining_slots));
            $('.total_slots').val(parseInt(total_capacity));
        });   
    });   
 
    $('.form-send-message').hide();
    $('#{{ $event->id }}').click(function(){
        $('.form-send-message').toggle('fast');
    });
    $(".delete_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','none');
    });
});

var start_date_id = moment.utc($('#start_date_id').data('date'));
var offset = new Date().getTimezoneOffset();
var converted_start_date = new Date(start_date_id.utcOffset(offset * -1));
var start_date_month = converted_start_date.getMonth()+1;
var start_date_day = converted_start_date.getDate();
var start_date_year = converted_start_date.getFullYear();
var start_date_hour = converted_start_date.getHours();
var start_date_minutes = converted_start_date.getMinutes();
var start_date_A = '';



if(start_date_hour == 0){
  var start_date_result = '12'; 
}
else{
  var start_date_result  = start_date_hour;  
}
if(start_date_hour > 12){
    start_date_A = 'PM';
    start_date_hour = (start_date_hour - 12);

    if(start_date_hour == 12){
        start_date_hour = '00';
        start_date_A = 'AM';
    }
}
else if(start_date_hour < 12){
    start_date_A = 'AM';   
}else if(start_date_hour == 12){
    start_date_A = 'PM';
}else if(start_date_hour == 0){
    start_date_hour = '00';
}
if(start_date_minutes < 10){
    start_date_minutes = "0" + start_date_minutes; 
}
if(start_date_hour == 0){
    start_date_hour = '12';
}
$('p#start').text(start_date_month+'/'+start_date_day+'/'+start_date_year+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A);

var end_date_id = moment.utc($('#end_date_id').data('date'));
var offset = new Date().getTimezoneOffset();
var converted_end_date = new Date(end_date_id.utcOffset(offset * -1));
var end_date_month = converted_end_date.getMonth()+1;
var end_date_day = converted_end_date.getDate();
var end_date_year = converted_end_date.getFullYear();
var end_date_hour = converted_end_date.getHours();
var end_date_minutes = converted_end_date.getMinutes();
var end_date_A = '';
if(end_date_hour == 0){
  var end_date_result = '12'; 
}
else{
  var end_date_result  = end_date_hour;  
}
if(end_date_hour > 12){
    end_date_A = 'PM';
    end_date_hour = (end_date_hour - 12);

    if(end_date_hour == 12){
        end_date_hour = '00';
        end_date_A = 'AM';
    }
}
else if(end_date_hour < 12){
// start_date_result = ((start_date_hour < 10) ? "0" + start_date_hour : start_date_hour);
    end_date_A = 'AM';   
}else if(end_date_hour == 12){
    end_date_A = 'PM';
}else if(end_date_hour == 0){
    end_date_hour = '00';
}
if(end_date_minutes < 10){
    end_date_minutes = "0" + end_date_minutes; 
}
if(end_date_hour == 0){
    end_date_hour = '12';
}
$('p#end').text(end_date_month+'/'+end_date_day+'/'+end_date_year+' ' + end_date_hour + ':' + end_date_minutes + ' ' + end_date_A);
$('#span').hide();
endDate = function(){
    var a = new Date($('#end_date').val());
    var b = moment.utc(a).format();
    $('#end_date_timezone').val(b);

}
 var ed = $('#end').text();
 var _edate = new Date($('#end').text());
 var _sdate = new Date($('#start').text());
 //start date
 var _sdate_day     = _sdate.getDate();
 var _sdate_month   = _sdate.getMonth()+1;
 var _sdate_year    = _sdate.getFullYear();
 var _sdate_        = (_sdate_month+''+_sdate_day+''+_sdate_year);
//end date
 var _edate_day     = _edate.getDate();
 var _edate_month   = _edate.getMonth()+1;
 var _edate_year    = _edate.getFullYear();
 var _edate_        = (_edate_month+''+_edate_day+''+_edate_year);
//hours and minutes
 var _edate_hour    = _edate.getHours();
 var _edate_minute  = _edate.getMinutes();

 if(_sdate_ == _edate_){
    $('#end__').text(_edate_hour+':'+ _edate_minute+' '+ed.slice(-2));
 }
reminderDate = function(){
    var a = new Date($('#reminder_date').val());
    var b = moment.utc(a).format();
    $('#reminder_date_timezone').val(b);
}
$.post('{{ route("event_modal",$slug)}}', function(){
    //$('.occurrence').val($('.occurrence').val());
    // alert($('.occurrence').val());
    if($('.occurrence').val() == 0){
        var occurrence = $('.occurrence').val();
    }else if($('.occurrence').val() > 0){
        var occurrence = ($('.occurrence').val())-1
    }
    $('#occurrence_modal').val(occurrence);
    var url = '{{ route("vg_per_occurrence") }}';
    var event_id = '{{ $event->id }}';
    $.post(url,{event_id:event_id, occurrence:occurrence}).done(function(data){
        $('#volunteer-group-table').empty().html(data);
        if(data ==''){
            $('#no_volunteer').append('<cite>No Volunteer Needed</cite>');
        }
    });
});
// var count_volunteer = '{{ count($event->volunteer_groups)}}';
//     var n = 0;
//     var o = 0;

//     var get_occurence = "{{ route('get_occurence') }}";
//     var token = "{{ csrf_token() }}";
//     var event_id = "{{ $event->id }}";
//     for(var x=0; x < count_volunteer; x++){
//         if(recurring == 2)
//         {
//             var no_of_month_diff = (Math.floor(total_current_days)) / 31;
//             var diff = Math.floor(no_of_month_diff);
//             var no_of_occurrence = (total_no_of_occurrence - diff) + 1;

//             $('#occurrence_modal_'+ x).val(no_of_occurrence);
//             $('#occurrence_modal').val(no_of_occurrence);    
//              var occur_count = $('.occur_count').val();
//              $.post(get_occurence,{_token: token,occurences: occur_count, event_id: event_id}, function(data){
//                 //console.log(data);
//                 //console.log(occur_count);
//                 $('#event_total_participants').text(data);
//              });
//         }
//         else if(recurring == 1)
//         {
//             var no_of_week_diff = (Math.floor(total_current_days)) / 7;
//             var diff = Math.floor(no_of_week_diff);
//             var no_of_occurrence = total_no_of_occurrence - diff; 

//             $('#occurrence_modal_'+ x).val(no_of_occurrence);
//             $('#occurrence_modal').val(no_of_occurrence); 
//             var occur_count = $('.occur_count').val();
//              $.post(get_occurence,{_token: token,occurences: occur_count, event_id: event_id}, function(data){
//                 //console.log(data);
//                // console.log(occur_count);
//                 $('#event_total_participants').text(data);
//              });
//         }
//         else if(recurring == 3)
//         {
//             var no_of_year_diff = (Math.floor(total_current_days)) / 365;
//             var diff = Math.floor(no_of_year_diff);
//             var no_of_occurrence = total_no_of_occurrence - diff;

//             $('#occurrence_modal_'+ x).val(no_of_occurrence);
//             $('#occurrence_modal').val(no_of_occurrence);
//              var occur_count = $('.occur_count').val();
//              $.post(get_occurence,{_token: token,occurences: occur_count, event_id: event_id}, function(data){
//                 //console.log(data);
//                 //console.log(occur_count);
//                 $('#event_total_participants').text(data);
//              });
//         }
//         else
//         {
//             $('#occurrence_modal_'+ x).val(0);
//             $('#occurrence_modal').val(0);
//         }

//         if($('#no_of_occurrence_'+ x).val() == $('#occurrence_modal_'+ x).val())
//         {
//             $('#volunteer_groups_'+ x).css('display','block');
//             $('#volunteers_needed_'+ x).css('display','block');

//             var n_o_c = parseInt($('#no_of_occurrence_'+ x).val());
//             var o_m  = parseInt($('#occurrence_modal_'+ x).val());

//             if(n_o_c == o_m)
//             {
//                 n++;
//             }
//         }
//         else
//         {
//             $('#volunteer_groups_'+ x).css('display','none');
//             $('#volunteers_needed_'+ x).css('display','none');

//             var n_o_c = parseInt($('#no_of_occurrence_'+ x).val());
//             var o_m  = parseInt($('#occurrence_modal_'+ x).val());
            
//             if(n_o_c == o_m)
//             {
//                 n++;
//             }
//         }
//         //console.log($('#no_of_occurrence_'+ x).val() +' '+ $('#occurrence_modal_'+ x).val());
//     }
</script>
