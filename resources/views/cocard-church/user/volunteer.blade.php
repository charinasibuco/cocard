@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-lg-2 col-md-3">
                <h3>Volunteers</h3>
            </div>
            <div class="col-lg-8 col-md-7">
                <h3>
                    <form method="GET" action="">
                        <div class="search form-group">
                            <div class="input-group" style="width:80%;">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" class="search-form form-control" placeholder="Search Volunteer Role" name="search" aria-describedby="basic-addon1" value="{{ $search }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-darkblue" type="submit">@lang('dashboard_details.go')</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </h3>
            </div>
            <div class="col-lg-2 col-md-2">
                <h3>
                    <div class="clearfix">
                        <a class="btn btn-darkblue float-right" id="export_volunteer">
                            Export&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('message') }}
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('error') }}
                </div>
            @endif
            <div class="table-responsive">
            <table class="table table-striped" id="tabledata">
                <thead class="theader">
                    <th>Event</th>
                    <th>Volunteer Role</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    <?php
                        $x = 0;
                    ?>
                    
                    @foreach($volunteers as $volunteer)
                    <?php
                        $x++;
                    ?> 
                        <tr>
                            <td>{{ $volunteer->event_name }}</td>
                            <td>{{ $volunteer->type }}</td>
                            <span class="dataTz" id="start_date_{{ $x }}" data-date="{{$volunteer->start_date}}" style="display:none"> &nbsp; </span>
                            <td id="start_date_timezone_{{ $x }}"></td>
                            <span class="dataTz" id="end_date_{{ $x }}" data-date="{{ $volunteer->end_date }}" style="display:none"> &nbsp; </span>
                            <td id="end_date_timezone_{{ $x }}"></td>
                            <td>{{ $volunteer->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $volunteers->render() }}
            </div>
            @include('cocard-church.user.all_volunteers')
        </div>
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
        $(date + 'input_timezone_'+ i).val($(date + 'timezone_'+ i ).text());
        $(date + i ).hide();

        }
    }
$("#export_volunteer").click(function(){
     $("#submit_volunteer").trigger('click');
    return false;
    });
var volunteers_limit = {{ count($volunteers)}};
// var created_date_var = '#created_date_';
var start_date_var   = '#start_date_';
var end_date_var     = '#end_date_';
// convertion(created_date_var, volunteers_limit);
convertion(start_date_var, volunteers_limit);
convertion(end_date_var, volunteers_limit);
$('.dataTz').css('display', 'none');
</script>
@endsection

