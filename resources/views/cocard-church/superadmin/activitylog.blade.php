@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content" id="activity-log">
    <div class="margin-mob-top">
        <div class="sub-table">
            <h3 class="permissiontitle">Activity Log</h3>
            <div class="row">
                <div class="col-sm-12">
                <div class="table-responsive">
                    <table width="100%" class="table table-striped table-hover page-table">
                        <thead>
                            <tr>
                                <th><a href="">Updated By</a></th>
                                <th><a href="">Activity</a></th>
                                <th><a href="">Details</a></th>
                                <th><a href="">Date</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $x = 0;
                            ?>
                            @foreach($activity_log as $row)
                            <tr>
                                <?php
                                    $x++;
                                ?>
                                <td>{{ $row->user->first_name }} {{ $row->user->last_name }}</td>
                                <td>{{ $row->activity  }}</td>
                                <td>{{ $row->details  }}</td>
                                <span class="dataTz" id="log_date_{{ $x }}" data-date="{{ $row->created_at }}"> &nbsp; </span>
                                <td id="log_timezone_{{ $x }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                    @if($activity_log->count() == 0)
                    <div class="norecords">No records to show</div>
                    @if($search)
                    <div class="for-search">for {{ $search }}</div>
                    @endif
                    @endif

                    {!! str_replace('/?', '?', $activity_log->render()) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
var log_limit = {{ count($activity_log)}};
for (var i = 1; i <= log_limit; i++) {
    var log_date = moment.utc($('#log_date_'+ i ).data('date'));
    var offset = new Date().getTimezoneOffset();
    var converted_log_date = new Date(log_date.utcOffset(offset * -1));
    var month = converted_log_date.getMonth()+1;
    var day   = converted_log_date.getDate();
    var year  = converted_log_date.getFullYear();
    var hour  = converted_log_date.getHours();
    var minutes = converted_log_date.getMinutes();
    var A = '';
    if(hour == 0){
      var result = '12'; 
    }
    else{
      var result  = hour;  
    }
    if(hour > 12){
        A = 'PM';
        hour = (hour - 12);

        if(hour == 12){
            hour = '00';
            A = 'AM';
        }
    }
    else if(hour < 12){
        A = 'AM';   
    }else if(hour == 12){
        A = 'PM';
    }else if(hour == 0){
        hour = '00';
    }
    if(minutes < 10){
        minutes = "0" + minutes; 
    }
    $('#log_timezone_'+ i ).text(month + '/' + day + '/' + year + ' ' + hour + ':' + minutes +' ' + A);
    $('#log_date_'+ i ).hide();
}
</script>
@endsection
