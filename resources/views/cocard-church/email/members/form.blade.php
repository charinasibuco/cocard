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
                <li class="active"><a href="{{ url('/organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create')}}">Add by Name</a></li>
                <li><a href="{{ url('/organization/'.$slug.'/administrator/email-group/'.$email_group_id.'/members/create/filter')}}">Add by Criteria</a></li>
            </ul>
        </div>
        <div class="table-main panel panel-default ">
            
            <div class="row">
                <div class="col-md-offset-2 col-md-9" style="padding-top:10px;">
                    <div class="search form-group">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                            <input type="text" id="search_text"class="search-form form-control" placeholder="Search User" name="term" aria-describedby="basic-addon1" value="">
                        </div>
                    </div>
                </div>
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ $action }}">
                    <input type="hidden" class="form-control" name="slug" value="{{ $organization->url }}">
                    <input type="hidden" class="form-control" name="email_group_id" value="{{ $email_group_id }}">
                    <input type="hidden" id="user_id" class="form-control" name="user_id" value="{{ $user_id }}">
                    <input type="hidden" name="cb_val" value="" id="cb_val">
                    <input type="hidden" name="egid" value="{{$id}}" id="dcid">
                    <input type="hidden" name="group_id" value="{{$group_id}}" id="group_id">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="text" id="email" class="form-control" placeholder="email" name="email" value="{{ $email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Gender</label>
                        <div class="col-sm-9">
                            {{-- <input type="text" id="gender" class="form-control" placeholder="gender" name="gender" value=""> --}}
                            <select name="gender" class="form-control" id="gender">
                                <option value="Male" {{ ($gender == 'Male')? 'selected="selected"' : ''}}>Male</option>
                                <option value="Female" {{ ($gender == 'Female')? 'selected="selected"' : ''}}>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Marital Status</label>
                        <div class="col-sm-9">
                          {{--   <input type="text" id="marital_status" class="form-control" placeholder="marital status" name="marital_status" value=""> --}}
                            <select name="marital_status" value="" class="form-control" id="marital_status">
                                <option value="Single" {{ ($marital_status == 'Single')? 'selected="selected"' : ''}}>Single</option>
                                <option value="Married" {{ ($marital_status == 'Married')? 'selected="selected"' : ''}}>Married</option>
                                <option value="Divorced"{{ ($marital_status == 'Divorced')? 'selected="selected"' : ''}}>Divorced</option>
                                <option value="Widowed/Widower"{{ ($marital_status == 'Widowed/Widower')? 'selected="selected"' : ''}}>Widowed/Widower</option>
                                <option value="Committed"{{ ($marital_status == 'Committed')? 'selected="selected"' : ''}}>Committed</option>
                                <option value="Not Specified"{{ ($marital_status == 'Not Specified')? 'selected="selected"' : ''}}>Not Specified</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Birtdate</label>
                        <div class="col-sm-9">
                            {{-- <input type="text" id="birthdate" class="form-control" placeholder="M/D/YYYY" name="birthdate" value=""> --}}
                         {{--    <div class="form-group"> --}}
                                <div class='input-group datepicker dtp'>
                                    <input required class="form-control" type="text" id="birthdate" placeholder="M/D/YYYY" name="birthdate"  value="{{ $birthdate }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                          {{--   </div> --}}
                        </div>
                    </div>
                      @if($id >0)
                       @else
                       <div id="cbdiv" class="form-group float-right" style="margin-right: 7%;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="save_another" id="cbx">&nbsp;Save and Add Another
                                </div><br>
                            </div>
                        </div>
                        <br><br>
                        @endif
                    {!! csrf_field() !!}
                    <div class="clearfix" style="margin-right: 7%;">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-darkblue ">
                                Submit
                            </button>
                            <a href="{{ url('organization/'. $slug .'/administrator/email-group/'.$email_group_id) }}" class="btn btn-red ">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
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
});
</script>
@endsection
