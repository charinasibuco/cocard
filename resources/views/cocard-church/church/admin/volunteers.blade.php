@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6" id="main-list-title">
                <h3>Events with Volunteer Groups</h3>
            </div>
            <div class="col-md-6" id="list-title" style="display:none">
                <h4></h4>
                <h6></h6>
                <cite id="start_date"></cite></br>
                <cite id="end_date"></cite></br>
            </div>
            <div class="col-md-6">
                <div class="clearfix">
                    <a id="back-main-list" class="btn btn-darkblue float-right" style="display:none">
                        <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                    </a>
                </div>
            </div>
        </div>
        </br>
        {{-- <div class="table-main panel panel-default" id="volunteer-list"> --}}
         <div class="table-main panel panel-default" id="volunteer-main-list">
            <div align="center" style="height:100%; padding-top:10%" class="loading"></div>
{{--             @include('cocard-church.volunteer.main_list') --}}
        </div>
        
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
// var url = "{{ route('church_volunteer_list',$slug) }}";

var event_name ='{{ $event_name or ''}}';
var volunteer_type ='{{ $volunteer_type or ''}}';

mainList = function(){
    var url_main = "{{ route('church_volunteer_main_list',$slug) }}";
    var page = "{{ $page }}";
    var render_page = '{!! $volunteer_groups->render() !!}';
    var search_volunteer_group = "{{ $search_volunteer_group }}";
    $.get(url_main,{page:page,search_volunteer_group:search_volunteer_group}).done(function(data){
        // $('#volunteer-list').empty().html(data);
         $('#volunteer-main-list').empty().html(data);
        $('#pagination').html(render_page);
    });
}
groupRow = function(e){
    var url = "{{ route('church_volunteer_list',$slug) }}";

    var event_name = '{{ $event_name or ''}}';
    var volunteer_type ='{{ $volunteer_type or ''}}';
    var event_id ='{{ $event_id or ''}}';
    //alert(volunteer_groups.event_name + '----'+ volunteer_groups.volunteer_type);
    $.get(url,{event_name:event_name,volunteer_type:volunteer_type,event_id:event_id}).done(function(data){
        $('#volunteer-main-list').empty().html(data);
    });
}
if(event_name == ''){
   mainList(); 
}else{
    groupRow();
}
$('#back-main-list').on('click',function(){
    mainList();
});
$(document).ready(function() {
    $(document).on('click', '.pagination a', function (e) {
        var search = $('#search_volunteer_group').val();
        $(this).attr('href', function(){
            return this.href +'&search_volunteer_group='+search;
        });
    });
});
</script>
@endsection