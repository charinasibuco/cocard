@extends('layouts.app')
@section('content')
<div class="bg-banner" style="height: auto!important;">
    <div class="container">
        <div class="col-md-offset-2 col-md-8">
            <div class="transparent-box-reg">
                <h2 class="text-center">{{ ($organization)?$organization->name:''}}</h2><br>
                <div class="col-md-offset-1 col-md-10">
                    @if(count($errors) > 0)
                    <?php $required =  $errors->first()?>
                    @if(strpos($required, 'required'))
                    <div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
                    @endif
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
                    <form class="form-horizontal" role="form" method="POST" action="{{ $action }}">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="col-md-12">
                                    <input type="hidden" class="form-control" name="slug" value="{{ ($organization)?$organization->url:''}}">
                                    <input type="hidden" class="form-control" name="organization_id" value="{{ ($organization)?$organization->id:''}}">
                                    <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                        <input placeholder="First Name*" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                                        @if ($errors->has('first_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('first_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                        <input placeholder="Last Name*" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                                        @if ($errors->has('last_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('last_name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="Middle Name" type="text" class="form-control" name="middle_name" value="{{ old('middle_name') }}">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="Address" type="text" class="form-control" name="address" value="{{ old('address') }}">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="City" type="text" class="form-control" name="city" value="{{ old('city') }}">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="State" type="text" class="form-control" name="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="Zipcode" type="text" class="form-control" name="zipcode" value="{{ old('zipcode') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class='input-group datepicker dtp'>
                                            <input required class="form-control" type="text" placeholder="M/D/YYYY" name="birthdate"  value="{{ $birthdate }}">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="gender" value="{{ old('gender') }}">
                                            <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                                            <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select class="form-control" name="marital_status" value="{{ old('marital_status') }}">
                                            <option value="Single" @if($marital_status == 'Single') selected @endif>Single</option>
                                            <option value="Married" @if($marital_status == 'Married') selected @endif>Married</option>
                                            <option value="Divorced" @if($marital_status == 'Divorced') selected @endif>Divorced</option>
                                            <option value="Widowed/Widower" @if($marital_status == 'Widowed/Widower') selected @endif>Widowed/Widower</option>
                                            <option value="Committed" @if($marital_status == 'Committed') selected @endif>Committed</option>
                                            <option value="Not Specified" @if($marital_status == 'Not Specified') selected @endif>Not Specified</option>
                                        </select>
                                    </div>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <input placeholder="Phone Number*" type="text" class="form-control tel" name="phone" value="{{ old('phone') }}" required>

                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input placeholder="Email Address*" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                    </div>
                                    <div class="input-group form-group{{ $errors->has('password') ? ' highlight_error' : '' }}">
                                        <input class="form-control" type="password" id="passwordfield" placeholder="Password*" name="password" required>
                                        <div class="input-group-addon">
                                            <span class="fa fa-eye float-right" id="fa-eye-p"aria-hidden="true" style=""></span>
                                        </div>
                                    </div>
                                    <div class="input-group form-group{{ $errors->has('password_confirmation') ? ' highlight_error' : '' }}">
                                        <input class="form-control" type="password" id="passwordconffield" placeholder="Confirm Password*" name="password_confirmation" required>
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
                            <button style="margin-left: 40%;" type="submit" class="btn btn-primary btn-green center">
                                <i class="fa fa-btn fa-user"></i>&nbsp;Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$("form").submit(function(e) {

    var ref = $(this).find("[required]");

    $(ref).each(function(){
        if ( $(this).val() == '' )
        {
            alert("Required field should not be blank.");

            $(this).focus();

            e.preventDefault();
            return false;
        }
    });  return true;
});
</script>
@endsection