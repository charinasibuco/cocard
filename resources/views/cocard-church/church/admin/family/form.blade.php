@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Family</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <a href="{{ url('organization/'. $slug .'/administrator/family') }}" class="btn btn-red float-right">
                        Cancel
                    </a>
                </h3>
            </div>
        </div>
        <div class="table-main well">
            <div class="this-div">
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
                        <input type="hidden" name="cb_val" value="" id="cb_val">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Name" name="name" value="{{ $name }}" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Description" name="description" value="{{ $description }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Primary Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control tel" placeholder="Primary Phone Number" name="primary_phone" value="{{ $primary_phone }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Secondary Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control tel" placeholder="Secondary Phone Number" name="secondary_phone" value="{{ $secondary_phone }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Primary Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" placeholder="Primary Email" name="primary_email" value="{{ $primary_email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Secondary Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" placeholder="Secondary Email" name="secondary_email" value="{{ $secondary_email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Address 1</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Address 1" name="address_1" value="{{ $address_1 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Address 2</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Address 2" name="address_2" value="{{ $address_2 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">City</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="City" name="city" value="{{ $city }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">State</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="State" name="state" value="{{ $state }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-2 control-label">Zipcode</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="Zipcode" name="zipcode" value="{{ $zipcode }}">
                            </div>
                        </div>
                        @if($action_name == "Add")
                        <div class="col-md-offset-2 col-md-9">
                            <div class="clearfix">
                                <div class="pull-right">
                                    <div id="cbdiv" class="form-group float-right">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <input type="checkbox" name="save_another" id="cbx">&nbsp;Save and Add Another
                                            </div><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {!! csrf_field() !!}
                            <div class="clearfix">
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-darkblue">
                                        Submit
                                    </button>
                                    <a href="{{ url('organization/'. $slug .'/administrator/family') }}" class="btn btn-red">
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
