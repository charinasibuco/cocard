@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Events</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a class="btn btn-darkblue float-right" id="export_events">
                            Export&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            <form method="GET" action="">
                <div class="search form-group">
                    <div class="input-group" style="width: 500px;">
                        <span class="input-group-addon" id="basic-addon1">
                            <i class="fa fa-search"></i>
                        </span>
                        <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="">
                        <span class="input-group-btn">
                            <button class="btn btn-darkblue" type="submit">@lang('dashboard_details.go')</button>
                        </span>
                    </div>
                </div>
            </form>
        <div class="table-responsive">
            <table class="table taNble-striped table-responsive" id="tabledata">
                <thead class="theader">
                    <th>Event</th>
                    <th>Quantity</th>
                    <th>Fee</th>
                    <th>Total Amount</th>
                    <th>Event Start Date</th>
                    <th>Event End Date</th>
                    <th>Recurring</th>
                    <th>Recurring End Date</th>
                    <th>Date of Transaction</th>
                </thead>
                <tbody>
                    <?php
                        $x = 0;
                    ?>
                    @foreach($participants as $participant)
                    
                    <tr>
                        <?php
                            $x++;
                        ?>
                        <td style="display:none;">
                            <input type="text" name="no_of_repetition" id="no_of_repetition" value="{{$participant->no_of_repetition}}">
                            <input type="text" name="recurring" id="recurring" value="{{$participant->event_recurring}}">
                            <input type="text" name="r_start_date" id="r_start_date" value="{{$participant->start_date}}">
                            <input type="text" name="r_end_date" id="r_endt_date" value="{{$participant->end_date}}">
                        </td>
                        <td>{{$participant->event_name}}</td>
                        <td>{{$participant->qty}}</td>
                        <td>${{number_format($participant->Event->fee,2)}}</td>
                        <td>&dollar;{{ number_format(number_format($participant->qty, 2) * number_format($participant->Event->fee, 2), 2) }}</td>
                        <span class="dataTz" id="start_date_{{ $x }}" data-date="{{ $participant->start_date }}" style="display:none"> &nbsp; </span>
                        <td data-id="data_start_{{ $participant->id }}" id="start_date_timezone_{{ $x }}"></td>                        
                        <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $participant->end_date }}" style="display:none"> &nbsp; </span>
                        <td data-id="data_end_{{ $participant->id }}" id="end_date_timezone_{{ $x }}"></td>                        
                        <td>@if($participant->Event->recurring >0 ) R @else  - @endif</td>
                        <td>@if($participant->Event->recurring >0 ){{ $participant->recurringEndDate($participant->start_date,$participant->Event->recurring_end_date,$participant->Event->recurring,$participant->no_of_repetition)  }}@else  - @endif</td>
                        <span class="dataTz" id="created_date_{{ $x }}" data-date="{{ $participant->created_at }}" style="display:none"> &nbsp; </span>
                        <td id="created_date_timezone_{{ $x }}"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $participants->render() }}
        </div>
        @include('cocard-church.user.all_events')
    </div>
</div>
@endsection
@section('script')
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
   
    //display the computed date
    //$('#computed_occurence_date').val(no_of_repetition*recurring);
    //
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
        $(date + 'timezone_'+ i ).text(month3[i] + '/' + day3[i] + '/' + year3[i])// + ' ' + hour3[i] + ':' + minutes3[i] +' ' + A3[i]);
        $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
        $(date + i ).hide();

        }
    }
$("#export_events").click(function(){
     $("#submit_event").trigger('click');
    return false;
    });
var participant_limit = {{ count($participants_all)}};
var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(created_date_var, participant_limit);
convertion(start_date_var, participant_limit);
convertion(end_date_var, participant_limit);
$('.dataTz').css('display', 'none');
</script>
@endsection
