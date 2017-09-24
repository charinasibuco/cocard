<div class="row">
    <table class="table table-striped" id="tabledata">
        <thead class="theader">
            <th></th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </thead>
        <tbody>
            @if(($volunteers->count() == 0))
            <tr>
                <td>No records to show</td>
            </tr>
            @else
            <?php 
            $x =0; 
            ?>
            @foreach($volunteers as $row)
            <tr id="row_{{ $row->id }}">
                <td><input type="checkbox" id="row_selected_{{$x}}" name="row_selected_{{ $row->id}}" value="{{ $row->email }}"></td>
                <td> @if($row->user_id == 0){{$row->name}}@else {{$row->User->first_name}}@endif</td>
                <td> @if($row->user_id == 0){{$row->email}}@else {{$row->User->email}}@endif</td>
                <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $row->volunteer_group->start_date }}" style="display:none"> &nbsp; </span>
                <td data-id="data_start_{{ $row->id }}" id="start_date_timezone_{{ $x }}" style="display:none"></td>
              {{--   <input id="start_date_reminder_{{ $x }}" type="text" value=""> --}}
                <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $row->volunteer_group->end_date }}" style="display:none"> &nbsp; </span>
                <td data-id="data_end_{{ $row->id }}" id="end_date_timezone_{{ $x }}" style="display:none"></td>
                <td id="status-per-volunteer_{{ $row->id}}">{{$row->volunteer_group_status}}</td>
                <td id="loading_{{ $row->id}}" align="center" style="display:none; width:100%"></td>  
              {{--   <td id="status-per-volunteer_{{ $row->id}}"></td> --}}
                <td>
                    <a title="Approve" id="approved_{{ $row->id }}" data-title="Approved" data-id="{{ $row->id }}" data-status="{{$row->volunteer_group_status}}" onclick="checkStatus(this)" class="btn btn-success" data-disabled="{{ $row->volunteers_approved_count_to_disabled }}" {{ $row->volunteers_approved_count_to_disabled }}>
                        <i class="icon-like" aria-hidden="true"></i>
                    </a>
                    <a title="Decline" class="delete-link btn btn-danger" id="decline_{{ $row->id }}" data-title="Decline" data-id="{{ $row->id }}" data-status="{{$row->volunteer_group_status}}" onclick="checkStatus(this)">
                        <i class="icon-dislike" aria-hidden="true"></i>
                    </a>
                 {{--    {{ url('organization/'.$slug.'/administrator/volunteer/filter-by-event/view-volunteers-by-event/Reject',$row->id) }} --}}
                    <a title="Send Message" href="" data-toggle="modal" data-target="#volunteer{{ $row->id}}" class="btn btn-default">
                       <i class="fa fa-envelope" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
            <!-- Modal -->
            <div class="modal fade" id="volunteer{{ $row->id }}" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Send Message to {{ $row->name }}</h4>
                        </div>
                        <div class="modal-body">
                             <div id="sent_{{ $row->id}}" style="display:none" align="center" style="text-align:center">Message sent</div>
                             <div id="form_{{ $row->id }}">
                                <label>To:</label>
                                <input class="form-control not_empty" id="email_{{$row->id}}" name="email" value="{{ $row->email}}" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" disabled>
                                <br>
                                <label>Subject:</label>
                                <input class="form-control not_empty" value="" id="subject_{{$row->id}}" name="subject" required>
                                <label>Message:</label>
                                <textarea class="form-control not_empty" value="" id="message_{{$row->id}}" name="message" required></textarea>
                            </div>
                            <div class='loading' align="center" style="display:none"></div>
                            <div class="modal-footer">
                                {!! csrf_field() !!}
                                <button type="submit" class="btn btn-default send" data-button="specific" data-id="{{$row->id}}" id="group_{{ $row->id }}" onclick="sendMessage(this)">Send</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $x++; ?>
            @endforeach
            @endif
        </tbody>
    </table>
    @if(($volunteers->count() > 0))
    <input type="checkbox" id="select-all" name="save_value" style="margin-left:8px" value="">  Select All<br><br>
    <button class="btn btn-default" data-toggle="modal" data-target="#member" id="save_volunteer_value" name="save_value" disabled>Send Message</button>

    @endif
     <a href="{{ url('/organization/'.$slug.'/administrator/volunteer/?volunteer_type='.$volunteers->first()->volunteer_group->type.'&event_name='.$volunteers->first()->volunteer_group->Event->name.'&event_id='.$volunteers->first()->volunteer_group->Event->id)}}">
   {{--   <a onclick="churchVolunteerList(this)" id="back" data-event_id="{{$volunteers->first()->volunteer_group->Event->id}}" data-event_name="{{$volunteers->first()->volunteer_group->Event->name}}" data-volunteer_type="{{ $volunteers->first()->volunteer_group->type }}"> --}}
    <button type="submit" class="btn btn-blue float-right" style="background-color: #04191C !important; color:#fff;">
        <i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
    </button>
    </a>
    <!-- Modal -->
    <div class="modal fade" id="member" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Send Message to {{ $volunteers->first()->volunteer_group->type}} Volunteer</h4>
                </div>
                <div class="modal-body">
                    <div id="sent" style="display:none" align="center" style="text-align:center">Message sent</div>
                        <div id="form_all">
                            <label>To:</label>
                            <input class="form-control not_empty" id="multiple_email" name="email" value="" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required>
                            <br>
                            <label>Subject:</label>
                            <input class="form-control not_empty" value="" id="subject" name="subject" required>
                            <label>Message:</label>
                            <textarea class="form-control not_empty" value="" id="message" name="message" required></textarea>
                        </div>
                        <div class='loading' align="center" style="display:none"></div>
                    </div>
                    <div class="modal-footer">
                        {!! csrf_field() !!}
                        <button type="submit" class="btn btn-default send" data-button="all" id="group_all" onclick="sendMessage(this)">Send</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var removeLastChar = function(value, char){
        var lastChar = value.slice(-1);
        if(lastChar == char) {
          value = value.slice(0, -1);
      }
      console.log(value);
      return value;
    }
    //alert($(":checkbox:checked").length);
$(function(){
    $('#save_volunteer_value').click(function(){
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        $('#multiple_email').val(val);
        var nums = $('#multiple_email').val();
        var result = removeLastChar(nums, ',');
        $('#multiple_email').val(result);
    });
});
$('#select-all').click(function(event) {
    if(this.checked) {
        $(':checkbox').prop('checked', true);
    } else {
        $(':checkbox').prop('checked', false);
    }
});
if($(":checkbox:checked").length > 0){
    $('#save_volunteer_value').prop('disabled', false);
}
var count = '{{ $volunteers->count() }}';
for(var x =0; x <= count; x++) {
    $('#row_selected_'+x).click(function(){
        buttonDisabled();
    });
}
 $('.not_empty').on('input',function(){
        $('.not_empty').css('border','solid rgb(204, 204, 204) 1px');
 });
$('#select-all').click(function(){
    buttonDisabled();
});
 $('.not_empty').on('input',function(){
        $('.not_empty').css('border','solid rgb(204, 204, 204) 1px');
 });
buttonDisabled = function(){
    if($(":checkbox:checked").length > 0){
        $('#save_volunteer_value').prop('disabled', false);
    }else{
        $('#save_volunteer_value').prop('disabled', true);
    }
}
var volunteer_group_id = '{{$volunteers->first()->volunteer_group_id}}';
var slug ='{{ $slug }}';
function volunteerDetailsTable(){
    $.get('{{ route("volunteer_detail_table") }}',{slug:slug,volunteer_group_id:volunteer_group_id}).done(function(data){
        $('.volunteer-table').empty().html(data);
    });
}
churchVolunteerList = function(e){
    var url = "{{ route('church_volunteer_list',$slug) }}";
    var volunteer_groups = new Object();
    volunteer_groups.event_name = $("#"+ e.id).data('event_name');
    volunteer_groups.volunteer_type = $("#"+ e.id).data('volunteer_type');
    volunteer_groups.event_id = $("#"+ e.id).data('event_id');
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    $.get(url,{event_name:volunteer_groups.event_name,volunteer_type:volunteer_groups.volunteer_type,event_id:volunteer_groups.event_id}).done(function(data){
        $('#table-content').empty().html(data);
    });
}
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
        }else{
            $(date + 'timezone_'+ i ).text('-------');
        }
        $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
        $(date + i ).hide();

    }
}

var limit = {{ count($volunteers)}};
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(start_date_var, limit);
convertion(end_date_var, limit);
</script>