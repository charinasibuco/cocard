@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Edit User Profile</h3>
            </div>
        </div>
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
        <form enctype="multipart/form-data" class="form-horizontal" role="form" method="POST" action="{{ $action }}">
            <div class="panel panel-primary panel-information" style="margin-bottom:10px;">
                <div class="panel-heading personal-information"><i class="fa fa-user" aria-hidden="true"></i> &nbsp;Personal Information</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-10">
                            <input type="hidden" class="form-control" name="slug" value="{{ ($organization)?$organization->url:''}}">
                            <input type="hidden" class="form-control" name="organization_id" value="{{ ($organization)?$organization->id:''}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">First Name</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="first_name" value="{{ $first_name }}" placeholder="Firstname">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Middle Name</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="middle_name" value="{{ $middle_name }}" placeholder="Middlename">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Last Name</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="last_name" value="{{ $last_name }}" placeholder="Lastname">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Birthdate</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                        <div class='input-group datepicker dtp'>
                                            <input type="text" class="form-control" value="{{ $birthdate }}" placeholder="M/D/YYYY">
                                            <input type="hidden" class="value" name="birthdate" value="{{ $birthdate }}">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Gender</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <select class="form-control" name="gender" value="{{ $gender }}">
                                        <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                                        <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Marital Status</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <select class="form-control" name="marital_status" value="{{ $marital_status }}">
                                        <option value="Single" @if($marital_status == 'Single') selected @endif>Single</option>
                                        <option value="Married" @if($marital_status == 'Married') selected @endif>Married</option>
                                        <option value="Divorced" @if($marital_status == 'Divorced') selected @endif>Divorced</option>
                                        <option value="Widowed/Widower" @if($marital_status == 'Widowed/Widower') selected @endif>Widowed/Widower</option>
                                        <option value="Committed" @if($marital_status == 'Committed') selected @endif>Committed</option>
                                        <option value="Not Specified" @if($marital_status == 'Not Specified') selected @endif>Not Specified</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Image</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="file" class="form-control" name="image" value="{{ $image }}" placeholder="Image">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Email</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="email" class="form-control" name="email" value="{{ $email }}" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Password</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="password" class="form-control" name="password" value="{{ $password }}" placeholder="******">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary panel-information">
                <div class="panel-heading personal-information"><i class="fa fa-phone" aria-hidden="true"></i> &nbsp;Contact Information</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-offset-1 col-md-10">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Address</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="address" value="{{ $address }}" placeholder="Address">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">City</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="city" value="{{ $city }}" placeholder="City">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">State</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="state" value="{{ $state }}" placeholder="State">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Zipcode</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control" name="zipcode" value="{{ $zipcode }}" placeholder="Zipcode">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Phone</label>
                                <div class="col-sm-offset-2 col-sm-8">
                                    <input type="text" class="form-control tel" name="phone" value="{{ $phone }}" placeholder="Phone">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! csrf_field() !!}
            <div>
                <a href="{{ url('organization/'.$slug.'/user/profile') }}" class="btn btn-darkblue float-right" style="margin-left:5px;">
                    <i class="fa fa-times" aria-hidden="true"></i>&nbsp;Cancel
                </a>
                <button type="submit" class="btn btn-darkblue float-right" id="save_button">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Save
                </button>
            </div>
            <br>
        </form>
    </div>
</div>
@endsection
