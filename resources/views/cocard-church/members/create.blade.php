<form class="form-horizontal form form-members" role="form" enctype="multipart/form-data" method="POST" action="{{ $action }}">
    @if(count($errors) > 0)
    <div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
    @foreach ($errors->all() as $error)
    @if(strpos($error, 'required') == false)
    <div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
    @endif
    @endforeach
    @endif
    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('message') }}
    </div>
    @endif
            <input type="hidden" class="form-control" name="slug" value="{{ ($organization)?$organization->url:''}}">
            <input type="hidden" class="form-control" name="organization_id" value="{{ ($organization)?$organization->id:''}}">
            <div class="well" id="adminAddUser">

                <div class="form-group">
                    <div class="row">
                        <div class="user-img">
                            @if($action_name == "Edit")
                            <div class="photo">
                                <img id="prev_image" src="{{ asset('images/'.(($image != null) ? $image : 'user.png')) }}" style="width:200px; height:auto; padding-bottom:10px;">
                            </div>
                            <div style="margin-left:2%;">
                                <div class="fileUpload btn btn-primary">
                                    <span> <i class="fa fa-camera" aria-hidden="true"></i> @lang('dashboard_details.change_profile_picture')</span>
                                    <input type="file" id="fieldID"  class="upload" name="image" value="{{ $image or "" }}"/><span class="error"></span>
                                </div>
                                &nbsp;
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                            <label for="first-name">First Name:</label>
                            <input placeholder="First Name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                            @if ($errors->has('first_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                            <label for="first-name">Last Name:</label>
                            <input placeholder="Last Name" type="text" class="form-control" name="last_name" value="{{ $last_name }}" required>
                            @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first-name">Middle Name:</label>
                            <input placeholder="Middle Name" type="text" class="form-control" name="middle_name" value="{{ $middle_name }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">Address:</label>
                            <input placeholder="Address" type="text" class="form-control" name="address" value="{{ $address }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">City:</label>
                            <input placeholder="City" type="text" class="form-control" name="city" value="{{ $city }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">State:</label>
                            <input placeholder="State" type="text" class="form-control" name="state" value="{{ $state }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">Zipcode:</label>
                            <input placeholder="Zipcode" type="text" class="form-control" name="zipcode" value="{{ $zipcode }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">Birthdate:</label>
                            <div class="form-group">
                                <div class='input-group datepicker'>
                                    <input placeholder="Birthdate" type="date" class="form-control" name="birthdate" value="{{ $birthdate }}">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">Gender:</label>
                            <select class="form-control" name="gender" value="{{ $gender }}">
                                <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                                <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first-name">Marital Status:</label>
                            <select class="form-control" name="marital_status" value="{{ old('marital_status') }}">
                                <option value="Single" @if($marital_status == 'Single') selected @endif>Single</option>
                                <option value="Married" @if($marital_status == 'Married') selected @endif>Married</option>
                                <option value="Divorced" @if($marital_status == 'Divorced') selected @endif>Divorced</option>
                                <option value="Widowed/Widower" @if($marital_status == 'Widowed/Widower') selected @endif>Widowed/Widower</option>
                                <option value="Committed" @if($marital_status == 'Committed') selected @endif>Committed</option>
                                <option value="Not Specified" @if($marital_status == 'Not Specified') selected @endif>Not Specified</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="first-name">Phone number:</label>
                            <input placeholder="Phone Number" type="number" class="form-control" name="phone" value="{{ $phone }}" required>
                            @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    @if($action_name == "Add")
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">@lang('dashboard_details.change_profile_picture'):</label>
                            <input type="file" id="img"class="form-control" placeholder="Image" name="image" value="{{ $image }}">
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="first-name">Email Address:</label>
                            <input placeholder="Email Address" type="email" class="form-control" name="email" value="{{ $email }}" required>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div id="password_field">
                        <label class="" for="password">@lang('dashboard_details.password'):</label>
                        <div class="input-group">
                            <input required  class="form-control" id="passwordfield" name="password" type="password" placeholder="******">
                            <div class="input-group-addon">
                                <span class="fa fa-eye float-right" id ="fa-eye-p" aria-hidden="true" style=""></span>
                            </div>
                        </div>
                        <label class="" for="password_confirmation">@lang('dashboard_details.confirm_password'):</label>
                        <div class="input-group">
                            <input  required class="form-control" id="passwordconffield" name="password_confirmation" type="password" placeholder="******">
                            <div class="input-group-addon">
                                <span class="fa fa-eye float-right" id ="fa-eye-cp" aria-hidden="true" style=""></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="form-group ">
            <br>
            {!! csrf_field() !!}
            <div class="clearfix">
                <div class="pull-right">
                    <button type="submit" class="btn btn-darkblue ">
                        Submit
                    </button>
                </div>
            </div>
        </div>
</form>
@section('script')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$("form.form").validate({
    rules : {
        password : {
            minlength : 5
        },
        password_confirmation : {
            minlength : 5,
            equalTo : "#passwordfield"
        }
    }
});
</script>
@endsection
