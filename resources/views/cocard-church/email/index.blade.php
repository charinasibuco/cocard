@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Email Groups</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        @can('add_email_group')
                        <a title="Add Email Group"href="{{ url('organization/'. $slug .'/administrator/email-group/create') }}" class="btn btn-darkblue float-right">
                            Add Email Group&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                        @endcan
                    </div>
                </h3>
            </div>
        </div>
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {{ Session::get('message') }}
        </div>
        @endif
        <div class="table-main panel panel-default">
            <div class="table-responsive">
            <input type="checkbox" id="select-all-top" name="save_value" style="margin-left:8px" value="">  Select All<br><br>
                <table class="table table-striped" id="tabledata">
                    <thead class="theader">
                        <th></th>
                        <th>Name</th>
                        <th>Details</th>
                        <th style="text-align:center;">Action</th>
                    </thead>
                    <tbody>
                        <?php 
                            $x=0;
                        ?>
                        @foreach($email_groups as $email_group)
                        <div class="delete-modal-container" data-id="{{$email_group->id}}">
                            <div class="modal-delete">
                                <div class="modal-header">
                                    <h5>Email Group: Delete {{ $email_group->name }}.</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete this email group?</p>
                                </div>
                                <div class="modal-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('organization/'. $slug .'/administrator/email-group/delete/'.$email_group->id) }}">
                                                YES
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$email_group->id}}">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <tr>
                            <td >
                                <input type="checkbox" id="row_selected_{{ $x }}" name="row_selected_{{ $email_group->id}}" value="@foreach($email_group->EmailGroupMember as $q)
                               @if($q->status == 'Active'){{$q->email}}@endif @endforeach ">
                            </td>
                            <td>{{ $email_group->name }}</td>
                            <td>{{ $email_group->details }}</td>
                            <td style="text-align:center;">
                                @can('view_email_member')
                                <a title="View Email Group Members"href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group->id) }}">
                                    <span class="fa-stack fa-lg icon-users" style="color: #012732;">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-users fa-stack-1x"></i>
                                    </span>
                                </a>
                                @endcan
                                @can('message_email_group')
                                @if($email_group->EmailGroupMember->count() != 0)
                                <a title="Send a Message to Email Group"href="" data-toggle="modal" data-target="#group_id{{ $email_group->id }}">
                                    <span class="fa-stack fa-lg icon-edit">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-envelope-o fa-stack-1x"></i>
                                    </span>
                                </a>
                                @else
                                <a onclick="alert('This email group is empty')">
                                    <span class="fa-stack fa-lg icon-edit" style="color:#414143">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-envelope-o fa-stack-1x"></i>
                                    </span>
                                </a>
                                @endif
                                @endcan
                                 @can('edit_email_group')
                                <a title="Modify Email Group" href="{{ url('organization/'. $slug .'/administrator/email-group/edit/'.$email_group->id) }}">
                                    <span class="fa-stack fa-lg icon-edit">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-pencil fa-stack-1x"></i>
                                    </span>
                                </a>
                                @endcan
                                @can('delete_email_group')
                                <a title="Delete Email Group" class="delete_modal" data-id="{{$email_group->id}}" style="cursor:pointer;">
                                    <span class="fa-stack fa-lg icon-delete">
                                        <i class="fa fa-square fa-stack-2x"></i>
                                        <i class="fa fa-trash-o fa-stack-1x"></i>
                                    </span>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        <!-- Modal -->
                        <div class="modal fade" id="group_id{{ $email_group->id }}" role="dialog">

                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Send Message to {{ $email_group->name }}</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form action=" {{ url('/organization/'.$slug.'/administrator/email-group/send-to-group/'.$email_group->id)}} " method="post" >
                                            <label>To:</label>
                                            @foreach($email_group->EmailGroupMember as $member)
                                                @if($member->status == 'Active')
                                                {{ $member->email }},
                                                @endif
                                            @endforeach
                                            <br>
                                            <label>Subject:</label>
                                            <input class="form-control" value="" name="subject" required>
                                            <label>Message:</label>
                                            <textarea class="form-control" value="" name="message" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            {!! csrf_field() !!}
                                            <button type="submit" class="btn btn-default">Send</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $x++;
                    ?>
                    @endforeach
                </tbody>
            </table>
            @if(isset($email_group))
            <input type="checkbox" id="select-all" name="save_value" style="margin-left:8px" value="">  Select All<br><br>
            <button class="btn btn-default" data-toggle="modal" data-target="#emailgroup" id="save_value" name="save_value" disabled>Send Message</button>
            @endif
             <!-- Modal -->
            <div class="modal fade" id="emailgroup" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Send Message to</h4>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/organization/'.$slug.'/administrator/email-group/send-to-multiple/'.(isset($email_group) ? $email_group->id : '' ))}}" method="post" >
                                <label>To:</label>
                                <input class="form-control" id="multiple_email" name="email" value="" required>
                                <br>
                                <label>Subject:</label>
                                <input class="form-control" value="" name="subject" required>
                                <label>Message:</label>
                                <textarea class="form-control" value="" name="message" required></textarea>
                            </div>
                            <div class="modal-footer">
                                {!! csrf_field() !!}
                                <button type="submit" class="btn btn-default">Send</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    //remove last comma
    var removeLastChar = function(value, char){
        var lastChar = value.slice(-1);
        if(lastChar == char) {
          value = value.slice(0, -1);
      }
      console.log(value);
      return value;
    }
    //trim spaces
    function myTrim(x) {
    return x.replace(/^\s+|\s+$/gm,'');
    }

    var email_group_count = '{{ count($email_groups) }}';

     $('#save_value').click(function(){
        var val = [];
        var result ;
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();

        });
        $('#multiple_email').val(val);
        var nums = $('#multiple_email').val();
        //var result = removeLastChar(nums.replace(/\s/g, ''));
        var result = nums.split(/[ ,]+/).join(',');
        var i = myTrim(result);
        var e = removeLastChar(i,',');
        //remove first char
        $('#multiple_email').val(e.slice(1));
    });
    $('#select-all').click(function(event) {
        if(this.checked) {
            $(':checkbox').prop('checked', true);
            $('#save_value').attr('disabled', false);
        } else {
            $(':checkbox').prop('checked', false);
            $('#save_value').attr('disabled', true);
        }
    });
    $('#select-all-top').click(function(event) {
        if(this.checked) {
            $(':checkbox').prop('checked', true);
            $('#save_value').attr('disabled', false);
        } else {
            $(':checkbox').prop('checked', false);
            $('#save_value').attr('disabled', true);
        }
    });
    for (var i = 0; i<email_group_count; i++) {
        $('#row_selected_'+i).click(function(event) {
        var x = $("input:checked").length;
        if(x > 0) {
           $('#save_value').attr('disabled', false);
        } else {
           $('#save_value').attr('disabled', true);
        }
        });
    };
});
</script>
@endsection
