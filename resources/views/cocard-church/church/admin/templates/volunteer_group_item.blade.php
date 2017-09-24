<style type="text/css">
.empty_field{
    border:solid red 1px;
    background-color: pink;
}
</style>

<div class="modal fade" id="delete_modal_{{$id or ''}}" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
                <p>Are you sure you want to delete this Volunteer Group?</p>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                        <a id="volunteer-group-{{ $count }}-delete" data-count="{{$count}}" data-status="{{ $status or ''}}" data-id="{{ $id or ''}}" href="javascript:void(0)" onclick="deleteVolunteerGroup(this)" class="btn btn-red btn-full" >YES</a>
                </div>
                <div class="col-md-6">
                    <button type="button" data-dismiss="modal" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="">Cancel</button>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
{{-- <div class="row per-volunteer-group-item" id="form_{{ $count }}" @if($type == '') style="display:none" @endif> --}}
<div class="row per-volunteer-group-item" id="form_{{ $count }}" @if($type == '') style="display:none" @endif>
    <div class="col-md-offset-1 col-md-10">
        <br>
        <div class="alert alert-success alert-dismissable" id="volunteer-group-row_{{ $count}}" style="display:none">
            {{-- <input type="hidden" name="volunteer_groups[{{ $count }}][deleted_id]" id="deleted_id_{{ $id or ''}}"  value=""> --}}
            <a id="volunteer-group-{{ $count }}-undo" data-count="{{$count}}" data-status="InActive" data-id="{{ $id or ''}}" href="javascript:void(0)" onclick="undoVolunteerGroup(this)" class="btn btn-danger">Undo</a></a>
                Successfully Deleted this Volunteer Group.
            <a href="#" class="close" data-dismiss="alert" aria-label="close" style="padding:8px">&times;</a>
        </div>
        <div class="volunteer-group-item" id="volunteer-group-item-{{$id or ''}}">
            <div class="form-group">
                <div class="clearfix">
                    <label class="pull-right">
                     {{--    <a id="volunteer-group-{{ $count }}-delete" data-count="{{$count}}" href="javascript:void(0)" onclick="deleteVolunteerGroup(this)" class="btn btn-danger">X</a> --}}
                       @if(isset($id))
                       <a  class="btn btn-danger" data-toggle="modal" data-target="#delete_modal_{{ $id or ''}}">X</a>
                       {{-- <a href="#" id="delete_vg" data-id="">Delete</a> --}}
                       @else
                       <a id="volunteer-group-{{ $count }}-cancel" data-status="{{ $status or '' }}" data-id="{{ $id or ''}}" href="javascript:void(0)" onclick="cancelVolunteerGroup(this)" class="btn btn-warning">Cancel</a>
                       @endif
                    </label>
                </div>
             {{--    {{ $id }}
                {{ $count }} --}}
                <label for="volunteer-group-{{ $count }}-title">Volunteer Title:</label>
                <input type="hidden"name="volunteer_groups[{{ $count }}][id]"  value="{{ $id or '' }}">
                <input type="hidden"name="volunteer_groups[{{ $count }}][status]"  value="{{ $status or '' }}" id="status_{{ $count }}">
                <input type="hidden" name="volunteer_groups[{{ $count }}][no_of_occurrence]"  value="{{ $no_of_occurrence or '' }}" id="no_of_occurrence_{{ $count }}">
                <input type="text" class="form-control volunteer-group-type" name="volunteer_groups[{{ $count }}][type]" id="volunteer-group-{{ $count }}-type" value="{{ $type or '' }}" required>
            </div>
{{--             <div class="form-group">
                <label for="status-active">Status:</label>
                <label class="radio-inline" for="volunteer-group-{{ $count }}-status-active" ><input @if(!isset($status) || $status == "Active") checked @endif id="volunteer-group-{{ $count }}-status-active" type="radio" name="volunteer_groups[{{ $count }}][status]" value="Active"  checked="checked">Active</label>
                <label class="radio-inline" for="volunteer-group-{{ $count }}-status-inactive"><input @if(isset($status) && $status == "InActive") checked @endif id="volunteer-group-{{ $count }}-status-inactive" type="radio" name="volunteer_groups[{{ $count }}][status]" value="InActive">InActive</label>
            </div> --}}
            @if(isset($id))
                <?php
                    $vg = App\VolunteerGroup::where('id', $id)->first();
                ?>
            @endif
            @if(isset($event->id))
            <?php
                        $start_date_parse = Carbon\Carbon::parse($event->start_date);
                        $original_recurring_end_date_parse = Carbon\Carbon::parse($event->original_recurring_end_date);
                        if($event->recurring == 2){
                            if($event->original_no_of_repetition == 0){
                                $total_no_of_occurrence = $original_recurring_end_date_parse->diffInMonths($start_date_parse);  
                            }else{
                                $total_no_of_occurrence = $event->original_no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addMonths($event->original_no_of_repetition); 
                            }
                        }elseif ($event->recurring == 1) {
                            if($event->original_no_of_repetition == 0){
                                $total_no_of_occurrence = $original_recurring_end_date_parse->diffInWeeks($start_date_parse) ; 
                            }else{
                                $total_no_of_occurrence = $event->original_no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addWeeks($event->original_no_of_repetition); 
                            }
                        }elseif ($event->recurring == 3){
                            if($event->original_no_of_repetition == 0){
                                $total_no_of_occurrence = $original_recurring_end_date_parse->diffInYears($start_date_parse); 
                            }else{
                                $total_no_of_occurrence = $event->original_no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addYears($event->original_no_of_repetition);
                            }
                        }else{
                            $total_no_of_occurrence = 0; 
                        }
                    ?>
            @endif
            <div id="span" style="display:block">
                <span class="dataTz" id="volunteer_group_start_date_id_{{$count}}"  data-date="{{ $vg->start_date or '' }}"> &nbsp; </span></br>
                <span class="dataTz" id="volunteer_group_end_date_id_{{$count}}"  data-date="{{ $vg->end_date or ''}}"> &nbsp; </span></br>
            </div>
            <div class="form-group">
                    <label>Start date</label>
                    <div class="input-group startdtp2" id="volunteer_group_startdtp_{{ $count }}">
                        <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="volunteer_group_start_date2" id="volunteer_group_start_date_{{ $count }}" value="{{ $start_date or ''}}" required="required">
                        <span class="input-group-addon">
                            <span class="glyphicon-calendar glyphicon"></span>
                        </span>
                    </div>
                    <input type="hidden" name="volunteer_groups[{{ $count }}][start_date]" id="volunteer_group_start_date_timezone_{{ $count }}" value="" >
                    @if ($errors->has('volunteer_group_start_date2'))
                    <span class="help-block">
                        <strong style="color:red;">Start date field is required</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label>End date</label>
                        <div class='input-group enddtp2' id="volunteer_group_enddtp_{{ $count }}">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="volunteer_group_end_date2"  value="{{ $end_date or ''}}" id="volunteer_group_end_date_{{ $count }}" required>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <input type="hidden" name="volunteer_groups[{{ $count }}][end_date]" id="volunteer_group_end_date_timezone_{{ $count }}" value="" >
                        @if ($errors->has('volunteer_group_end_date2'))
                        <span class="help-block">
                            <strong style="color:red;">End date field is required</strong>
                        </span>
                        @endif
                </div>
            <div class="form-group">
                <label for="volunteer-group-{{ $count }}-title">Number of Volunteers Needed:</label>
                <input type="number" class="form-control volunteer-group-volunteers-needed" name="volunteer_groups[{{ $count }}][volunteers_needed]" id="volunteer-group-{{ $count }}-volunteers_needed" value="{{ $volunteers_needed or '' }}" required>
            </div>
            <div class="form-group">
                <label for="volunteer-group-{{ $count }}-title">Note:</label>
                <textarea class="form-control volunteer-group-note" name="volunteer_groups[{{ $count }}][note]" id="" cols="5" rows="2" id="volunteer-group-{{ $count }}-note">{{ $note or "" }}</textarea>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript">
$( document ).ready(function() {
    var count = '{{ $count }}';
    var attribute ='';
    var empty_field =0;
    validationForEmptyField = function(attribute){
        var field = $('#volunteer-group-'+ count +'-'+attribute)
        var dates = $('#volunteer_group_'+ attribute +'_'+count)
        customValidation = function(any){
        any.on('input', function(){
            if(any.val().length == 0){
                any.addClass('empty_field');
                any.after( "<cite class='error_message' style='color:red;font-size:10px'>This field is required!</cite>" );
            }else{
                any.removeClass('empty_field');
                any.nextAll('.error_message').remove();
            }
        });
        }
        customValidation(dates);
        customValidation(field);
    }
    validationForEmptyField('start_date');
    validationForEmptyField('end_date');
    validationForEmptyField('type');
    validationForEmptyField('volunteers_needed');
    validationForEmptyField('note');

    $('.startdtp2').datetimepicker({
        format: 'M/D/YYYY h:mm A',
    });
    $('.enddtp2').datetimepicker({
        useCurrent: false,
        format: 'M/D/YYYY h:mm A'
    });
    var now =moment().millisecond(0).second(0).minute(0).hour(0);
    $('.startdtp2').data("DateTimePicker").minDate(now);
    $(".startdtp2").on("dp.change", function (e) {
        $('.enddtp2').data("DateTimePicker").minDate(e.date);
        $('input[name="volunteer_group_start_date2"]').removeClass('empty_field');
        $('input[name="volunteer_group_start_date2"]').nextAll('.error_message').remove();
        
    });
    $(".enddtp2").on("dp.change", function (e) {
        $('.startdtp2').data("DateTimePicker").maxDate(e.date);
        $('input[name="volunteer_group_end_date2"]').removeClass('empty_field');
        $('input[name="volunteer_group_end_date2"]').nextAll('.error_message').remove();
    });
    var count =  '{{$count}}';
    startDatePopUp = function(){
        var now =moment().millisecond(0).second(0).minute(0).hour(0);
        var start = new Date($("#volunteer_group_start_date_"+ count).val());
        var end = new Date($("#volunteer_group_end_date_"+ count).val());
        if( start > end){
            $('#myModalStart').modal('show');
            $("#volunteer_group_end_date_"+ count).val(' ');
        }
    }
    
    startDate = function(){
            $("#volunteer_group_start_date_"+count).on("change keyup paste autocompletechange mouseenter keydown input", function(){
                var a = new Date($("#volunteer_group_start_date_"+count).val());
                //$("#volunteer_group_start_date_"+ count).val();
                var b = moment.utc(a).format();
                $("#volunteer_group_start_date_timezone_"+count).val(b);
                startDatePopUp();
            });
            $('#volunteer_group_startdtp_'+ count).on("dp.change", function () {
                var a = new Date($("#volunteer_group_start_date_"+count).val());
                //$("#volunteer_group_start_date_"+ count).val();
                var b = moment.utc(a).format();
                $("#volunteer_group_start_date_timezone_"+count).val(b);
                startDatePopUp();
            });
        }
        
    endDate = function(){
            $("#volunteer_group_end_date_"+ count).on("change keyup paste autocompletechange mouseenter keydown input", function(){
                var a = new Date($('#volunteer_group_end_date_'+ count).val());
                var b = moment.utc(a).format();
                $('#volunteer_group_end_date_timezone_'+ count).val(b);
            });
            $('#volunteer_group_enddtp_'+ count).on("dp.change", function () {
                var a = new Date($('#volunteer_group_end_date_'+ count).val());
                var b = moment.utc(a).format();
                $('#volunteer_group_end_date_timezone_'+ count).val(b);
            });
        }
        $(".startdtp2").on("dp.change", function (e) {
            startDatePopUp();
        });
         $(".enddtp2").on("dp.change", function (e) {
            startDatePopUp();
         });
        startDate();
        endDate();
        $('span.input-group-addon , span.glyphicon-calendar').on('click change', function (){
            // var now =moment().millisecond(0).second(0).minute(0).hour(0);
            // $('#volunteer_group_startdtp_'+count).data("DateTimePicker").minDate($('#start_date').val());
            // $('#volunteer_group_startdtp_'+count).data("DateTimePicker").maxDate($('#end_date').val());
            // $('#volunteer_group_enddtp_'+count).data("DateTimePicker").maxDate($('#end_date').val());
            
            // $('#volunteer_group_start_date_timezone_'+count).val($('#start_date').val());
            // $('#volunteer_group_end_date_timezone_'+count).val($('#end_date').val());
        });
        $(".startdtp").on("dp.change", function (e) {
            $('#volunteer_group_start_date_'+count).val($('#start_date').val());
            var a = new Date($("#volunteer_group_start_date_"+count).val());
            var b = moment.utc(a).format();
            $("#volunteer_group_start_date_timezone_"+count).val(b);
            startDatePopUp();
        });
        $(".enddtp").on("dp.change", function (e) {
            $('#volunteer_group_end_date_'+count).val($('#end_date').val());
            var a = new Date($('#volunteer_group_end_date_'+ count).val());
            var b = moment.utc(a).format();
            $('#volunteer_group_end_date_timezone_'+ count).val(b);
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
    convertion = function(date, limit){
        for (var i = count; i <= limit; i++) {
        created_date[i] = moment.utc($(date+'id_'+ i ).data('date'));
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
            hour3[i] = '00';
        }
            if($(date+'id_'+ i ).data('date') !== '' && $(date+'id_'+ i ).data('date') !== '0000-00-00 00:00:00'){
                $(date + i).val(month3[i] + '/' + day3[i] + '/' + year3[i] + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);

                var a = new Date($('#volunteer_group_start_date_'+ i).val());
                $('#volunteer_group_start_date_'+ i).val();
                var b = moment.utc(a).format();
                $('#volunteer_group_start_date_timezone_'+i).val(b);

                var d = new Date($('#volunteer_group_end_date_'+ i).val());
                $('#volunteer_group_end_date_'+ i).val();
                var e = moment.utc(d).format();
                $('#volunteer_group_end_date_timezone_'+i).val(e);
                }
            else{
                var start_date_event = $('#start_date').val();
                var end_date_event   = $('#end_date').val();
                $('#volunteer_group_start_date_' + i).val(start_date_event);
                $('#volunteer_group_end_date_' + i).val(end_date_event);

                var a = new Date($('#volunteer_group_start_date_'+ i).val());
                $('#volunteer_group_start_date_'+ i).val();
                var b = moment.utc(a).format();
                $('#volunteer_group_start_date_timezone_'+i).val(b);

                var d = new Date($('#volunteer_group_end_date_'+ i).val());
                $('#volunteer_group_end_date_'+ i).val();
                var e = moment.utc(d).format();
                $('#volunteer_group_end_date_timezone_'+i).val(e);
            }
            
        }
    }
var limit = count;
var start_date_var   = '#volunteer_group_start_date_';
var end_date_var     = '#volunteer_group_end_date_';
convertion(start_date_var, limit);
convertion(end_date_var, limit);
// autoStartDate2();
// autoEndDate2();

$('.dataTz').css('display', 'none');

    var start_date_id = moment.utc($('#start_date_id').data('date'));

    var offset = new Date().getTimezoneOffset();
    var timezone = new Date().toString().match(/([A-Z]+[\+-][0-9]+)/)[1];
    //start date
    var converted_start_date = new Date(start_date_id.utcOffset(offset * -1));
    var start_date_hour = converted_start_date.getHours();
    var start_date_minutes = converted_start_date.getMinutes();
    // var start_date_result  = start_date_hour;
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
        // start_date_result = ((start_date_hour < 10) ? "0" + start_date_hour : start_date_hour);
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

var split_hash = (window.location.hash).substring(1);
var arr = split_hash.split('/');
var hash_date = new Date(arr[0]);
// var original_start_date= moment.utc('{{ $start_date_parse or  ''}}').format();
var original_start_date= new Date('{{ $start_date_parse or  ''}}');
// console.log('vg_item - original_start_date: ' + original_start_date);
var hash_current_start_date = (hash_date.getMonth()+1)+'/'+hash_date.getDate()+'/'+hash_date.getFullYear()+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A;
// console.log('hash_current_start_date: ' + hash_current_start_date);
// var current_start_date = moment.utc(hash_current_start_date).format();
var current_start_date = new Date(hash_current_start_date);
// console.log('vg_item - current_start_date: ' + current_start_date);

// alert(hash_current_start_date);
// var original_recurring_end_date =moment.utc('{{ $original_recurring_end_date_parse or  ''}}').format();
var original_recurring_end_date =new Date('{{ $original_recurring_end_date_parse or '' }}');
// console.log('vg_item - original_recurring_end_date: ' + original_recurring_end_date);
// var no_of_repetition_date = moment.utc('{{ $no_of_repetition_date or  ''}}').format();
var no_of_repetition_date = new Date('{{ $no_of_repetition_date or ''}}');
// console.log('vg_item - no_of_repetition_date: ' + no_of_repetition_date);
var original_no_of_repetition     ='{{ $event->original_no_of_repetition or ''}}';
var actual_total_no_of_occurrence = '{{ $total_no_of_occurrence or ''}}';

if(original_no_of_repetition == 0){
    var total_current_days = (original_recurring_end_date- current_start_date) / (1000 * 60 * 60 * 24);
}else{
    var total_current_days = (no_of_repetition_date- current_start_date) / (1000 * 60 * 60 * 24);
}

var recurring = '{{ $event->recurring or '' }}';
if(recurring == 2){
    var total_no_of_occurrence = ('{{ $total_no_of_occurrence or ''}}');
}else{
    var total_no_of_occurrence = '{{ $total_no_of_occurrence or ''}}';
}

if(recurring == 2){
   var no_of_month_diff = (Math.floor(total_current_days)) / 31;
    var diff = Math.floor(no_of_month_diff);
    var no_of_occurrence = (total_no_of_occurrence - diff) - 1 ; 
    $('#occurrence').val(no_of_occurrence);
}else if(recurring == 1){
    var no_of_week_diff = (Math.floor(total_current_days)) / 7;
    var diff = Math.floor(no_of_week_diff);
    var no_of_occurrence = total_no_of_occurrence - diff; 
    $('#occurrence').val(no_of_occurrence);
}
else if(recurring == 3){
    var no_of_year_diff = (Math.floor(total_current_days)) / 365;
    var diff = Math.floor(no_of_year_diff);
    var no_of_occurrence = total_no_of_occurrence - diff; 
    $('#occurrence').val(no_of_occurrence);
}else{
    // var no_of_year_diff = (Math.floor(total_current_days)) / 365;
    // var diff = Math.floor(no_of_year_diff);
    var no_of_occurrence = 0; 
    $('#occurrence').val(0);
}
 // console.log('count --'+ '{{ $count }}', 'current_start_date--'+current_start_date, 'recurring_end_date--'+recurring_end_date,'total_no_of_occurrence---' +total_no_of_occurrence, 'no_of_occurrence---' +no_of_occurrence, 0/31 ,no_of_month_diff, (Math.floor(total_current_days))/31 + 'recurring' + recurring +'total_current_days '+total_current_days+' no_of_repetition'+no_of_repetition);
 // var no_of_occurrence = 
//alert(no_of_occurrence+'---'+(count)+(1));
// var occurrence_number = (no_of_occurrence)+(1);
// alert(occurrence_number);
// if($('#no_of_occurrence_{{ $count }}').val() == occurrence_number){
//    //$('#form_{{ $count }}').css('display', 'block');
//    $('#form_{{ $count }}').show();
// }else if($('#no_of_occurrence_{{ $count}}').val() == ''){
//     //$('#form_{{ $count }}').css('display', 'block');
//     $('#form_{{ $count }}').show();
// }else{
//     //$('#form_{{ $count }}').css('display', 'none')
//     $('#form_{{ $count }}').hide();
// }
$('#form_{{ $count }}').show();
});

</script>