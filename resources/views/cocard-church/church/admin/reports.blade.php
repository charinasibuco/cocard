@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content" id="activity-log">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Reports</h3>
            </div>
        </div>
        <form method="post" id="commentForm" action="{{ url('organization/'.$slug.'/administrator/reports/generate') }}">
            <div class="row">
                <input type="hidden" class="form-control" name="organization" value="{{ $organization->id }}">
                <div class="col-sm-12">
                    <div id="span" style="display:none">
                        <input type="hidden" name="converted_start_date" value="">
                        <input type="hidden" name="converted_end_date" value="">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">Start date</label>
                            </div>
                            <div class="col-sm-9">
                                <div class='input-group startdp'>
                                    <input type="text" placeholder="M/D/YYYY" class="form-control" id="start_date" name="start_date" value="{{$start_date}}"  @if($report != 6)required @endif>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="control-label">End date</label>
                            </div>
                            <div class="col-sm-9">
                                <div class='input-group enddp'>
                                    <input type="text" placeholder="M/D/YYYY" class="form-control "id="end_date" name="end_date"  value="{{$end_date}}" @if($report != 6)required @endif>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $n = 1 ?>
                    <span class="dataTz" id="now_date_{{ $n }}" data-date="{{ Carbon\Carbon::now()}}" style="display:none"> &nbsp; </span>
                    <div id="now_date_timezone_{{ $n }}" style="display:none"></div>
                    <input type="text" value="" name="input_now_date_timezone[]" id="now_date_input_timezone_{{ $n }}" style="display:none">
                </div>

                <div class="col-sm-12" style="margin-top:40px;">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Export as: </label>
                        </div>
                        <div class="col-sm-9">
                            <label class="radio-inline">
                                <input style="" type="radio" name="eformat" value="xls"  {{ ($eformat == 'xls') ? 'checked' : ' '}}  checked> xls
                            </label>
                            <label class="radio-inline">
                                <input style="" type="radio" name="eformat" value="xlsx" {{ ($eformat == 'xlsx') ? 'checked' : ' '}}> xlsx
                            </label>
                            <label class="radio-inline">
                                <input style="" type="radio" name="eformat" value="csv"  {{ ($eformat == 'csv') ? 'checked' : ' '}}> csv
                            </label>
                            <label class="radio-inline">
                                <input style="" type="radio" name="eformat" value="pdf"  {{ ($eformat == 'pdf') ? 'checked' : ' '}}> pdf
                            </label>
                        </div>
                    </div>

                </div>
                <div class="col-sm-12">
                    <br>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Select Data: </label>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group">
                                <select class="form-control report_"  name="report">
                                    <option disabled style="background:#F5F5F5;">DONATION</option>
                                    <option selected value="1" {{ ($report == '1') ? 'selected="selected"' : ' '}} >Donation by Family/Individual</option>
                                    <option value="2" {{ ($report == '2') ? 'selected="selected"' : ' '}} >Donation by Fund</option>
                                    <option disabled style="background:#F5F5F5;">EVENT SIGNUP</option>
                                    <option value="3" {{ ($report == '3') ? 'selected="selected"' : ' '}} >Event Participants</option>
                                    <option disabled style="background:#F5F5F5;">VOLUNTEERS</option>
                                    <option value="4" {{ ($report == '4') ? 'selected="selected"' : ' '}} >Volunteers by Event</option>
                                    <option value="5" {{ ($report == '5') ? 'selected="selected"' : ' '}} >Volunteers by Family</option>
                                    <option disabled style="background:#F5F5F5;">MEMBERS INFORMATION</option>
                                    <option value="6" {{ ($report == '6') ? 'selected="selected"' : ' '}} >List of Members</option>
                                    <option disabled style="background:#F5F5F5;">REPORT SUMMARY</option>
                                    <option value="7" {{ ($report == '7') ? 'selected="selected"' : ' '}} >Summary of Donations by Fund</option>
                                    <option value="10" {{ ($report == '10') ? 'selected="selected"' : ' '}} >Summary of Donations by Category</option>
                                    <option value="8" {{ ($report == '8') ? 'selected="selected"' : ' '}} >Summary of Events</option>
                                    <option value="9" {{ ($report == '9') ? 'selected="selected"' : ' '}} >Summary of Volunteers</option>

                                </select>
                            </div>
                        </div>
                        {!! csrf_field() !!}
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="submit" name="view" class="btn btn-darkblue btn-block" value="View">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="submit" name="generate" class="btn btn-darkblue btn-block" value="Export">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="table-main panel panel-default">
                        <div class="table-responsive">
                            <table width="100%" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        @if($report == 7)
                                        <th>Category</th>
                                        <th>Donation List</th>
                                        <th>Number of Donations</th>
                                        <th>Total Amount Donated</th>
                                        @endif
                                        @if($report == 6)
                                        <th>Name</button></th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Birthdate</th>
                                        <th>Gender</th>
                                        <th>Address</th>
                                        @endif
                                        @if($report == 1)
                                        <th>Name</th>
                                        @endif
                                        @if($report ==3)
                                        <th>Event Date</th>
                                        <th>Recurring</th>
                                        <th>Event Name</th>
                                        <th>Attendee</th>
                                        <th># of Tickets</th>
                                        <th>Total</th>
                                        @endif
                                        @if($report==5)
                                        <th> Event Date</th>
                                        <th> Event Name </th>
                                        <th> Volunteer Group</th>                                        
                                        <th> Name of the Family</th>
                                        @endif
                                        @if($report==4)
                                        <th>Event Date</th>
                                        <th>Event Name</th>
                                        <th>Name of Volunteers</th>
                                        <th>Volunteer Group</th>
                                        @endif
                                        @if($report == 1 || $report == 2)
                                        <th>Date of Donation</th>
                                        <th>Fund to Donate To</th>
                                        <th>Amount Donated</th>
                                        @endif
                                        @if($report == 8)
                                            <th>Start Date</th>
                                            <th>Recurring</th>
                                            <th>Event Name</th>
                                            <th>Event Capacity</th> 
                                            <th># Signed Up</th>
                                                                                      
                                        @endif
                                        @if($report == 9)
                                        <th>Event Date</th>
                                        <th>Event Name</th>
                                        <th>Volunteer Group</th>
                                        <th>No. Volunteers Needed</th>
                                        <th>No. of Volunters Signed up</th>
                                        @endif
                                        @if($report == 10)
                                        <th>Category</th>
                                        <th>Number of Donations</th>
                                        <th>Total Amount Donated</th>
                                        @endif
                                    </tr>                                   
                                </thead>
                                <tbody>
                                   
                                    <?php
                                        $x = 0;
                                    ?>
                                    @foreach($reports as $row)
                                    <?php
                                        $x++;
                                    ?>
                                     @if(count($reports) == 0)
                                    <tr>
                                        <td>No records found.</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        @if($report == 7)
                                            <td>{{$row->donation_category_name}}</td>
                                            <td>{{$row->donation_list_name }}</td>
                                            <td>{{$row->dl_count}}</td>
                                            <td>${{number_format($row->total, 2, '.', ',')}}</td>
                                        @endif
                                        @if($report== 6)
                                        <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                                        <td>{{ $row->email}}</td>
                                        <td>{{ $row->phone}}</td>
                                        <td>{{Carbon\Carbon::parse($row->birthdate)->format('n/j/Y')}}</td>
                                        <td>{{ $row->gender}}</td>
                                        <td>{{ $row->address}} {{ $row->city}} {{ $row->state}}</td>
                                        @endif
                                        @if($report == 1)
                                        <td>{{ $row->first_name}} {{ $row->last_name}}</td>
                                        @endif
                                        @if($report==5 )
                                        <td id="start_date_timezone_{{ $x }}" ></td>
                                        <td>{{ $row->event_name}}</td>
                                        <td>{{ $row->volunteer_group_name}}</td>
                                        <td>{{ $row->family_name}}</td>
                                        <span class="dataTz" id="start_date_{{ $x }}" data-date="{{$row->event_start_date}}" style="display:none"> &nbsp; </span>
                                        
                                        <td style="display:none"><input type="text" value="" name="input_start_date_timezone[]" id="start_date_input_timezone_{{ $x }}"></td>
                                     {{--    <td>{{Carbon\Carbon::parse($row->event_start_date)->format('n/d/Y')}}</td> --}}
                                        @endif
                                        @if($report == 3)
                                        <td>{{Carbon\Carbon::parse($row->start_date)->format('n/j/Y')}}</td>
                                        <td>{{ ($row->recurring == 1)? 'R':''}}</td>
                                        <td>{{ $row->event_name}}</td>
                                        <td>{{ $row->participant_name}}</td>
                                        <td>{{ $row->qty}}</td>
                                        <td>${{ number_format($row->fee * $row->qty,2,'.',',')}}</td>
                                        @endif
                                        @if($report == 4)
                                        <td style="display:none"><input type="text" value="" name="input_start_date_timezone[]" id="start_date_input_timezone_{{ $x }}"></td>
                                        <td id="start_date_timezone_{{ $x }}" ></td>    
                                        <td>{{ $row->event_name}}</td>
                                        <span class="dataTz" id="start_date_{{ $x }}" data-date="{{$row->event_start_date}}" style="display:none"> &nbsp; </span>                                                     
                                        <td>{{ $row->vol_name}}</td>                    
                                        <span class="dataTz" id="end_date_{{ $x }}" data-date="{{$row->event_end_date}}" style="display:none"> &nbsp; </span>
                                        {{--<td id="end_date_timezone_{{ $x }}" ></td>--}}
                                        <td style="display:none"><input type="text" value="" name="input_end_date_timezone[]" id="end_date_input_timezone_{{ $x }}"></td>
                                        <td>{{ $row->volunteer_group_name}}</td>
                                        
                                    {{--     <td>{{ $row->event_start_date}}</td> --}}
                                    {{--     <td>{{ $row->event_end_date}}</td> --}}
                                        @endif
                                        @if($report == 1 || $report == 2)
                                        <td>{{ $row->created_at->format('n/d/Y')}}</td>
                                        <td>{{ $row->dl_name}}</td>
                                        <td>${{ number_format($row->amount,2,'.',',')}}</td>
                                        @endif
                                        @if($report == 8)
                                        <td>{{Carbon\Carbon::parse($row->start_date)->format('n/j/Y')}}</td>
                                        <td>{{ ($row->recurring == 1)? 'R':''}}</td>
                                        <td>{{ $row->event_name}}</td>
                                        <td>{{ $row->capacity}}</td>
                                        <td>{{ $row->pending}}</td>
                                        
                                        @endif
                                         @if($report == 9)
                                        <td>{{Carbon\Carbon::parse($row->start_date)->format('n/j/Y')}}</td>
                                        <td>{{ $row->event_name}}</td>
                                        <td>{{ $row->volunteer_group_name}}</td>
                                        <td>{{ $row->volunteers_needed}}</td>
                                        <td>{{ $row->total}}</td>
                                        @endif
                                    </tr>
                                     @if($report == 10)
                                            <td>{{$row->donation_category_name}}</td>
                                            <td>{{$row->dl_count}}</td>
                                            <td>${{number_format($row->total, 2, '.', ',')}}</td>
                                        @endif
                                    @endforeach
                                     @if($report == 7 || $report == 10)
                                        <tr>
                                            <td><b>TOTAL:</b> </td>
                                            @if($report == 7)
                                                <td> - </td>
                                            @endif
                                            <td> - </td>
                                            <td><b>${{number_format($count_amount, 2, '.', ',')}}</b></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
@section('script')
<script>
$('.report_').on('change', function() {
      if(this.value == 6){//members list
        document.getElementById("start_date").required = false;
        document.getElementById("end_date").required = false;
      }else{
        document.getElementById("start_date").required = true;
        document.getElementById("end_date").required = true;
      }
    });
$( document ).ready(function() {
    $('.report_').on('change', function() {
      if(this.value == 6){//members list
        document.getElementById("start_date").required = false;
        document.getElementById("end_date").required = false;
      }else{
        document.getElementById("start_date").required = true;
        document.getElementById("end_date").required = true;
      }
    });
    var offset = new Date().getTimezoneOffset();
    $('.startdp').on("dp.change", function () {
        var start_date = $('input[name="start_date"]').val();
        var start_date_convert = moment.utc(start_date);
        var converted_start_date = new Date(start_date_convert.utcOffset(offset * -1));

        var month = converted_start_date.getMonth()+1;
        var day   = converted_start_date.getDate();
        var year  = converted_start_date.getFullYear();
        var hour  = converted_start_date.getHours();
        var minutes = converted_start_date.getMinutes();
        if(minutes < 10){
            minutes = "0" + minutes;
        }
        if(hour < 10){
            hour = "0" + hour;
        }
        if(month < 10){
            month = "0" + month;
        }
        if(day < 10){
            day = "0" + day;
        }
        var final_converted_start_date = $('input[name="converted_start_date"]').val(year + '-' + month + '-' + day + ' ' + hour + ':' + minutes + ':00' );
    });
    $('.enddp').on("dp.change", function () {
        var end_date = $('input[name="end_date"]').val();
        var end_date_convert = moment.utc(end_date);
        var converted_end_date  = new Date(end_date_convert.utcOffset(offset * -1));

        var month2 = converted_end_date.getMonth()+1;
        var day2   = converted_end_date.getDate();
        var year2  = converted_end_date.getFullYear();
        var hour2  = converted_end_date.getHours();
        var minutes2 = converted_end_date.getMinutes();
        if(minutes2 < 10){
            minutes2 = "0" + minutes2;
        }
        if(hour2 < 10){
            hour2 = "0" + hour2;
        }
        if(month2 < 10){
            month2 = "0" + month2;
        }
        if(day2 < 10){
            day2 = "0" + day2;
        }
        var final_converted_start_date = $('input[name="converted_end_date"]').val(year2 + '-' + month2 + '-' + day2 + ' ' + hour2 + ':' + minutes2 + ':00' );
    });
});
</script>
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
$("#export_volunteer").click(function(){
     $("#submit_volunteer").trigger('click');
    return false;
    });
var limit = {{ count($reports)}};
var now_date_var = '#now_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
convertion(now_date_var, 1);
convertion(start_date_var, limit);
convertion(end_date_var, limit);
$('.dataTz').css('display', 'none');

</script>
@endsection
