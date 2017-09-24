@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <h3 class="text-center">@lang('dashboard_details.add') @lang('dashboard.organization')</h3>
        @if(count($errors) > 0)
        <?php $required =  $errors->first()?>
        @if(strpos($required, 'required'))
        <div class="alert alert-warning alert-dismissible">@lang('messages.required_error') <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
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
        @include('cocard-church.organization.form')
    </div>
</div>
@endsection
