@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="panel panel-primary panel-information">
            <div class="panel-heading personal-information"><i class="fa fa-user" aria-hidden="tdue"></i> &nbsp;Volunteers per Event Information</div>
            <div class="panel-body volunteer-table">
                
            </div>
        </div>
        <!-- Modal -->
        <div id="current_status" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <a class="btn" id="btn-status" style="display:inline">
                    <i id="like" aria-hidden="true"></i>
                </a>&nbsp;&nbsp;&nbsp;
                <p style="text-align:center; display:inline" id="current_status_per_volunteer"></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
var volunteer_group_id = '{{$volunteers->first()->volunteer_group_id}}';
var slug ='{{ $slug }}';
function volunteerDetailsTable(){
    $.get('{{ route("volunteer_detail_table") }}',{slug:slug,volunteer_group_id:volunteer_group_id}).done(function(data){
        $('.volunteer-table').empty().html(data);
    });
}
volunteerDetailsTable();
checkStatus = function(e){
    var volunteer_group = new Object;
    var dataId = $("#"+ e.id).data('id');
    $('tr#row_'+dataId).each(function() {
        var start_date_timezone = $(this).find('[data-id=data_start_' + dataId + ']').text(); 
        var end_date_timezone = $(this).find('[data-id=data_end_' + dataId + ']').text(); 

        var id = "#"+ e.id;
        volunteer_group.status = $("#"+ e.id).data('status');
        volunteer_group.disabled = $("#"+ e.id).data('disabled');
        volunteer_group.title = $("#"+ e.id).data('title');
        if(volunteer_group.status == 'Approved' && volunteer_group.title == 'Approved'){
            $('#current_status_per_volunteer').text('Approved Already');
            $('#like').addClass('icon-like');
            $('#like').removeClass('icon-dislike');
            $('#btn-status').addClass('btn-success');
            $('#btn-status').removeClass('btn-danger');
            $("#current_status").modal('show');
        }else if(volunteer_group.status == 'Rejected'  && volunteer_group.title == 'Decline'){
            $('#current_status_per_volunteer').text('Rejected already!');
            $('#like').addClass('icon-dislike');
            $('#like').removeClass('icon-like');
            $('#btn-status').addClass('btn-danger');
            $('#btn-status').removeClass('btn-success');
            $("#current_status").modal('show');
        }
        else if(volunteer_group.disabled == 'disabled'){
            //do nothing :)
        }
        else{
            if(volunteer_group.title == 'Approved'){
                var url = "{{ url('organization/'.$slug.'/administrator/volunteer/filter-by-event/view-volunteers-by-event/Approve') }}"+'/'+dataId;
                $.post(url,{id:dataId,start_date:start_date_timezone,end_date:end_date_timezone}).done(function(){
                    $('#status-per-volunteer_'+dataId).css('display','block');
                    $('#status-per-volunteer_'+dataId).empty();
                    volunteerDetailsTable();
                    $('#loading_'+dataId).css('display','none');
                });  
            }else{
            var url = "{{ url('organization/'.$slug.'/administrator/volunteer/filter-by-event/view-volunteers-by-event/Reject') }}"+'/'+dataId;
                $.post(url,{id:dataId,start_date:start_date_timezone,end_date:end_date_timezone}).done(function(){
                    $('#status-per-volunteer_'+dataId).css('display','block');
                    $('#status-per-volunteer_'+dataId).empty();
                    volunteerDetailsTable();
                    $('#loading_'+dataId).css('display','none');
                    
                }); 
            }
            
            timeoutLoader(dataId);
        }
    });
}
timeoutLoader = function(dataId){
    var timeout;
    function startLoad() {
        $('#loading_'+dataId).css('display','block');
       $('#loading_'+dataId).css('width','15%');
        $('#status-per-volunteer_'+dataId).css('display','none');
        $('#loading_'+dataId).html('<img style="height:30px; width:30px" src="{{asset('images/ajax-loader-small.gif')}}"/>');
    }
    function loaded() {
        $('#loading_'+dataId).html('Please try Again.');
    }
    clearTimeout(timeout);
    timeout = setTimeout(loaded, 30500);
    $('#volunteer_'+dataId).click(startLoad);
    startLoad();
}
sendMessage = function(e){
   var details = new Object();
   var dataId = $("#"+ e.id).data('id');
   var button =$("#"+ e.id).data('button');
    if(button == 'specific'){
        var message = $('#message_'+dataId).val();
        var subject = $('#subject_'+dataId).val();
        var email = $('#email'+dataId).val();
        if(message != '' && subject != '' && email != ''){
            var send_url = "{{ url('/organization/'.$slug.'/administrator/volunteer/send-email-to-individual-volunteer/')}}" +'/'+ dataId; 
            $.post(send_url,{message:message,subject:subject,email:email,volunteer_group_id:volunteer_group_id,id:dataId}).done(function(){
             // alert('message sent');
            $('#sent_'+dataId).css('display','block');
            $('#form_'+dataId).css('display','none');
            $('#group_'+dataId).css('display','none');
            $('.loading').css('display','none');
           }).fail(function(){
                $('.loading').html('Fail sending. Please try Again.');
            });
            timeoutLoaderMessage(dataId,button);
        }else{
            if($('#message_'+dataId).val() == ''){
                $('#message_'+dataId).css('border','solid red 1px');
            }
            if($('#subject_'+dataId).val() == ''){
                $('#subject_'+dataId).css('border','solid red 1px');
            }
        }
    }else if(button == 'all'){
        var message = $('#message').val();
        var subject = $('#subject').val();
        var email = $('#multiple_email').val();
        if(message != '' && subject != ''){
            var url = "{{ url('/organization/'.$slug.'/administrator/volunteer/send-email-to-multiple-volunteer/')}}"; 
            $.post(url,{message:message,subject:subject,email:email,volunteer_group_id:volunteer_group_id,id:dataId}).done(function(){
             // alert('message sent');
            $('#sent').css('display','block');
            $('#form_all').css('display','none');
            $('#group_all').css('display','none');
            $('.loading').css('display','none');
           }).fail(function(){
            $('.loading').html('Fail sending. Please try Again.');
           });
            timeoutLoaderMessage(dataId,button);
        }else{
            if($('#message').val() == ''){
                $('#message').css('border','solid red 1px');
            }
            if($('#subject').val() == ''){
                $('#subject').css('border','solid red 1px');
            }
        }
    }
}
// sendMessageAll = function(e){
//    var details = new Object();
//    var dataId = $("#"+ e.id).data('id');
//    var message = $('#message').val();
//    var subject = $('#subject').val();
//    var email = $('#multiple_email').val();
    
//    if(message != '' && subject != ''){
//         var url = "{{ url('/organization/'.$slug.'/administrator/volunteer/send-email-to-multiple-volunteer/')}}"; 
//         $.post(url,{message:message,subject:subject,email:email}).done(function(){
//          // alert('message sent');
//         $('#sent').css('display','block');
//         $('#form_all').css('display','none');
//         $('#group').css('display','none');
//         $('.loading').css('display','none');
//        });
//         timeoutLoaderMessage(dataId);
//    }else{
//         if($('#message').val() == ''){
//             $('#message').css('border','solid red 1px');
//         }
//         if($('#subject').val() == ''){
//             $('#subject').css('border','solid red 1px');
//         }
//    }
// }
timeoutLoaderMessage = function(dataId,button){
    var timeout;
    function startLoad2() {
        if(button == 'all'){
            $('#form_all').css('display','none');
        }else{
            $('#form_'+dataId).css('display','none');
        }
        
        $('.loading').css('display','block');
        $('.loading').html('<img style="height:100px;" src="{{asset('images/ajax-loader-small.gif')}}"/>');
    }
    // function loaded2() {
    //     $('.loading').html('Fail sending. Please try Again.');
    // }
    // clearTimeout(timeout);
    // timeout = setTimeout(loaded2, 100500);
    $('.send').click(startLoad2);
    startLoad2();
}

</script>
@endsection
