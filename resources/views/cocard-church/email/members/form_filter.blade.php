@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">      
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
        </div>   
        <div class="margin-mob-top">
            <ul class="nav donation-nav">
                <li ><a href="{{ url('/organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create')}}">Add by Name</a></li>
                <li class="active"><a href="{{ url('/organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create/filter')}}">Add by Criteria</a></li>
            </ul>
        </div>
        
        <div class="table-main panel panel-default ">
            <div class="row" style="border:solid 1px #CCC; padding:5px">
                <form class="form-inline" method="get" action=" {{ url('organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create/filter')}} ">
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
            <input type="checkbox" id="select-all-top" name="save_value" style="margin-left:8px" value="">  Select All<br><br>
                <form class="form-inline" method="post" action=" {{ url('organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create/filter/add-member')}} ">
                    <table class="table table-striped" id="tabledata">
                        <thead class="theader">
                            <th></th>
                            <th width="15%">Name</th>
                            <th width="15%">Email</th>
                            <th width="15%">Gender</th>
                            <th width="5%">Age</th>
                            <th width="15%">Marital Status</th>
                            {{--<th width="25%"style="text-align:center;">Action</th>--}}
                        </thead>
                        <tbody>
                            <?php
                            $x = 0;
                            ?>
                            @foreach($email_group_members as $email_group_member)
                            <div class="delete-modal-container " data-id="{{$email_group_member->user_id}}">
                                <div class="modal-delete">
                                    <div class="modal-header">
                                        <h5>Add to Email Group</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to add {{$email_group_member->first_name}} {{$email_group_member->last_name}} to this group?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a class="btn btn-red btn-full"  style="color:#fff;background-color: #012732;" href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group_id.'/members/create/filter/add-member/'.$email_group_member->id ) }}">
                                                    YES
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$email_group_member->user_id}}">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <tr>
                                <td style="width: 5%;"><input type="checkbox" id="row_selected_{{ $email_group_member->id }}" name="row_selected[]" value="{{ $email_group_member->id }}"></td>
                                <td>{{ $email_group_member->first_name }} {{ $email_group_member->last_name }}</td>
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
                               {{-- <td style="text-align:center;">
                                      @can('edit_email_member')
                                      <a title="Add to Email Group" class="delete_modal" data-id="{{$email_group_member->user_id}}" style="cursor:pointer;">
                                          <span class="fa-stack fa-lg icon-edit">
                                              <i class="fa fa-square fa-stack-2x"></i>
                                              <i class="fa fa-plus fa-stack-1x"></i>
                                          </span>
                                      </a>
                                      @endcan
                                  </td>--}}
                            </tr>
                            <input type="hidden" name="email_group" value="{{$email_group_id}}">
                        </div>
                        <?php
                        $x++;
                        ?>
                        @endforeach
                    </tbody>
                </table>        
            <div class="col-md-6 selected">
                <button type="submit" class="btn btn-darkblue">Add all selected to this Email Group</button>
                <a href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group_id) }}" class="btn btn-red ">
                    Cancel
                </a>
            </div>

        </form>  
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.6/jstz.js"></script>
<script type="text/javascript">
$(document).ready(function(){
   $('#cbx').click(function(){
    if($(this).prop("checked") == true){
            document.getElementById("cb_val").value = "1";
    }else{
            document.getElementById("cb_val").value = "0";
    }
});
     $('#select-all-top').click(function(event) {
        if(this.checked) {
            $(':checkbox').prop('checked', true);
            $('.selected').prop('onclick','return false');
        } else {
            $(':checkbox').prop('checked', false);
            $('.selected').prop('onclick','return true');

        }
    });
    //  $("input[type='checkbox']").change(function(){
    //      if(a.length == a.filter(":checked").length){
    //     alert('all checked');
    // } else {
    //     alert('not all checked');
    // }
    //  });
});
</script>
@endsection
