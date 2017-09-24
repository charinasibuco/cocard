@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content" id="settings">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Settings</h3>
            </div>
            <div class="col-md-5">
                <h3>
                    <a href="{{ url('/organization/'.$slug.'/administrator/restore/'.$id)}}" class="btn btn-darkblue float-right"> Restore Defaults</a>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
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
            @include('cocard-church.organization.modify')
        </div>
    </div>
</div>
@endsection
