@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Family Member</h3>
            </div>
            <!-- <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('organization/'. $slug .'/user/family/'.$family_id) }}"  class="btn btn-darkblue float-right">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
                        </a>
                    </div>
                </h3>
            </div> -->
        </div>
        <div class="table-main panel panel-default">
            @if(count($errors) > 0)
            <!-- <div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div> -->
            @foreach ($errors->all() as $error)

            <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>

            @endforeach
            @endif
            @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div>
            @endif
            <div class="row">
                <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ $action }}">
                    <input type="hidden" class="form-control" name="slug" value="{{ $organization->url }}">
                    <input type="hidden" class="form-control" name="organization_id" value="{{ $organization->id }}">
                    <input type="hidden" class="form-control" name="family_id" value="{{ $family_id }}">
                    <input type="hidden" class="form-control" name="user_id" value="{{ $user_id }}">
                    <input type="hidden" class="form-control" name="cb_num" id="cb_num" value="{{ $cb_num }}">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Firstname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="First name" name="first_name" value="{{ $first_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Middlename</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Middle name" name="middle_name" value="{{ $middle_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Lastname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Last name" name="last_name" value="{{ $last_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Birthdate</label>
                        <div class="col-sm-9">
                            <div class='input-group datepicker dtp'>
                                <input required placeholder="M/D/YYYY" type="text" class="form-control" name="birthdate"  value="{{ $birthdate }}" id="birthdate">
                                <input type="hidden" class="value" >
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Gender</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="gender" value="{{ $gender }}">
                                <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                                <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label hidden for="inputPassword3" class="col-sm-2 control-label">Allergies</label>
                        <div class="col-sm-9">
                            <input type="hidden" class="form-control" placeholder="Allergies" name="allergies" value="{{ $allergies }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Image</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" placeholder="Image" name="img" value="{{ $img }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Relationship</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Relationship" name="relationship" value="{{ $relationship }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Additional Info</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Additional Information" name="additional_info" value="{{ $additional_info }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label hidden for="inputPassword3" class="col-sm-2 control-label">Child Number</label>
                        <div class="col-sm-9">
                            <input type="hidden" class="form-control" placeholder="Child Number" name="child_number" value="{{ $child_number }}">
                        </div>
                    </div>
                    <div class="add_user" style="display: none;">
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control tel" placeholder="Phone Number" name="phone" value="{{ $phone }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Email Address</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" placeholder="Email Address" name="email" value="{{ $email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" placeholder="Password" name="password" value="{{ $password }}">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">
                        <div class="pull-right">
                            <div id="cbdiv" class="form-group float-right">
                                <div class="row">
                                    <div class="col-sm-12" @if($action_name == "Edit") style="display: none;" @endif>
                                        <input type="checkbox" name="save_another" class="cb" id="cb">&nbsp;Add to Member Directory
                                    </div><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn-viewmembers">
                        {!! csrf_field() !!}
                        <div class="clearfix">
                    		<div class="pull-right">
                    			<button type="submit" class="btn btn-darkblue">
                    				Submit
                    			</button>
                    			<a href="{{ url('organization/'. $slug .'/user/family/'.$family_id) }}" class="btn btn-red" style="    background-color: #F05656;">
                    				Cancel
                    			</a>
                    		</div>
                    	</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function(){
    
    $('#cb').change(function () {
        if (this.checked){ 
           $('.add_user').css('display', 'block');
           $('#cb_num').val('1');
        }else{
            $('.add_user').css('display', 'none');
            $('#cb_num').val('0');
        }
    });

    function checkedBox() {
        if ($('#cb_num').val() == 1){
            $('.add_user').css('display', 'block'); 
            $('#cb').attr("checked", "checked");
        }
    }

    checkedBox();
});
</script>
@endsection