@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $action_name }} Email Group</h3>
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
                @if(Session::has('messagess'))
                <div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  {{ Session::get('messagess') }}
                </div>
                 @endif
                <div class="row">
                    <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="{{ $action }}">
                        <input type="hidden" class="form-control" name="slug" value="{{ $organization->url }}">
                        <input type="hidden" name="cb_val" value="" id="cb_val">
                        <input type="hidden" name="egid" value="{{$id}}" id="dcid">
                        <div class="form-group">
                           <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                           <div class="col-sm-9">
                               <input type="text" class="form-control" name="name" value="{{ $name }}">
                           </div>
                       </div>
                       <div class="form-group">
                           <label for="inputPassword3" class="col-sm-2 control-label">Details</label>
                           <div class="col-sm-9">
                               <input type="text" class="form-control" name="details" value="{{ $details }}">
                           </div>
                       </div>
                       @if($id >0)
                       @else
                       <div id="cbdiv" class="form-group float-right" style="margin-right:9%;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="save_another" id="cbx" @if(Session::has('messagess') || $cb_val == 1) checked="checked" @endif>&nbsp;Save and Add Another
                                </div><br>
                            </div>
                        </div>
                        <br><br>
                        @endif
                        {!! csrf_field() !!}
                        <div class="clearfix" style="margin-right: 9%;">
                            <div class="pull-right">
                                    <button type="submit" id="event-submit" class="btn btn-darkblue ">
                                        Submit
                                    </button>
                                    <a style="margin-right:10px;" href="{{ url('/organization/'.$slug.'/administrator/email-group') }}" class="btn btn-red ">
                                        Cancel
                                    </a>
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
  if($('#cbx').prop("checked") == true){
        document.getElementById("cb_val").value = "1";
  }
});
</script>
@endsection
