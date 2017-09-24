@extends('layouts.app')
@section('style')
<style type="text/css">
.empty_field{
    border:solid red 1px;
    background-color: pink;
}
</style>
@endsection
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Event</h3>
                <input type="hidden" value="{{ $action_name}}" id="action_name">
            </div>
            <div class="col-md-6">

                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
        </div>
        <div class="table-main panel panel-default well">
            <form id="event-form" class="form-horizontal" method="POST" action="{{ $action }}" >
                <input type="hidden" id="instance" name="instance" value="{{ $instance }}">
                <div class="form-group">
                    <div class="col-sm-9">
                        <input type="hidden" class="form-control" name="organization_id" value="{{ $organization_id }}" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Event name</label>
                    <div class="col-sm-9">
                        <input type="hidden" name="slug" value="{{ $slug }}" >
                        <input type="text" class="form-control" name="name" value="{{ $name }}">
                        @if ($errors->has('name'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <input type="hidden" value="" name="hash" class="hash">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" value="{{ $description }}">
                        @if ($errors->has('description'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('description') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Capacity</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="capacity" value="{{ $capacity }}">
                        @if ($errors->has('capacity'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('capacity') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Fee</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                             <span class="input-group-addon">$</span>
                            <input type="number" class="form-control" name="fee" value="{{ $fee }}">
                        </div>
                        @if ($errors->has('fee'))
                            <span class="help-block">
                                <strong style="color:red;">{{ $errors->first('fee') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div id="span" style="display:none">
                    <span class="dataTz" id="now" data-date="{{ Carbon\Carbon::now() }}"> &nbsp; </span></br>
                    <span class="dataTz" id="start_date_id"  data-date="{{ $start_date }}"> &nbsp; </span></br>
                    <span class="dataTz" id="end_date_id"  data-date="{{ $end_date }}"> &nbsp; </span></br>
                    <span class="dataTz" id="reminder_date_id" data-date="{{ $reminder_date }}"> &nbsp; </span></br>
                </div>
                <div class="form-group">
                    
                    <?php
                        $start_date_parse = Carbon\Carbon::parse($start_date);
                        $recurring_end_date_parse = Carbon\Carbon::parse($recurring_end_date);
                        // echo 'recurring_end_date: '.$recurring_end_date . '<br>';
                        // echo 'recurring_end_date_parse: '.$recurring_end_date_parse . '<br>';
                        if($recurring == 2){
                            if($no_of_repetition == 0){
                                $total_no_of_occurrence = $recurring_end_date_parse->diffInMonths($start_date_parse);  
                            }else{
                                $total_no_of_occurrence = $no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addMonths($no_of_repetition); 
                            }
                        }elseif ($recurring == 1) {
                            if($no_of_repetition == 0){
                                $total_no_of_occurrence = $recurring_end_date_parse->diffInWeeks($start_date_parse) ; 
                            }else{
                                $total_no_of_occurrence = $no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addWeeks($no_of_repetition); 
                            }
                        }elseif ($recurring == 3){
                            if($no_of_repetition == 0){
                                $total_no_of_occurrence = $recurring_end_date_parse->diffInYears($start_date_parse); 
                            }else{
                                $total_no_of_occurrence = $no_of_repetition -1; 
                                $no_of_repetition_date= $start_date_parse->addYears($no_of_repetition);
                            }
                        }else{
                            $total_no_of_occurrence = 0; 
                        }
                    ?>
                    <label class="col-sm-2 control-label">Start date</label>
                    <div class="col-sm-9">
                        <div class="input-group startdtp" id="startdtp">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="start_date2" id="start_date" value="{{ (old('start_date2'))? old('start_date2') :  $start_date }}">
                            <span class="input-group-addon">
                                <span class="glyphicon-calendar glyphicon"></span>
                            </span>
                        </div>
                        <input type="hidden" name="start_date" id="start_date_timezone" value="" >
                        <input type="hidden" name="start_date_timezone_per_occurrence" id="start_date_timezone_per_occurrence" value="" >
                        
                        @if ($errors->has('start_date2'))
                        <span class="help-block">
                            <strong style="color:red;">Start date field is required</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">End date</label>
                    <div class="col-sm-9">
                        <div class='input-group enddtp' id="enddtp">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control " name="end_date2"  value="{{ (old('end_date2'))? old('end_date2') :  $end_date }}" id="end_date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <input type="hidden" name="end_date" id="end_date_timezone" value="" >
                        <input type="hidden" name="end_date_timezone_per_occurrence" id="end_date_timezone_per_occurrence" value="" >
                        @if ($errors->has('end_date2'))
                        <span class="help-block">
                            <strong style="color:red;">End date field is required</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Reminder date</label>
                    <div class="col-sm-9">
                        <div class="input-group reminderdtp" >
                            <input type="text" class="form-control" name="reminder_date" placeholder="M/D/YYYY"  value="{{ $reminder_date}}" id="reminder_date_get">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <!-- <input type="hidden" id="reminder_date_timezone" value="" > -->
                        <input type="hidden"  id="reminder_date" value="0000-00-00" >
                        @if ($errors->has('reminder_date'))
                        <span class="help-block">
                            <strong style="color:red;">{{ $errors->first('reminder_date') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                
                <input type="hidden" value="" id="no_occurrence" name="occurrence">
                <input type="hidden" value="" name="total_no_of_occurrence" id="total_no_of_occurrence">
                <!-- Hidden-->
                <div class="row" style="">
                    <div class="col-md-11">
                        <div class="clearfix">
                            
                            <div class="pull-right">
                                @if($action_name != 'Duplicate')
                                <input type="checkbox"  name="cb_recurring"  class="cb_event_recurring" value="0" {{ (old('cb_recurring') != 0)? 'checked': ' '}}  {{ ($recurring >0) ? 'checked="checked" value=""' : ' '}} {{(isset($id->id) > 0)?'checked="unchecked"' :' '}}>
                                @else
                                <input type="hidden"  name="cb_recurring"  class="cb_event_recurring" value="0" >
                                @endif
                                <input type="hidden"  name="cb_recurring"  class="cb_event_recurring" value="0"  {{ ($recurring >0) ? 'checked="checked" value=""' : ' '}} >
                                <input type="hidden"    name="recurring"       class="cb_event_recurring" value="0" @if($recurring >0) value= "{{$recurring}}"@endif>
                                <input type="hidden"    name="cb_recurring"       class="cb_event_recurring" value="0" @if($recurring >0) value= "{{$recurring}}"@endif>
                                <input type="hidden"    name="repeat"       class="cb_event_repeat" value="0">
                                @if($action_name != 'Duplicate') Make this as a Recurring Event @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row " id="event-group"style="display:none; text-align: center;">
                    <div class="col-md-11 well">
                        <input style="margin-left: 10%;" type="radio"  name="recurring" id="rb_recurring_1" value="1" checked="checked"{{ ($recurring == 1) ? 'checked="checked"' : ' '}}>Weekly
                        <input style="margin-left: 10%;" type="radio"  name="recurring" id="rb_recurring_2" value="2" {{ ($recurring == 2) ? 'checked="checked"' : ' '}}>Monthly
                        <input style="margin-left: 10%;" type="radio"  name="recurring" id="rb_recurring_3" value="3"{{ ($recurring == 3) ? 'checked="checked"' : ' '}}>Yearly
                    </div>
                </div>
                <input type="hidden" name="old_recurring" value="{{ $recurring }}">
                <div class="row " id="event-group-repeat"style="display:none; text-align: center;">
                    <div class="col-md-11 well">
                        <input  type="radio"   name="rb_repeat"     id="rb_repeat_2" value="0" {{ (old('rb_repeat') == 0) ? 'checked': ' '}}  @if($no_of_repetition == 0)checked ="checked"@endif>Repeat Event Until
                        <div class='input-group repeatdtp'>
                            <input disabled type="text" class="form-control" id="date_until"  name="recurring_end_date" value="{{ (old('recurring_end_date'))? old('recurring_end_date') :  $recurring_end_date }}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-11 well">
                        <input  type="radio"   name="rb_repeat"     id="rb_repeat_1" value="1" {{ (old('rb_repeat') == 1) ? 'checked': ' '}} @if($no_of_repetition >0)checked ="checked"@endif>No. of Repetitions
                        <input  class="form-control" type="number"  name="no_of_repetition"    id="repetition_no" value="{{ (old('no_of_repetition'))? old('no_of_repetition') :  $no_of_repetition }}">
                    </div>
                </div>
                <div class="col-sm-11">
                </div>
                {{--<div class="form-group">
                    <label class="col-sm-2 control-label">No. of Volunteers</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" name="volunteer_number" value="{{$volunteer_number}}">
                    </div>
                </div>--}}
                
                <input type="hidden" name="check_if_recurring" value="{{ $recurring or ''}}">
                <div class="form-group volunteer-panel">
                    <div class="col-md-12 text-left">
                        <label>
                            Add Volunteer Groups
                            {{-- @if(isset($volunteer_group_items))
                                @foreach($volunteer_group_items as $group)
                                {!! $group !!}
                                @endforeach
                                @endif--}}
                            </label>
                            <a id="add-volunteer-group" href="javascript:void(0);" class="btn btn-darkblue btn-right" data-action="{{ route('generate_volunteer_group', isset($event)?$event->id:'0') }}">+</a>
                          {{--    <a id="add-volunteer-group" href="javascript:void(0);" class="btn btn-darkblue btn-right" data-action="{{ isset($event)? route('generate_volunteer_group_id', $event->id) : route('generate_volunteer_group') }}">+</a>   --}}
                        </div>
                    </div>
                    {{-- modal choice confirm changes to all suceeding events or specific event--}}
                    @if($recurring== 0)
                        <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="small-modal">
                            <div class="modal-dialog modal-lg modal-md hidden-xs" role="document">

                                <div class="modal-content" style="text-align: center;">
                                    <br><br>
                                    <label>Choose which event dates you want to update: </label>
                                    <br><br>
                                    <input type="radio" name="edit_as" value="{{isset($name)?'1':'0'}}" checked="checked">This event only
                                    <br><br>
                                    <br>
                                    <button type="submit" id="event-submit" class="btn btn-darkblue">
                                        Submit
                                    </button>
                                    <br><br>
                                </div>
                            </div>
                        </div>
                    @else
                         @if($action_name != 'Duplicate')
                            <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="small-modal">
                            <div class="modal-dialog modal-lg modal-md hidden-xs" role="document">
                                <div class="modal-content" style="text-align: center;">
                                    <br><br>
                                    <label>Choose which event dates you want to update:</label>
                                    <br><br>
                                    <input type="radio" name="edit_as" value="1" checked="checked">This event only
                                    <br>
                                    <input type="radio" name="edit_as" value="2">This and future events in series
                                    <br><br>
                                    <cite>Changes on fee will apply to all events</cite>
                                    <br><br>
                                    <button type="submit" id="event-submit" class="btn btn-darkblue">
                                        Submit
                                    </button>
                                    <br><br>
                                </div>
                            </div>
                        </div>
                        @else
                            <div class="modal fade bs-example-modal-sm check_if_empty" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="small-modal">
                            <div class="modal-dialog modal-lg modal-md hidden-xs" role="document">
                                <div class="modal-content" style="text-align: center;">
                                    <br><br>
                                    <label>Choose which event dates you want to duplicate:</label>
                                    <br><br>
                                    <input type="radio" name="edit_as" value="1" checked="checked">This event only
                                    <br>
                                    <button type="submit" id="event-submit" class="btn btn-darkblue">
                                        Submit
                                    </button>
                                    <br><br>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                    <div class="btn-right">
                        {!! csrf_field() !!}


                        @if($id >0 && $recurring > 0)
                        <a href="" class="btn btn-darkblue float-right check_if_empty" data-toggle="modal" data-target=".bs-example-modal-sm">Submit</a>
                        @else
                        <button type="submit" id="event-submit" class="btn btn-darkblue float-right check_if_empty">
                             Submit
                        </button>
                        @endif
                        <a style="margin-right:20px;"href="{{ url('/organization/'.$slug.'/administrator/events')}}" class="btn btn-red float-right">
                            Cancel
                        </a>
                        <br><br>
                    </div>
                </form>
                <div class="modal fade" id="myModal"tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Warning Message</h4>
                            </div>
                            <div class="modal-body">
                                <p>Please select correct Date based on Start Date of Event!</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModalStart"tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Warning Message</h4>
                            </div>
                            <div class="modal-body">
                                <p>Your Start Date is greater than your End Date. Please select another date for End Date!</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    @endsection
    @section('script')
    <script>
    $( document ).ready(function() {
   //Check empty inputs on click submit
    $('.check_if_empty').click(function(){
        validationForEmptyFieldOnClick = function(name){
            if($('input[name="'+ name +'"]').val().length == 0){
                $('input[name="'+ name +'"]').removeClass('empty_field');
                $('input[name="'+ name +'"]').nextAll('.error_message').remove();
                $('input[name="'+ name +'"]').addClass('empty_field');
                $( 'input[name="'+ name +'"].empty_field' ).after( "<cite class='error_message' style='color:red;font-size:10px'>This field is required!</cite>" );
            }else if($('input[name="'+ name +'"]').val().length > 0){
                $('input[name="'+ name +'"]').removeClass('empty_field');
                $('input[name="'+ name +'"]').nextAll('.error_message').remove();
            }
        }
        validationForEmptyFieldOnClick('name');
        validationForEmptyFieldOnClick('description');
        validationForEmptyFieldOnClick('capacity');
        validationForEmptyFieldOnClick('fee');
        validationForEmptyFieldOnClick('start_date2');
        validationForEmptyFieldOnClick('end_date2');

        if($('#rb_repeat_1').is(':checked')){
            validationForEmptyFieldOnClick('no_of_repetition');
        }
        if($('#rb_repeat_2').is(':checked') && $('input[name="cb_recurring"]').val() == '1'){
            validationForEmptyFieldOnClick('recurring_end_date');
        }
         if($('input').hasClass('empty_field')){
            $('html, body').animate({
                scrollTop: $(".empty_field").first().offset().top-80
            }, 1000);
            return false;
        }
    });
    //check empty input on input
    validationForEmptyField = function(any){
       $('input[name="'+ any +'"]').on('input', function(){
            var length = $(this).val().length;
            if(length == 0){
                $(this).addClass('empty_field');
                $('input[name="'+ any +'"].empty_field' ).after( "<cite class='error_message' style='color:red;font-size:10px'>This field is required!</cite>" );
            }else{
                $(this).removeClass('empty_field');
                $(this).nextAll('.error_message').remove();
            }
        }); 
    }

    validationForEmptyField('name');
    validationForEmptyField('description');
    validationForEmptyField('capacity');
    validationForEmptyField('fee');
    validationForEmptyField('start_date2');
    validationForEmptyField('end_date2');
    validationForEmptyField('no_of_repetition');
    validationForEmptyField('recurring_end_date');

    $('#rb_repeat_1').on('click',function(){
        $('input[name="recurring_end_date"]').removeClass('empty_field');
        $('input[name="recurring_end_date"]').nextAll('.error_message').remove();
    });
    $('#rb_repeat_2').on('click',function(){
        $('input[name="no_of_repetition"]').removeClass('empty_field');
        $('input[name="no_of_repetition"]').nextAll('.error_message').remove();
    });

    $('input[name="cb_recurring"]').click(function(){
        if($(this).val() ==0){
            var checkbox_recurring = 1;
        }else if($(this).val() == 1){
            var checkbox_recurring = 0;
        }
        if(checkbox_recurring == 0){
            $('input[name="no_of_repetition"]').removeClass('empty_field');
            $('input[name="no_of_repetition"]').nextAll('.error_message').remove();
            $('input[name="recurring_end_date"]').removeClass('empty_field');
            $('input[name="recurring_end_date"]').nextAll('.error_message').remove();
        }
    });

    var token = "{{ csrf_token() }}";
    var slug = "{{ $slug }}";
    var volunteer_group_count = 0;
    var start_date_event = $('#start_date').val();
    var volunteer_groups = JSON.parse('{!! $volunteer_groups_json !!}');
    addVG = function(type,note,status,volunteers_needed,start_date,end_date,id,no_of_occurrence){
        var object = {
            _token:"df25456y4",
            count: volunteer_group_count,
            type: type,
            note: note,
            status: status,
            volunteers_needed: volunteers_needed,
            start_date: start_date,
            end_date: end_date,
            id:id,
            no_of_occurrence:no_of_occurrence 
        };
        $.post($("#add-volunteer-group").data("action"),object).done(function(data){
            $("#add-volunteer-group").before(data);
            $volunteer_group_type = $("#volunteer-group-"+volunteer_group_count+"-type");
            $volunteer_group_type.closest("div.volunteer-group-item").show("fast", function(){
                $(window).scrollTop($volunteer_group_type.offset().top);
                $volunteer_group_type.focus();
                // $("form#event-form").validate();
            });
        });
        volunteer_group_count++;
    };
    $(function() {
        $('.s_date').on('change', function() {
            var value = $(this).val();
            $('.e_date').val(value);
        });
    });
$(document).ready(function(){
    if({{$id}}==0){
        var CurrentDate = new Date();    
        $("#start_date").on("input change", function(){
            //alert( $("#start_date").val());
            CurrentDate = new Date($('#start_date').val());
            if($('#start_date').val() == ''){
                CurrentDate = new Date();
            }
            var _month  =  CurrentDate.getMonth()+5;           
            var _day    = CurrentDate.getDate();
            var _year   = CurrentDate.getFullYear();
            //end date
            var e_month  =  CurrentDate.getMonth()+1;           
            var e_day    = CurrentDate.getDate();
            var e_year   = CurrentDate.getFullYear();
            var e_hour   = CurrentDate.getHours()+1;
            var e_minute   = CurrentDate.getMinutes();
            var e_date_A = '';
            var ee_hour =e_hour+1;
            if(e_hour == 0 ||ee_hour == 0){
                e_hour = '12';
                ee_hour = '12';
            }else{
                e_hour  = e_hour;
                ee_hour  = ee_hour;
            }
            if(e_hour > 12 || ee_hour >12){
                e_date_A = 'PM';
                e_hour = (e_hour - 12);
                ee_hour = (ee_hour - 12);
                if(e_hour == 12 || ee_hour == 12){
                    e_hour = '00';
                    ee_hour = '00';
                    e_date_A = 'AM';
                }
            }else if(e_hour < 12 || ee_hour < 12){
                e_date_A = 'AM';
            }else if(e_hour == 12 || ee_hour == 12){
                e_date_A = 'PM';
            }else if(e_hour == 0 || ee_hour == 0){
                e_hour = '00';
                ee_hour = '00';
            }
            if(e_minute < 10){
                e_minute = "0" + e_minute;
            }
            if(e_hour == 0){
                e_hour = '12';
                ee_hour = '12';
            }
            if(_month > 12){
                var m = (_month - 12);
                _month = m ;
                _year   = CurrentDate.getFullYear()+1;
            }
            if($('#date_until').val() != ''){
                $('#date_until').val(_month+'/'+_day+'/'+_year);
            }
            if($('#start_date').val() != ''){
                $('#date_until').attr('value',_month+'/'+_day+'/'+_year );
                $('#date_until').val(_month+'/'+_day+'/'+_year );
                       //alert(_month+'/'+_day+'/'+_year +' ' + ee_hour + ':' + e_minute + ' ' + e_date_A);
            }                    

        }); 
        if({{$id}}==0){
            $('span.input-group-addon , span.glyphicon-calendar').on("change", function (){
                //alert({{$id}});
                CurrentDate = new Date($('#start_date').val());
                if($('#start_date').val() == ''){
                    CurrentDate = new Date();
                }
                var _month  =  CurrentDate.getMonth()+5;           
                var _day    = CurrentDate.getDate();
                var _year   = CurrentDate.getFullYear();
                if(_month > 12){
                    var m = (_month - 12);
                    _month = m ;
                    _year   = CurrentDate.getFullYear()+1;
                }
                if($('#date_until').val() == 'NaN/NaN/NaN'){
                    $('#date_until').val(' ');
                }else{
                    $('#date_until').attr('value',_month+'/'+_day+'/'+_year );
                    $('#date_until').val(_month+'/'+_day+'/'+_year );
                }
            });          
            var _month  =  CurrentDate.getMonth()+5;           
            var _day    = CurrentDate.getDate();
            var _year   = CurrentDate.getFullYear();
            if(_month > 12){
                var m = (_month - 12);
                _month = m ;
                _year   = CurrentDate.getFullYear()+1;
            }
        }

    }
     var split_hash = (location.href.slice(location.href.lastIndexOf('#'))).substring(1);
    var arr = split_hash.split('/');
    $('.hash').val(arr[0]);
    $('.s_date').on('change', function() {
        var value = $(this).val();
        $('.e_date').val(value);
    });
    // $("form#event-form").validate();
    volunteer_groups.forEach(function(item,index){
        addVG(item.type,item.note,item.status,item.volunteers_needed,item.start_date,item.end_date,item.id,item.no_of_occurrence );
    });
    // $("form#event-form").validate();

    $("#add-volunteer-group").click(function(){
        addVG("","","","");
    });
        // deleteVolunteerGroup = function(e){
        //     var volunteer = new Object();
        //     volunteer.count = $("#"+ e.id).data('count');
        //     $("#"+e.id).closest("div.per-volunteer-group-item").hide("fast", function(){
        //         $('#status_'+volunteer.count).val('InActive');
        //         $(this).remove();
        //     });
        // };
        $('.delete-volunteer-group').hide();
        // $('').click(function(){
        //     $('.delete-volunteer-group').toggle('fast');
        // });
        deleteVolunteerGroup = function(e){
            var volunteer = new Object();
            volunteer.id = $("#"+ e.id).data('id');
            volunteer.status = $("#"+ e.id).data('status');
            volunteer.count = $("#"+ e.id).data('count');
            // $("#"+e.id).closest("div.volunteer-group-item").hide("fast", function(){
                // $(this).remove();
                var url = '{{ route("change_volunteer_group_status")}}';
                var status = volunteer.status;
                $.post(url,{id:volunteer.id,status:status}).done(function(){
                    $('#status_'+volunteer.count).attr('value','InActive');
                    //$("#"+e.id).closest("div.volunteer-group-item").hide();
                    $('#volunteer-group-item-'+volunteer.id).hide('fast');
                    $('#volunteer-group-item-'+volunteer.id+' .empty_field').removeClass('empty_field');
                    $('#volunteer-group-item-'+volunteer.id).nextAll('.error_message').remove();
                    $('#delete_modal_'+volunteer.id).modal('hide');
                    $('#volunteer-group-row_'+volunteer.count).css('display','block');
                    // $('#deleted_id_'+volunteer.id).val(volunteer.id);
                });
            // });
        };
        cancelVolunteerGroup = function(e){
            var volunteer = new Object();
            volunteer.id = $("#"+ e.id).data('id');
            $("#"+e.id).closest("div.volunteer-group-item").hide("fast", function(){
                // $(this).remove();
                $('#volunteer-group-item-'+volunteer.id+' .empty_field').removeClass('empty_field');
                $('#volunteer-group-item-'+volunteer.id).nextAll('.error_message').remove();
            });
        };
        undoVolunteerGroup = function(e){
            var volunteer = new Object();
            volunteer.id = $("#"+ e.id).data('id');
            volunteer.status = $("#"+ e.id).data('status');
            volunteer.count = $("#"+ e.id).data('count');
            
            var url = '{{ route("change_volunteer_group_status")}}';
            var status = volunteer.status;
            $.post(url,{id:volunteer.id,status:status}).done(function(){
                // $("#"+e.id).closest("div.volunteer-group-item").show("fast", function(){
                // $(this).add();
                // });
            $('#status_'+volunteer.count).attr('value','Active');
            $('#volunteer-group-row_'+volunteer.count).css('display','none');
            $('#volunteer-group-item-'+volunteer.id).show('fast');
            });
        };
        //onload checking if event is recurring
        // $('input[name="recurring"]').val(0);
        if($('.cb_event_recurring'). prop("checked") == true){
            $('#event-group').fadeIn("slow");
            $('#event-group-repeat').fadeIn("slow");
            $('.cb_event_recurring').val('1');
            $('.cb_event_repeat').val('1');
            //$('#repetition_no').val('0')
        } else {
            $('#total_no_of_occurrence').val(0);
            $('#event-group').fadeOut("slow");
            $('#event-group-repeat').fadeOut("slow");
            $('.cb_event_recurring').val('0');
            $('.cb_event_repeat').val('0');
        }

        if($('#rb_repeat_1').prop("checked") == true){
            $('.cb_event_repeat').val('1');
            document.getElementById('date_until').disabled = true;
            document.getElementById('repetition_no').disabled = false;
        }
        if($('#rb_repeat_2').prop("checked") == true){
            $('.cb_event_repeat').val('0');
            document.getElementById('date_until').disabled = false;
            document.getElementById('repetition_no').disabled = true;
        }
        //click event
        $('#rb_repeat_1').click(function(){
            $('.cb_event_repeat').val('1');
            //$('#date_until').val('');      //remove value
            document.getElementById('date_until').disabled = true;
            document.getElementById('repetition_no').disabled = false;
        });
        $('#rb_repeat_2').click(function(){
            $('.cb_event_repeat').val('0');
            //$('#repetition_no').val('');       //remove value
            document.getElementById('repetition_no').disabled = true;
            document.getElementById('date_until').disabled = false;
        });

        var startDate;

        timeZone = function(){
            $('.dataTz').each(function(){
                var currentElementDate = moment.utc($(this).data('date'));
                var offset = new Date().getTimezoneOffset();
                $(this).text(moment(currentElementDate).utcOffset(offset * -1));
            });
        }

        autoEndDate = function(){
            var a = new Date($('#end_date').val());
            $('#end_date').val();
            var b = moment.utc(a).format();
            $('#end_date_timezone').val(b);
        }
        startDatePopUp = function(){
            var now =moment().millisecond(0).second(0).minute(0).hour(0);
            var start = new Date($('#start_date').val());
            var end = new Date($('#end_date').val());
            if( start >= end){
                $('#myModalStart').modal('show');
                $('#end_date').val(' ');
            }
        }
        startDate = function(){
            $("div").on("change keyup paste autocompletechange mouseenter keydown input", function(){
               // alert($("#start_date").val());
               if($('#start_date').val() == ''){
                    $('#start_date_timezone').val('');
               }else{
                    var a = new Date($('#start_date').val());
                    $('#start_date').attr('value',$('#start_date').val());
                    var b = moment.utc(a).format();
                    $('#start_date_timezone').val(b);
                    startDatePopUp();
               } 
            });
            $('#startdtp').on("dp.change", function () {
                var a = new Date($('#start_date').val());
                $('#start_date').attr('value',$('#start_date').val());
                var b = moment.utc(a).format();
                if($('#start_date').val() == ''){
                    $('#start_date_timezone').val('');
                }else{
                    $('#start_date_timezone').val(b);
                }       
                 startDatePopUp();
                $('input[name="start_date2"]').removeClass('empty_field');
                $('input[name="start_date2"]').nextAll('.error_message').remove();
            });

               // var CurrentDate =  new Date($('#start_date').val());
               // var _month  =  CurrentDate.getMonth()+5;           
               // var _day    = CurrentDate.getDate();
               // var _year   = CurrentDate.getFullYear();
               //  if(_month > 12){
               //      var m = (_month - 12);
               //      _month = m ;
               //      _year   = CurrentDate.getFullYear()+1;
               //  }
               //  $('#date_until').val(_month+'/'+_day+'/'+_year );
        }

        endDate = function(){
            $("div").on("change keyup paste autocompletechange mouseenter keydown input", function(){
                if($('#end_date').val() == ''){
                    $('#end_date_timezone').val('');
                }else{
                    var a = new Date($('#end_date').val());
                    $('#end_date').attr('value',$('#end_date').val());
                    var b = moment.utc(a).format();
                    $('#end_date_timezone').val(b);
                }
            });
            $('#enddtp').on("dp.change", function () {
               var a = new Date($('#end_date').val());
               $('#end_date').attr('value',$('#end_date').val());
                var b = moment.utc(a).format();
                if($('#end_date').val() == ''){
                    $('#end_date_timezone').val('');
                }else{
                    $('#end_date_timezone').val(b);
                }
                $('input[name="end_date2"]').removeClass('empty_field');
                $('input[name="end_date2"]').nextAll('.error_message').remove();
            });
        }

        reminderDate = function(){
            var a = new Date($('#reminder_date').val());
            var b = moment.utc(a).format();
            $('#reminder_date_timezone').val(b);
        }
        $('#end_date').keypress(function() {
            endDate();
        });
        
        var now =moment().millisecond(0).second(0).minute(0).hour(0);
        startDate();
        endDate();

        $('#reminderdtp').on('click', function(){
            reminderDate();
            var now =moment().millisecond(0).second(0).minute(0).hour(0);
            var s = new Date($('#start_date').val());
            $('.reminderdtp').data("DateTimePicker").maxDate(s);
           $('.reminderdtp').data("DateTimePicker").minDate(now);
            $('#reminderdtp').on("dp.change", function () {
                reminderDate();
            });
        });
        if({{$id}}==0){
            //alert('second');
            $('span.input-group-addon , span.glyphicon-calendar').on('click', function (){
                var now =moment().millisecond(0).second(0).minute(0).hour(0);
                $('#startdtp').data("DateTimePicker").minDate(now);
            });
        }
        var start_date_id = moment.utc($('#start_date_id').data('date'));
        var end_date_id = moment.utc($('#end_date_id').data('date'));
        var reminder_date_id = moment.utc($('#reminder_date_id').data('date'));
        var offset = new Date().getTimezoneOffset();
        var timezone = new Date().toString().match(/([A-Z]+[\+-][0-9]+)/)[1];
        $('#timezone').val(timezone);
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

        if($('#start_date').val() != ''){
             var split_hash = (location.href.slice(location.href.lastIndexOf('#'))).substring(1);
            var arr = split_hash.split('/');
            var hash_date = moment(arr[0],'YYYY-MM-DD');
            if(split_hash != ''){
                $('#start_date').val((hash_date.format('M'))+'/'+hash_date.format('D')+'/'+hash_date.format('YYYY')+' ' + start_date_hour + ':' + start_date_minutes + ' ' + start_date_A);
                $('#start_date').attr('value', $('#start_date').val());
            }
            var a = new Date($('#start_date').val());
            var b = moment.utc(a).format();
            $('#start_date_timezone').val(b);
            $('#start_date_timezone_per_occurrence').val(b);

            $('#reminder_date').val((hash_date.format('M'))+'/'+hash_date.format('D')+'/'+hash_date.format('YYYY'));
            if($('#repetition_no').val() != 0){
                 $('#repetition_no').val($('#repetition_no').val()- arr[2]+1);
            }
            //get No. of Occurrence
            var original_start_date= moment.utc('{{ $start_date_parse or  ''}}').format();   
            console.log('original_start_date: '+original_start_date);
            //new Date('{{ $start_date_parse or  ''}}');
            var hash_current_start_date = $('#start_date').val();
            var current_start_date = moment.utc(hash_current_start_date).format();   
            console.log('current_start_date: '+current_start_date);
            //new Date(hash_current_start_date);
            var recurring_end_date = moment.utc('{{ $recurring_end_date_parse or '' }}').format();    
            console.log('recurring_end_date: '+recurring_end_date);
            //new Date('{{ $recurring_end_date_parse or '' }}');
            var no_of_repetition_date = moment.utc('{{ $no_of_repetition_date or ''}}').format();    
            console.log('no_of_repetition_date: '+no_of_repetition_date);
            //new Date('{{ $no_of_repetition_date or ''}}');
            var no_of_repetition     ='{{ $event->no_of_repetition or ''}}';
            var total_no_of_occurrence = '{{ $total_no_of_occurrence or '' }}';
            if(no_of_repetition == 0){
                var total_current_days = (recurring_end_date- current_start_date) / (1000 * 60 * 60 * 24);
            }else{
                var total_current_days = (no_of_repetition_date- current_start_date) / (1000 * 60 * 60 * 24);
            }
            var recurring = '{{ $event->recurring or '' }}';
            if(recurring == 2){
               var no_of_month_diff = (Math.floor(total_current_days)) / 31;
                var diff = Math.floor(no_of_month_diff);
                var no_of_occurrence = total_no_of_occurrence - diff; 
                $('#no_occurrence').val(no_of_occurrence);
            }else if(recurring == 1){
                var no_of_week_diff = (Math.floor(total_current_days)) / 7;
                var diff = Math.floor(no_of_week_diff);
                var no_of_occurrence = total_no_of_occurrence - diff; 
                $('#no_occurrence').val(no_of_occurrence);
            }
            else if(recurring == 3){
                var no_of_year_diff = (Math.floor(total_current_days)) / 365;
                var diff = Math.floor(no_of_year_diff);
                var no_of_occurrence = total_no_of_occurrence - diff; 
                $('#no_occurrence').val(no_of_occurrence);
            }else{
                $('#no_occurrence').val(0);
                $('#total_no_of_occurrence').val(0);
            }
            // console.log('original_start_date--'+ original_start_date, 'current_start_date--'+current_start_date, 'recurring_end_date--'+recurring_end_date,'total_no_of_occurrence---' +total_no_of_occurrence, 'no_of_occurrence---' +no_of_occurrence+ 'no_of_week_diff ' +no_of_week_diff + ' '+diff+ ' total_current_days '+ total_current_days);


        }
        var check_id = '{{ $id }}';
        if(check_id == 0){
            $('#no_occurrence').val(0);
        }
        //$('#no_occurrence').val(0);
        //end date
        var converted_end_date = new Date(end_date_id.utcOffset(offset * -1));
        var end_date_hour = converted_end_date.getHours();
        var end_date_minutes = converted_end_date.getMinutes();
        // var end_date_result  = end_date_hour;
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
            // end_date_result = ((end_date_hour < 10) ? end_date_hour : end_date_hour);
            end_date_A = 'AM';
        }else if(end_date_hour == 12){
            end_date_A = 'PM';
        }
        if(end_date_minutes < 10){
            end_date_minutes = "0" + end_date_minutes;
        }
        if(end_date_hour == 0){
            end_date_hour = '12';
        }
        if($('#end_date').val() != ''){
            var split_hash = (location.href.slice(location.href.lastIndexOf('#'))).substring(1);
            var arr = split_hash.split('/');
            var hash_date = moment(arr[1],'YYYY-MM-DD');
            if(split_hash != ''){
                $('#end_date').val((hash_date.format('M'))+'/'+hash_date.format('D')+'/'+hash_date.format('YYYY')+' ' + end_date_hour + ':' + end_date_minutes + ' ' + end_date_A);
            }
            var a = new Date($('#end_date').val());
            var b = moment.utc(a).format();
            $('#end_date_timezone').val(b);
            $('#end_date_timezone_per_occurrence').val(b);
        }
        $('#span').hide();
        // else{
        //     $('input[name="recurring"]').val(1);
        // }
        //checkbox show hide of recurring options
        $('.cb_event_recurring').click(function(){
            if($(this).prop("checked") == true){
                $('#event-group').fadeIn("slow");
                $('#event-group-repeat').fadeIn("slow");
                $('.cb_event_recurring').val('1');
                $('.cb_event_repeat').val('0');
                $('#repetition_no').val('0');
                $('#rb_recurring_1').prop("checked", true);
              //  $('.volunteer-panel').hide();//for temporary only, hiding for recurring eventv
            } else {
                $('#event-group').fadeOut("slow");
                $('#event-group-repeat').fadeOut("slow");
                $('.cb_event_recurring').val('0');
                $('.cb_event_repeat').val('1');
                $('#date_until').val('');
                $('#repetition_no').val('0');
                $('input[name="recurring"]').attr('checked',false);
                $('input[name="repeat"]').val('0');
               // $('.volunteer-panel').show();//for temporary only, hiding for recurring event
            }
            $('#rb_recurring_1').click(function(){
                $('.cb_event_recurring').val('1');
            });
            $('#rb_recurring_2').click(function(){
                $('.cb_event_recurring').val('1');
            });
            $('#rb_recurring_3').click(function(){
                $('.cb_event_recurring').val('1');
            });
            $('#rb_repeat_1').click(function(){
                $('.cb_event_repeat').val('1');
                document.getElementById('date_until').disabled = true;
                document.getElementById('repetition_no').disabled = false;
            });
            $('#rb_repeat_2').click(function(){
                $('.cb_event_repeat').val('0');
                document.getElementById('repetition_no').disabled = true;
                document.getElementById('date_until').disabled = false;
            })
        });


    });

//// Get Total no of Occurrence 
    var recurring_value = $("input[name='recurring']:checked").val();
    noOfRepetition = function(){
        if($('#repetition_no').val() > 0){
                $('#total_no_of_occurrence').val(parseInt($('#repetition_no').val()) - 1);
            }else{
                $('#total_no_of_occurrence').val(0);
            }
        }
        $('#repetition_no').on('input', function(){
             noOfRepetition();
        });
        $("#rb_repeat_1").click(function(){
            noOfRepetition();
        });

        //on load
        var rb_repeat = '{{ $rb_repeat or ''}}';
        var no_of_repetition = '{{ $no_of_repetition or ''}}';
        if(rb_repeat != null){
            var hash_current_start_date = $('#start_date').val();
            var current_start_date = new Date(hash_current_start_date);
            var date_until         = $('#date_until').val();
            var recurring_end_date =new Date(date_until);

            var total = recurring_end_date.getMonth() - current_start_date.getMonth() + (12 * (recurring_end_date.getFullYear() - current_start_date.getFullYear()));
            $('#total_no_of_occurrence').val(total); 
        }
        if($('#repetition_no').val() > 0){
           if($('#repetition_no').val() > 0){
                $('#total_no_of_occurrence').val(parseInt($('#repetition_no').val()) - 1);
            }else{
                $('#total_no_of_occurrence').val(0);
            }  
        }
        if($('#repetition_no').val() == ''){
            $('#total_no_of_occurrence').val(0);
        }
        
    //weekly recurring
    weekly = function(){
        CurrentDate = new Date($('#start_date').val());
           if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
        var _month  =  CurrentDate.getMonth()+5;           
        var _day    = CurrentDate.getDate();
        var _year   = CurrentDate.getFullYear();
        if(_month > 12){
            var m = (_month - 12);
            _month = m ;
            _year   = CurrentDate.getFullYear()+1;
        }
        if($('#date_until').val() == 'NaN/NaN/NaN'){
            $('#date_until').val(' ');
        }else{
            if({{$id}} == 0){
                $('#date_until').val(_month+'/'+_day+'/'+_year );
                $('#date_until').attr('value', _month+'/'+_day+'/'+_year );
            }
            

            var r =$('#date_until').attr('value');
            var recurring_end_date =  new Date(r);
            var week = 1000 * 60 * 60 * 24 * 7;
            var diff = Math.abs(CurrentDate.getTime() - recurring_end_date.getTime());
            var total = Math.floor(diff / week);
            $('#total_no_of_occurrence').val(total);
        }
    }
    $("#rb_recurring_1").click(function () {
        if($('#rb_repeat_2').is(':checked')){
            weekly() ;
        }
    });
    
    if($('#rb_repeat_2').is(':checked') && recurring_value == 1){
         weekly();
     }
    //monthly recurring
    monthly = function(){
        var CurrentDate = new Date();
        CurrentDate = new Date($('#start_date').val());
        if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
        var _month  =  CurrentDate.getMonth()+10;           
        var _day    = CurrentDate.getDate();
        var _year   = CurrentDate.getFullYear();
        if(_month > 12){
            var m = (_month - 12);
            _month = m ;
            _year   = CurrentDate.getFullYear()+1;

        }
        if({{$id}} == 0){
                $('#date_until').val(_month+'/'+_day+'/'+_year );
                $('#date_until').attr('value', _month+'/'+_day+'/'+_year );
            }
        
       var r =$('#date_until').attr('value');
       var recurring_end_date =  new Date(r);

       var total = recurring_end_date.getMonth() - CurrentDate.getMonth() + (12 * (recurring_end_date.getFullYear() - CurrentDate.getFullYear()));
       $('#total_no_of_occurrence').val(total);
    }
    $("#rb_recurring_2").click(function () {
        if($('#rb_repeat_2').is(':checked')){
            monthly();
        }
    });
    if($('#rb_repeat_2').is(':checked') && recurring_value == 2){
         monthly();
     }
    //yearly recurring
    yearly = function(){
        var CurrentDate = new Date();
        CurrentDate = new Date($('#start_date').val());
        if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
       var _month  = CurrentDate.getMonth();
       var _day    = CurrentDate.getDate();
       var _year   = CurrentDate.getFullYear() + 5;
       if({{$id}} == 0){
                $('#date_until').val(_month+'/'+_day+'/'+_year );
                $('#date_until').attr('value', _month+'/'+_day+'/'+_year );
            }
       var r =$('#date_until').attr('value');
       var recurring_end_date =  new Date(r);
       var year = 1000 * 60 * 60 * 24 * 365.25;
       var diff = Math.abs(CurrentDate.getTime() - recurring_end_date.getTime());
       var total =  Math.floor(diff / year);
       $('#total_no_of_occurrence').val(total);
       
    }
    $("#rb_recurring_3").click(function () {
        if($('#rb_repeat_2').is(':checked')){
            yearly();
        }
   });
    if($('#rb_repeat_2').is(':checked') && recurring_value == 3){
         yearly();
     }
    $('#startdtp').on("dp.change", function (){
        var recurring_value = $("input[name='recurring']:checked").val();
        var cb_recurring = $("input[name='cb_recurring']:checked").val();
        if(recurring_value == 3 && cb_recurring >= 0){
            yearly();
        }else if(recurring_value == 2 && cb_recurring >= 0){
            monthly();
        }else if(recurring_value == 1 && cb_recurring >= 0){
            weekly();
        }
    });
    $('.cb_event_recurring').click(function(){

        var cb_recurring = $("input[name='cb_recurring']:checked").val();
        var recurring_value = $("input[name='recurring']:checked").val();
        // alert(cb_recurring);
        if(cb_recurring == 0 && $('#rb_repeat_2').is(':checked')){
           if(recurring_value == 3){
            yearly();
            }else if(recurring_value == 2){
                monthly();
            }else if(recurring_value == 1){
                weekly();
            } 
        }else{
           $('#total_no_of_occurrence').val(0); 
        }
    });
    $("#rb_repeat_2").click(function(){
        var recurring_value = $("input[name='recurring']:checked").val();
        if(recurring_value == 3){
            yearly();
            }else if(recurring_value == 2){
                monthly();
            }else if(recurring_value == 1){
                weekly();
            } 
    });

    yearlyModified = function(){
        var CurrentDate = new Date();
        CurrentDate = new Date($('#start_date').val());
        if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
        var r =$('#date_until').val();
        var recurring_end_date =  new Date(r);
        var year = 1000 * 60 * 60 * 24 * 365.25;
        var diff = Math.abs(CurrentDate.getTime() - recurring_end_date.getTime());
        var total =  Math.floor(diff / year);
        $('#total_no_of_occurrence').val(total);
    }
    monthlyModified = function(){
        var CurrentDate = new Date();
        CurrentDate = new Date($('#start_date').val());
        if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
       var r =$('#date_until').val();
       var recurring_end_date =  new Date(r);
       var total = recurring_end_date.getMonth() - CurrentDate.getMonth() + (12 * (recurring_end_date.getFullYear() - CurrentDate.getFullYear()));
       $('#total_no_of_occurrence').val(total);
    }
    weeklyModified = function(){
        CurrentDate = new Date($('#start_date').val());
           if($('#start_date').val() == ''){
            CurrentDate = new Date();
        }
        var r =$('#date_until').val();
        var recurring_end_date =  new Date(r);
        var week = 1000 * 60 * 60 * 24 * 7;
        var diff = Math.abs(CurrentDate.getTime() - recurring_end_date.getTime());
        var total = Math.floor(diff / week);
        $('#total_no_of_occurrence').val(total);
    }
    $('#date_until').on('input', function(){
    var recurring_value = $("input[name='recurring']:checked").val();
       if(recurring_value == 3){
            yearlyModified();
        }else if(recurring_value == 2){
            monthlyModified();
        }else if(recurring_value == 1){
            weeklyModified();
        } 
    });
    $('.repeatdtp').on('dp.change', function(){
    var recurring_value = $("input[name='recurring']:checked").val();
       if(recurring_value == 3){
            yearlyModified();
        }else if(recurring_value == 2){
            monthlyModified();
        }else if(recurring_value == 1){
            weeklyModified();
        } 
    });
//// Get Total no of Occurrence
});
    </script>
    @endsection
