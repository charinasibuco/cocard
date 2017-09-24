@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $name }} Members</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <div class="pull-right">
                            <a href="{{ url('organization/'. $slug .'/administrator/email-group') }}" class="btn btn-darkblue ">
                                Back
                            </a>
                            @can('add_email_member')
                            <a href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group_id.'/members/create') }}" class="btn btn-darkblue ">
                                Add Member&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            @endcan
                        </div>
                    </div>
                </h3>
            </div>
        </div>
        <div class="row" style="border:solid 1px #CCC; padding:5px">
            <form class="form-inline" method="get" action=" {{ url('organization/'.$slug.'/administrator/email-group/'.$email_group_id)}} ">
                Search by:
                <div class="form-group">
                    <label for="age">Age:</label>
                    {{--  <input type="text" class="form-control" id="age" name="search_by_age" style="width:150px" value="{{ $age }}"> --}}
                    <input type="number" class="form-control" style="width:75px" name="from" value="{{ $from }}"> to <input type="number" class="form-control" style="width:75px" name="to" value="{{ $to }}">
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select class="form-control" name="search_by_gender" style="width:150px">
                        <option value="All" {{ ($gender == 'All')? 'selected="selected"' : ''}}>All</option>
                        <option value="Male" {{ ($gender == 'Male')? 'selected="selected"' : ''}}>Male</option>
                        <option value="Female" {{ ($gender == 'Female')? 'selected="selected"' : ''}}>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="marital_status">Marital Status:</label>
                    <select class="form-control" name="search_by_marital_status" style="width:150px">
                        <option value="All">All</option>
                        <option value="Single"{{ ($marital_status == 'Single')? 'selected="selected"' : ''}}>Single</option>
                        <option value="Married"{{ ($marital_status == 'Married')? 'selected="selected"' : ''}}>Married</option>
                        <option value="Divorced"{{ ($marital_status == 'Divorced')? 'selected="selected"' : ''}}>Divorced</option>
                        <option value="Widowed/Widower"{{ ($marital_status == 'Widowed/Widower')? 'selected="selected"' : ''}}>Widowed/Widower</option>
                        <option value="Committed"{{ ($marital_status == 'Committed')? 'selected="selected"' : ''}}>Committed</option>
                        <option value="Not Specified"{{ ($marital_status == 'Not Specified')? 'selected="selected"' : ''}}>Not Specified</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-default pull-right">Search</button>
            </form>
        </div>
        <div class="table-main panel panel-default">
            <table class="table table-striped" id="tabledata">
                <thead class="theader">
                    <th></th>
                    <th width="15%">Name</th>
                    <th width="15%">Email</th>
                    <th width="15%">Gender</th>
                    <th width="5%">Age</th>
                    <th width="15%">Marital Status</th>
                    <th width="25%"style="text-align:center;">Action</th>
                </thead>
                <tbody>
                    <?php
                        $x = 0;
                    ?>
                    @foreach($email_group_members as $email_group_member)
                    <div class="delete-modal-container" data-id="{{$email_group_member->id}}">
                        <div class="modal-delete">
                            <div class="modal-header">
                                <h5>Email Group Member: Delete {{$email_group_member->name}}.</h5>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this member?</p>
                            </div>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group_id.'/members/delete/'. $email_group_member->id) }}">
                                            YES
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$email_group_member->id}}">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <tr>
                        <td><input type="checkbox" id="row_selected_{{ $x }}" name="row_selected_{{ $email_group_member->id}}" value="{{ $email_group_member->email }}"></td>
                        <td>{{ $email_group_member->name }}</td>
                        <td>{{ $email_group_member->email }}</td>
                        <td>{{ $email_group_member->gender }}</td>
                        {{-- <td>{{ $now - $email_group_member->birthdate }}</td> --}}
                        <?php $month = Carbon\Carbon::parse($email_group_member->birthdate)->format('m');
                        $from = new DateTime($email_group_member->birthdate);
                        $to   = new DateTime($now);
                        $age__ =  $from->diff($to)->y;
                        ?>
                        @if($age__ == 0)
                            <td>< 1 year </td>
                        @else
                            @if($age__ >1)
                            <td>{{ $age__ }} years old</td>
                            @else
                                <td>{{ $age__ }} year old</td>
                            @endif
                        @endif
                        <td>{{ $email_group_member->marital_status}}</td>
                        <td style="text-align:center;">
                            @can('message_email_member')
                            <a href="" data-toggle="modal" data-target="#group_member{{ $email_group_member->id}}">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-envelope-o fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('edit_email_member')
                            <a href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group->id.'/members/edit/'. $email_group_member->id) }}">
                                <span class="fa-stack fa-lg icon-edit">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-pencil fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                            @can('delete_email_member')
                            <a title="Delete Email Group" class="delete_modal" data-id="{{$email_group_member->id}}" style="cursor:pointer;">
                                <span class="fa-stack fa-lg icon-delete">
                                    <i class="fa fa-square fa-stack-2x"></i>
                                    <i class="fa fa-trash-o fa-stack-1x"></i>
                                </span>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="group_member{{ $email_group_member->id }}" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Send Message to {{ $email_group_member->name }}</h4>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/organization/'.$slug.'/administrator/email-group/send-to-individual/'.$email_group_member->id)}} " method="post" >
                                        <label>To:</label>
                                        <input class="form-control" name="email" value="{{ $email_group_member->email}}" disabled>
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
        @if(count($email_group_members) == 0)
        <div style="text-align:center; padding:20px">No records to show</div>
        @endif
        <div>
            @if(count($email_group_members) != 0)
            <input type="checkbox" id="select-all" name="save_value" style="margin-left:8px" value="">  Select All<br><br>
            <button class="btn btn-default" data-toggle="modal" data-target="#member" id="save_value" name="save_value" disabled>Send Message</button>
            @endif
            <!-- Modal -->
            <div class="modal fade" id="member" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Send Message to</h4>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/organization/'.$slug.'/administrator/email-group/send-to-multiple/'.$email_group->id)}}" method="post" >
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
<script>
var removeLastChar = function(value, char){
    var lastChar = value.slice(-1);
    if(lastChar == char) {
        value = value.slice(0, -1);
    }
    console.log(value);
    return value;
}
$(function(){
    $('#save_value').click(function(){
        var val = [];
        $(':checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });
        $('#multiple_email').val(val);
        var nums = $('#multiple_email').val();
        var result = removeLastChar(nums, ',');
        $('#multiple_email').val(result);
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
    $(".delete_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation[data-id="' + id + '"]').css('display','none');
    });
    var email_group_members_count = '{{ count($email_group_members) }}';
    for (var i = 0; i<email_group_members_count; i++) {
        $('#row_selected_'+i).click(function(event) {
        var x = $("input:checked").length;
        if(x > 0) {
           $('#save_value').attr('disabled', false);
        } else {
           $('#save_value').attr('disabled', true);
        }
        });
    }
});

</script>
@endsection
