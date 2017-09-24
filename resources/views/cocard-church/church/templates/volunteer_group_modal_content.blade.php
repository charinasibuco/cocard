<style type="text/css">
.hide_important{
    display: none !important;
}
</style>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="myModalLabel">{{ $volunteer_group->event->name }}</h3>
</div>
<div class="modal-body">
    <div class="row">
        <input id="volunteer_group" value="" type="hidden">
        <div id="apply_volunteer" class="apply_volunteer">
            <div class="row">
                <div class="col-md-12">
                    <span class="dataTz" id="vg_start_date_id" data-date="{{ $volunteer_group->start_date }}"></span>
                    <span class="dataTz" id="vg_end_date_id" data-date="{{ $volunteer_group->end_date }}"></span>
                    <h4>Volunteer Group Name: {{ $volunteer_group->type }}</h4>
                    <table class="table">
                        <tr>
                            <td width="40%">Volunteers Needed:</td> 
                            <td width="60%">{{ $volunteer_group->volunteers_approved }}/{{ $volunteer_group->volunteers_needed }}</td>
                        </tr>
                        <tr>
                            <td width="40%">From:</td>
                            <td width="60%"><span id="vg_start_date_span"></span></td>
                        </tr>
                        <tr>
                            <td width="40%">To:</td>
                            <td width="60%"><span id="vg_end_date_span"></span></td>
                        </tr>
                        <tr>
                            <td width="40%">
                                Note:
                            </td>
                            <td width="60%">
                                <cite>{{ $volunteer_group->note }}</cite>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            {{--  @if(Carbon\Carbon::now() < $event->start_date) --}}
            <div class="row">
                <div class="col-md-12">
                    @if(isset($applied))
                    <h4>Thank you for applying!</h4>
                    <cite>*We'll send an email if Volunteering was approved!</cite>
                        @if(isset($existing_message))
                            <div class="alert alert-danger">
                              {{ $existing_message }}
                            </div>
                        @endif
                    @else

                    @if($volunteer_group->event->volunteer_slots > 0)
                   {{--  <a href="javascript:void(0)" class="apply-volunteer btn btn-darkblue float-right">Apply as Volunteer</a> --}}
                   {{--  @endif --}}
                        <div style="display:block" class="volunteer-form-container">
                            <form class="volunteer-form" action="{{ route('volunteer_apply') }}" data-unique_url="{{ route('volunteer_unique_email',$volunteer_group->event->id) }}" method="post">
                                @if(Auth::user())
                                    @if($check_applied_user == 0)
                                        <div id="user_details">
                                            <div class="form-group">
                                                <label for="{{ $volunteer_group->event->id }}-name">Name: </label> {{ Auth::user()->first_name }} {{ Auth::user()->last_name }} 
                                            </div>
                                            <div class="form-group">
                                                <label for="{{ $volunteer_group->event->id }}-name">Email: </label> {{ Auth::user()->email }}
                                            </div>
                                            <input type="hidden" class="user_id" name="volunteers[0][user_id]" id="{{$volunteer_group->event->id}}-0-user_id" value="{{ Auth::user()->id }}">
                                            <div class="form-group">
                                             {{--    <label for="{{ $volunteer_group->event->id }}-name">Group: </label> --}}
                                           {{--      <select class="form-control" type="text" id="{{ $volunteer_group->event->id }}-0-volunteer_group_id" name="volunteers[0][volunteer_group_id]" required>
                                                    <option value="" selected disabled>Select A Group</option>
                                                    @foreach($volunteer_group->event->volunteer_groups as $group)
                                                    @if($group->available_slots > 0)
                                                    <option value="{{ $group->id }}" >{{ $group->type }}</option>
                                                    @endif
                                                    @endforeach
                                                </select> --}}
                                                <input name="volunteers[0][volunteer_group_id]" id="{{ $volunteer_group->event->id }}-0-volunteer_group_id"  type="hidden" value="{{ $volunteer_group->id }}">
                                            </div>
                                                {{-- <input type="hidden" name="event_name_role" value="{{$event->name}} {{$group->type}}"> --}}
                                        </div>
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label for="include_user"><input type="checkbox" id="include_user" name="include_user" checked value="true"> Include Yourself</label>
                                            </div>
                                        </div>
                                    @else
                                        <h4>You have applied for this Volunteer Group.</h4>
                                    @endif
                                @endif
                                @if($volunteer_group->volunteers_approved != $volunteer_group->volunteers_needed)
                                    <a href="javascript:void(0)" class="btn btn-primary" id="add-volunteer" data-volunteer_group_id="{{ $volunteer_group->id }}" data-event_id="{{ $volunteer_group->event->id }}" data-post_url="{{ route('volunteer_add') }}">+</a>
                                    <input class="form-control" type="hidden" id="{{ $volunteer_group->event->id }}-slug" name="slug" value = "{{ $slug }}">
                                    {{ csrf_field() }}
                                  {{--   <a href="javascript:void(0)" class="hide-apply-volunteer btn btn-darkblue">X</a>&nbsp; --}}
                                    <input type="hidden" name="event_id" id="event_id" value="{{ $volunteer_group->event->id  }}">
                                    <input type="hidden" name="volunteer_group_id" id="volunteer_group_id" value="{{ $volunteer_group->id  }}">
                                    <button  type="submit" href="javascript:void(0)" class="btn btn-darkblue volunteer-submit">Apply</button>
                            </form>
                            <br/><br/>
                            <div class="error-container"></div>
                            @else
                                <div class="alert alert-danger">
                                  Group is already full.
                                </div>
                            @endif
                        </div>
                        @endif
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
<script>
    if (window.location.href.indexOf("volunteer") > -1) {
        $('#paid_event').addClass('hide_important');
    }
    else{
       $('#apply_volunteer').addClass('hide_important');
    }

function show_qty_input(){
    $('#paid_event').toggle('fast');
 }
 $('input.qty').keypress(function(e){
   if (this.value.length == 0 && e.which == 48 ){
      return false;
   }
});







applicants = function(){
    if($('#include_user').is(":checked")){
        alert('checked');
    }else{
        alert('uncheck');
    }
}

// $('.volunteer-submit').on('click',function(){
//     alert($('.volunteer-fieldset').length);
// });
$('#include_user').on('click',function(){
    volunteerSubmitButton('include_user');
});

// this section is for date time convertion from UTC date time(fom database) 
// to user browser timezone shown to this blade
var recurring = '{{ $volunteer_group->event->recurring }}';
var no_of_occurrence = '{{ $volunteer_group->no_of_occurrence }}';
convertion_modal = function(date){
    var start_date_id = moment.utc($(date+'id').data('date'));
    var converted_start_date = new Date(start_date_id.utcOffset(offset * -1));
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
    $(date+'span').text(start_date_month+'/'+start_date_day+'/'+start_date_year+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A);
    var date2 =$(date+'span').text();
    var startDate = new Date(date2);
    var endDateMoment = moment(startDate);
    if(recurring == 1){
      endDateMoment.add(no_of_occurrence, 'weeks');  
    }else if(recurring == 2){
      endDateMoment.add(no_of_occurrence, 'months');
    }else if(recurring == 3){
      endDateMoment.add(no_of_occurrence, 'years');
    }
    var recurring_date = new Date(endDateMoment);
    // alert(recurring_date);

    var hour= recurring_date.getHours();
    var minutes= recurring_date.getMinutes();
    var day= recurring_date.getDate();
    var months= recurring_date.getMonth()+1;
    var year= recurring_date.getFullYear();
    var a ='';
    //console.log(result_month[i] +' '+ f[i]);
    if(hour > 12){
        a = 'PM';
        hour = (hour - 12);

        if(hour == 12){
            hour= '00';
            a = 'AM';
        }
    }
    else if(hour < 12){
    a = 'AM';   
    }else if(hour == 12){
    a = 'PM';
    }else if(hour == 0){
    hour = '00';
    }
    if(minutes < 10){
    minutes = "0" + minutes; 
    }
    if(hour== 0){
    hour= '12';
    }
    if(date == '#start_date_'){
        $('#start_date_span').text(months + '/' + day + '/' + year + ' ' + hour + ':' + minutes +' ' + a);
        console.log(endDateMoment);
    }
    if(date == '#end_date_'){
        $('#end_date_span').text(months + '/' + day + '/' + year + ' ' + hour + ':' + minutes +' ' + a);
    }
}
var vg_start_date_var   = '#vg_start_date_';
var vg_end_date_var   = '#vg_end_date_';
var start_date_var    = '#start_date_';
var end_date_var    = '#end_date_';
convertion_modal(vg_start_date_var);
convertion_modal(vg_end_date_var);
convertion_modal(start_date_var);
convertion_modal(end_date_var);
$('#now').hide();
$('.dataTz').css('display', 'none');
</script>
