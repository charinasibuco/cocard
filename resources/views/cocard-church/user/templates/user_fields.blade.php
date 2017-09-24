<div class="well form-members">
    <div class="row">
        <div class="form-group">
            <div class="row">
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
                <div class="user-img">
                    <div class="photo">
                        <img id="prev_image" src="{{ asset('images/'.(($image != null) ? $image : 'user.png')) }}" style="width:200px; height:auto; padding-bottom:10px;">
                    </div>
                    <div style="margin-left:2%;">
                        <div class="fileUpload btn btn-primary">
                            <span> <i class="fa fa-camera" aria-hidden="true"></i> @lang('dashboard_details.change_profile_picture')</span>
                            <input type="file" id="img"  class="upload" name="image" value="{{ $image }}"/>
                        </div>
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" class="form-control" name="organization_id" value="{{ $organization_id }}">
        @if($organization != null)<input type="hidden" class="form-control" name="slug" value="{{ $organization->url }}">@endif
        <div class="col-md-4">
            <div class="form-group">
                <label for="first-name">@lang('dashboard_details.first_name'):</label>
                <input required class="form-control" name="first_name" placeholder="First Name" value="{{ $first_name or "" }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="middle-name">@lang('dashboard_details.middle_name'):</label>
                <input  class="form-control" name="middle_name" placeholder="Middle Name"  value="{{ $middle_name or "" }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.last_name'):</label>
                <input required class="form-control" name="last_name" placeholder="Last Name" value="{{ $last_name or "" }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.address'):</label>
                <input required class="form-control" name="address" placeholder="Address" value="{{ $address}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.city'):</label>
                <input required class="form-control" name="city" placeholder="City" value="{{ $city}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.state'):</label>
                <input required class="form-control" name="state" placeholder="State" value="{{ $state}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.zipcode'):</label>
                <input required class="form-control" name="zipcode" placeholder="Zipcode" value="{{ $zipcode}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.birthdate'):</label>
                <div class="form-group">
                    <div class='input-group datepicker'>
                        <input required class="form-control" type="text" placeholder="M/D/YYYY" name="birthdate"  value="{{ $birthdate }}">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>@lang('dashboard_details.gender'):</label>

                <select class="form-control" name="gender" value="{{ old('gender') }}">
                    <option @if($gender == "Male") selected @endif value="Male">Male</option>
                    <option  @if($gender == "Female") selected @endif value="Female">Female</option>
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
        @if ((isset($staffs)) && ($action_name == 'Add'))
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.role'):</label>
                <select class="form-control" name ="role_id">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" @if($role_id == $role->id) selected="selected" @endif >{{ $role->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                <label for="last-name">@lang('dashboard_details.phone_number'):</label>
                <input required class="form-control tel" type="text" name="phone" placeholder="Phone Number" value="{{ $phone}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">@lang('dashboard_details.email'):</label>
                <input type="email" required class="form-control" name="email" placeholder="Email" value="{{ $email }}">
            </div>
        </div>
        <div class="col-md-12">
            <div id="password_field">
                <label class="" for="password">@lang('dashboard_details.password'):</label>
                <div class="input-group">
                    <input @if($action_name == "Add") required @endif class="form-control" id="password" name="password" type="password" @if($action_name == "Edit") placeholder="******" @else placeholder="Password" @endif>
                    <div class="input-group-addon">
                        <span class="fa fa-eye float-right" id ="fa-eye-p" aria-hidden="true" style=""></span>
                    </div>
                </div>
                <br>
                <label class="" for="confirm">@lang('dashboard_details.confirm_password'):</label>
                <div class="input-group">
                    <input @if($action_name == "Add") required @endif class="form-control" id="passwordconffield" name="confirm" type="password" @if($action_name == "Edit") placeholder="******" @else placeholder="Password" @endif>
                    <div class="input-group-addon">
                        <span class="fa fa-eye float-right" id ="fa-eye-cp" aria-hidden="true" style=""></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
<script src="http://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script type="text/javascript">
$("form.form").validate({
    rules : {
        password : {
            minlength : 5
        },
        confirm : {
            minlength : 5,
            equalTo : "#password"
        }
    }
});

var token = "{{ csrf_token() }}";
var change_status_url = "{{ route('change_status')  }}";

$(".staff-status").click(function(){
    var switch_ = $(this);
    var value = switch_.closest("td").find(".status-container").html();
    $.post(change_status_url,{id:switch_.data("staff_id"),status:value,_token:token}).done(function(data){
        switch_.closest("td").find(".status-container").html(data);
    });
});

$(function () {
    $(":file").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function imageIsLoaded(e) {
    $('#prev_image').attr('src', e.target.result);
};
</script>
@endsection
