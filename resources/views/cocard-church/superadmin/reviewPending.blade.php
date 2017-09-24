@extends('layouts.app')
@include('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
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
        <h3 class="text-center">Review Organization Details</h3>
        <div class="well" style="width:auto;overflow:hidden;">
            <form class="form-horizon" role="form" method="POST" action="{{ route('pending_organization_pending_store',$id) }}">
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label>Organization:</label>
                        <input placeholder="Name of Organization" type="text" class="form-control" name="name" value="{{ $name }}">
                        @if ($errors->has('organization'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                        <label>Contact Person:</label>
                        <input placeholder="Contact Person" type="text" class="form-control" name="contact_person" value="{{ $contact_person }}">
                        @if ($errors->has('contact_person'))
                        <span class="help-block">
                            <strong>{{ $errors->first('contact_person') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role:</label>
                        <select class="form-control" name="position">
                            <option {{ ($position == 'Admin') ? 'selected="selected"' : ' '}} >Admin</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                        <label>Slug:</label>
                        <input placeholder="Url Link" type="text" class="form-control" name="url" value="{{ $url }}">
                        @if ($errors->has('url'))
                        <span class="help-block">
                            <strong>{{ $errors->first('url') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('contact_number') ? ' has-error' : '' }}">
                        <label>Contact number:</label>
                        <input placeholder="Contact Number" type="text" class="form-control tel" name="contact_number" value="{{ $contact_number }}">
                        @if ($errors->has('contact_number'))
                        <span class="help-block">
                            <strong>{{ $errors->first('contact_number') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label>Email:</label>
                        <input placeholder="Email Address" type="email" class="form-control" name="email" value="{{ $email }}">
                        @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <!--  <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <input placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation">
            @if ($errors->has('password_confirmation'))
            <span class="help-block">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
        </span>
        @endif
    </div> -->
    <div class="form-group ">
        <br>
        {!! csrf_field() !!}
        <button type="submit" class="btn btn-darkblue center">
            Submit
        </button>
    </div>
</form>
</div>
</div>

@endsection
