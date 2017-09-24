<style type="text/css">
.hide_important{
    display: none !important;
}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">{{ $event->name }}</h3>
</div>
<div class="modal-body">
    <h4>{{ $event->description }}</h4>
    <div class="row">
        <div class="col-md-12">
            <form action="{{ url('/organization/'.$slug.'/event/add-to-cart') }}" method="post" class="event" id="event">
                <table class="table">
                    <tr>

                        <td>Capacity:</td>
                        <td> <p id="event_total_participants" style="float:left; font-family: arial, sans-serif;"></p>/{{ $event->capacity }}</td>
                    </tr>
                    <tr>
                        <td>Remaining Available:</td>
                        <td> <p id="event_remaining" style="float:left; font-family: arial, sans-serif;"></p></td>
                    </tr>
                    <tr>
                        <td>Fee:</td>
                        <td>${{ number_format($event->fee, 2, '.', '')}}</td>
                        <input value="{{ $event->description }}" name="description" type="hidden">
                        <input value="{{ number_format($event->fee, 2, '.', '') }}" name="fee" type="hidden">
                    </tr>
                    <tr>
                        <td>Start Date:</td> 
                        <td id="now"></td>
                        <td id="start"></td>
            <!--             <td id="start2"></td> -->
                        <input value="" id="start_date_timezone" name="start_date_timezone" type="hidden">
                    </tr>
                    <tr>
                        <td>End Date:</td>
                        <td id="end"></td>
                        <input value="" id="end_date_timezone" name="end_date_timezone" type="hidden">

                    </tr>
                    @if($event->recurring > 0)
                        @if($event->no_of_repetition > 0)
                            <tr>
                                <td>Event No. of Repetition:</td>
                                <td>{{ $event->no_of_repetition}}</td>
                                <input value="{{ $event->no_of_repetition}}" name="no_of_repetition" type="hidden">
                            </tr>
                        @else
                            <tr>
                                <td>Recurring End Date:</td>
                                <td>{{  Carbon\Carbon::parse($event->recurring_end_date)->format('n/j/Y')}}</td>
                                <input value="{{  Carbon\Carbon::parse($event->recurring_end_date)->format('n/j/Y')}}" name="recurring_end_date" type="hidden">
                            </tr>
                        @endif
                    <tr>
                        <td>Event Frequency:</td>
                        <td>{{ ($event->recurring == 3)? 'Yearly' : (($event->recurring == 2)? 'Monthly' : 'Weekly')}}</td>
                        <input value="{{ $event->recurring}}" name="recurring" type="hidden">

                    </tr>
                    @endif
                </table>
                {{--  <button type="button" class="btn btn-default join" onclick="show_qty_input()" style="margin-bottom:10px; float:right;">Join</button> --}}
                <div id="span">
                    <span class="dataTz" id="start_date_id" data-date="{{ $event->start_date }}"> &nbsp; </span>
                    <span class="dataTz" id="end_date_id" data-date="{{ $event->end_date }}"> &nbsp; </span>
                </div>
                <br>
                <div class="row" id="event_ended" style="display:none; text-align:center">
                    <cite>Event has passed</cite>
                </div>
                <div class="row" id="event_started" style="display:none; text-align:center">
                    <cite>Event has started </cite>
                </div>
                <div class="row" id="paid_event" style="display:none">
                    <div class="form-group">
                        <input class="form-control"type="text" name="name" value="{{ (Api::getUserByMiddleware()) ? Api::getUserByMiddleware()->first_name.' '. Api::getUserByMiddleware()->last_name  : ''}}" placeholder="Name" required>
                    </div>
                    <div class="form-group">
                        <input class="form-control"type="text" name="email" value="{{ (Api::getUserByMiddleware()) ? Api::getUserByMiddleware()->email  : ''}}" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" pattern= "[0-9]" oninput="this.value = this.value.replace(/(\..*)\./g, '');"class="form-control qty" id="qty{{ $event->id}}" name="qty" placeholder="Quantity" value="" required>
                        {{--<input type="number"class="form-control qty" id="qty{{ $event->id}}" name="qty" placeholder="Quantity" value="" required>--}}
                        <input type="hidden" class="occur_count" name="occur_count" value="">                     
                        <input type="hidden" name="slug" value="{{ $slug }}">
                        <input type="hidden" name="event_id" id="event_id" value="{{ $event->id }}">
                        <input type="hidden" class="fee" id="fee{{ $event->id}}" value="{{$event->fee}}" >
                        <input type="hidden" class="capacity" id="capacity{{ $event->id}}" value="{{$event->capacity}}" >
                        <input type="hidden" class="pending" id="pending{{ $event->id}}" value="{{$event->pending}}" >
                        <input type="hidden" class="remaining_slots" value="">
                        <input type="hidden" class="total_slots" value="">
                    </div>
                        <input type="hidden" class="total{{ $event->id}}" name="total" value="">
                    <div class="col-sm-6" style="font-weight:700">
                    Total Due:$<span class="total" id="total{{ $event->id}}"></span>
                    </div>
                    <br><br>
                    <div class="col-sm-12">
                        <input type="hidden" name="user_id" value="{{ (Auth::user())? Auth::user()->id : 0 }}">
                        {!! csrf_field() !!}
                        <input type="hidden" value="1" id="btn_cart" name="btn_cart"/>
                        <button  id="this_event"class="btn btn-darkblue btn-block add_to_cart">Add this event to Cart &nbsp;<i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
                        <button  id="future_event"class="btn btn-darkblue btn-block add_to_cart">Add this and future events to Cart &nbsp;<i class="fa fa-cart-arrow-down" aria-hidden="true"></i></button>
                    </div>
                </div>
                {{-- <input class="form-control qty" id="qty" name="qty" placeholder="Number of Tickets" value="">
                <input class="form-control qty" id="fee" name="feeid=""" placeholder="Number of Tickets" value=""> --}}
            </form>
        </div>
        <div id="apply_volunteer" class="apply_volunteer">
            <div class="row">
                <div class="col-md-12">
                    Volunteers Needed:

                    @if(count($event->volunteer_groups) == 0) <i>None</i>
                    @else
                    <table>
                        @foreach($event->volunteer_groups as $group)
                        <tr>
                            <td width="20%">{{ $group->type }}:</td>
                            <td width="10%">{{ count($group->approved_volunteers) }}/{{ $group->volunteers_needed }}</td>
                            <td width="70%">{{ $group->note }}</td>
                            <td><input class="volunteer_group_class" value="{{ $group->id}}"></td>
                        </tr>
                        @endforeach
                    </table>
                    @endif
                </div>
            </div>
            {{--  @if(Carbon\Carbon::now() < $event->start_date) --}}
            <div class="row">
                <div class="col-md-12">
                    @if(isset($applied))
                    <h4>Thank you for applying!</h4>
                    @else

                    @if($event->volunteer_slots > 0)
                    <a href="javascript:void(0)" class="apply-volunteer btn btn-darkblue float-right">Apply as Volunteer</a>
                    @endif
                    <div style="display:none" class="volunteer-form-container">
                        <form class="volunteer-form" action="{{ route('volunteer_apply') }}" data-unique_url="{{ route('volunteer_unique_email',$event->id) }}" method="post">
                            @if(Auth::user())
                            @if($check_applied_user == 0)
                            <div id="user_details">
                                <div class="form-group">
                                    <label for="{{ $event->id }}-name">Name: </label> {{ Auth::user()->full_name }}
                                </div>
                                <div class="form-group">
                                    <label for="{{ $event->id }}-name">Email: </label> {{ Auth::user()->email }}
                                </div>
                                <input type="hidden" class="user_id" name="volunteers[0][user_id]" id="{{$event->id}}-0-user_id" value="{{ Auth::user()->id }}">
                                <div class="form-group">
                                    <label for="{{ $event->id }}-name">Group: </label>
                                    <select class="form-control" type="text" id="{{ $event->id }}-0-volunteer_group_id" name="volunteers[0][volunteer_group_id]" required>
                                        <option value="" selected disabled>Select A Group</option>
                                        @foreach($event->volunteer_groups as $group)
                                        @if($group->available_slots > 0)
                                        <option value="{{ $group->id }}" >{{ $group->type }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
            {{--                     <input type="hidden" name="event_name_role" value="{{$event->name}} {{$group->type}}"> --}}
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label for="include_user"><input type="checkbox" id="include_user" name="include_user" checked value="true"> Include Yourself</label>
                                </div>
                            </div>
                            @else
                            <h4>You have applied for this event.</h4>
                            @endif
                            @endif
                            <a href="javascript:void(0)" class="btn btn-primary" id="add-volunteer" data-event_id="{{ $event->id }}" data-post_url="{{ route('volunteer_add') }}">+</a>
                            <input class="form-control" type="hidden" id="{{ $event->id }}-slug" name="slug" value = "{{ $slug }}">
                            {{ csrf_field() }}
                            <a href="javascript:void(0)" class="hide-apply-volunteer btn btn-darkblue">X</a>&nbsp;
                            <input type="hidden" name="event_id" id="event_id" value="{{ $event->id  }}">
                            <button  type="submit" href="javascript:void(0)" class="btn btn-darkblue volunteer-submit">Submit</button>
                        </form>
                        <br/><br/>
                        <div class="error-container"></div>
                    </div>
                    @endif


                </div>
                <div class="col-md-6">&nbsp;</div>
            </div>
        </div>
    {{--     @endif --}}

    </div>
  {{--   <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div> --}}
<script type="text/javascript">
$(document).ready(function() {
    $('#this_event').click(function(){
       // alert('0');
        $('#btn_cart').val('0');   
        //$('#this_event').submit();   
    });
    $('#future_event').click(function(){
       // alert('1');
        $('#btn_cart').val('1');   
        //$('#future_event').submit();   

    });
     //load again the page(modal) to fetch the value passed by from calendar js
    $.post('{{ route("event_modal",$slug)}}', function(){
        var start_date = new Date($('td#start').text());
        var start_date_converted = moment.utc(start_date).format();
        var now_date = new Date($('td#now').text());
        var now_date_converted = moment.utc(now_date).format();
        var end_date = new Date($('td#end').text());
        var end_date_converted = moment.utc(end_date).format();
        if(start_date_converted < now_date_converted && end_date_converted < now_date_converted){
            $('#paid_event').hide();
            $('#event_ended').css('display','block');
        } 
        else if(start_date_converted < now_date_converted){
            $('#paid_event').hide(); 
            $('#event_started').css('display','block'); 
        }
        else{
            $('#paid_event').show(); 
        }

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
    if (window.location.href.indexOf("volunteer") > -1) {
        $('#paid_event').addClass('hide_important');
    }
    else{
       $('#apply_volunteer').addClass('hide_important');
    }
    $('form.event').each(function() {
        $('#qty'+ '{{ $event->id }}, #fee' + '{{ $event->id }}').on('input',function() {
            var capacity = parseInt($('#capacity'+ '{{ $event->id }}').val());
            var pending = parseInt($('#pending'+ '{{ $event->id }}').val());
            var remaining = capacity - pending;
            var qty = parseInt($('#qty'+ '{{ $event->id }}').val());
            var fee = parseFloat($('#fee'+ '{{ $event->id }}').val());
            $('#total'+ '{{ $event->id }}').text((qty * fee ? qty * fee : 0).toFixed(2));
            $('.total'+ '{{ $event->id }}').val((qty * fee ? qty * fee : 0).toFixed(2));
            var remaining_slot = $('.remaining_slots').val();//using remaining slot 
            if(qty > remaining_slot){
                alert('You reach the maximum capacity');
                $('#qty'+ '{{ $event->id }}').val(' ');
                $('#total'+ '{{ $event->id }}').text(' ');
                $('.total'+ '{{ $event->id }}').val(' ');
            }
        });
        $('#clear'+ '{{ $event->id }}').on('click', function() {
            $('#qty'+ '{{ $event->id }}').val('');
            $('#total'+ '{{ $event->id }}').text('0.00');
            $('.total'+ '{{ $event->id }}').val('0.00');
        })
    });
    // $('#paid_event').hide();

 function show_qty_input(){
    $('#paid_event').toggle('fast');
 }
//  $('input.qty').keypress(function(e){
//    if (this.value.length == 0 && e.which == 48 ){
//       return false;
//    }
// });
var offset = new Date().getTimezoneOffset();
var now = moment.utc(new Date());
var converted_now = new Date(now.utcOffset(offset * -1));
var now_month = converted_now.getMonth()+1;
var now_day = converted_now.getDate();
var now_year = converted_now.getFullYear();
var now_hour = converted_now.getHours();
var now_minutes = converted_now.getMinutes();
var now_A = '';
if(now_hour == 0){
  var now_result = '12';
}
else{
  var now_result  = now_hour;
}
if(now_hour > 12){
    now_A = 'PM';
    now_hour = (now_hour - 12);

    if(now_hour == 12){
        now_hour = '00';
        now_A = 'AM';
    }
}
else if(now_hour < 12){
    now_A = 'AM';
}else if(now_hour == 12){
    now_A = 'PM';
}else if(now_hour == 0){
    now_hour = '00';
}
if(now_minutes < 10){
    now_minutes = "0" + now_minutes;
}
$('td#now').text(now_month+'/'+now_day+'/'+now_year+' ' + now_hour + ':' + now_minutes + ' ' + now_A);
$('#now').hide();
var start_date_id = moment.utc($('#start_date_id').data('date'));
// var converted_start_date = new Date(start_date_id.utcOffset(offset * -1));
var converted_start_date = new Date(start_date_id);
var start_date_month = converted_start_date.getMonth()+1;
var start_date_day = converted_start_date.getDate();
var start_date_year = converted_start_date.getFullYear();
var start_date_hour = converted_start_date.getHours();
var start_date_minutes = converted_start_date.getMinutes();
var start_date_A = '';
if(start_date_hour == 0){
  start_date_hour = '00';

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
//alert(start_date_day);
$('td#start').text(start_date_month+'/'+start_date_day+'/'+start_date_year+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A);
$('#start_date_timezone').val($('td#start').text());
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
  end_date_hour = '00';
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
$('td#end').text(end_date_month+'/'+end_date_day+'/'+end_date_year+' ' + end_date_hour + ':' + end_date_minutes + ' ' + end_date_A);
$('#end_date_timezone').val($('td#end').text());
$('#span').hide();
var s = new Date($('td#start').text());
var s_c = moment.utc(s).format();
var n = new Date($('td#now').text());
var n_c = moment.utc(n).format();
var e = new Date($('td#end').text());
var e_c = moment.utc(e).format();
//alert(s_c +'----'+ n_c +'----'+ e_c +'----'+ n_c);
// if(s_c < n_c && e_c < n_c){
//     $('#apply_volunteer').hide();  
//     $('#paid_event').hide();
//     $('#event_ended').css('display','block');
//     alert('1'+'---'+s);
//     } 
//     else if(s_c < n_c){
//         alert('2');
//       //  $('#apply_volunteer').hide();  
//         $('#paid_event').hide(); 
//         //alert('tapos na');
//         $('#event_started').css('display','block'); 
//     }
//     else{
//         alert('3');
//            // $('#apply_volunteer').show();  
//             $('#paid_event').show(); 
//         }
////
    //check if an event has passed
    //hide the join event row
    // var date_today_ = moment.utc(new Date());
    // var modal_date_ = new Date($('td#end').text());
    // //var start_date_id = moment.utc($('#start_date_id').data('date'));
    //  if(moment(date_today_) > moment(modal_date_)){
    //     //alert('future');
    //     alert(moment(date_today_)+'----'+moment(modal_date_));
    //  }else{
    //     //alert(moment(date_today_)+'----'+moment(modal_date_));

    //  }
      
});
        
</script>
