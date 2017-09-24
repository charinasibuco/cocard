@extends('layouts.app')
@include('includes.navigation')
@section('content')
<div class="bg-banner">
    <div class="container">
        <div class="col-md-offset-2 col-md-8">
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
            <div class="transparent-box-reg">
                <h2 class="text-center">Contact Us</h2><br>
                <div class="col-md-offset-1 col-md-10">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('pending_organization_store') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <input placeholder="Name of Organization" type="text" class="form-control" name="name" value="{{ old('name') }}">
                                        @if ($errors->has('name'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                        <input placeholder="Contact Person" type="text" class="form-control" name="contact_person" value="{{ old('contact_person') }}">
                                        @if ($errors->has('contact_person'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('position') ? ' has-error' : '' }}">
                                        <select class="form-control" name="position">
                                            <option>Admin</option>
                                        </select>
                                            @if ($errors->has('position'))
                                        <span class="help-block">

                                        </span>
                                            @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                                        <input placeholder="Url Link" type="text" class="form-control" name="url" value="{{ old('url') }}">
                                        @if ($errors->has('url'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group{{ $errors->has('contact_number') ? ' has-error' : '' }}">
                                        <input placeholder="Contact Number" type="text" class="form-control tel" name="contact_number" value="{{ old('contact_number') }}">
                                        @if ($errors->has('contact_number'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input placeholder="Email Address" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                        @if ($errors->has('email'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input placeholder="Password" type="password" class="form-control" name="password">
                                        @if ($errors->has('password'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <input placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation">
                                        @if ($errors->has('password_confirmation'))
                                        <span class="help-block">

                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <br>
                            {!! csrf_field() !!}
                            <button type="submit" class="btn btn-primary btn-green center">
                                <i class="fa fa-btn fa-user"></i>&nbsp;Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
