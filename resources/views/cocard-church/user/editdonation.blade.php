@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content">
  <div class="margin-mob-top">
    <div class="row">
      <div class="col-md-6">
        <h3>{{ $action_name }} Donation</h3>
      </div>
      <div class="col-md-6">
        <h3>
          <div class="clearfix">
            <a href="{{ url('organization/'. $slug .'/user/donation') }}" class="btn btn-darkblue float-right btn-right">
              <i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
            </a>
          </div>
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
                  <div class="form-group">
                      <label class="col-sm-2 control-label">Category</label>
                      <div class="col-sm-9">
                          <select class="form-control" name="donation_category_id" value="{{ $donation_category_id }}" disabled="disabled">
                              @foreach($donation_categories as $donation_category)
                              <option value="{{ $donation_category->name }}" @if($donation_category_id == $donation_category->id) selected @endif>{{ $donation_category->name }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">Name</label>
                      <div class="col-sm-9">
                          <select class="form-control" name="donation_list_id" value="{{ $donation_list_id }}" disabled="disabled">
                              @foreach($donation_lists as $donation_list)
                              <option value="{{ $donation_list->name }}" @if($donation_list_id == $donation_list->id) selected @endif>{{$donation_list->name}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">Frequency</label>
                      <div class="col-sm-9">
                          <select class="form-control" name="frequency_id" value="{{ $frequency_id }}" disabled="disabled">
                              @foreach($frequencies as $frequency)
                              <option value="{{ $frequency->title }}" @if($frequency_id == $frequency->id) selected @endif>
                              {{$frequency->title}}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                        <label class="col-sm-2 control-label">Amount</label>
                        <div class="col-sm-9">
                            <input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "20" type = "number" step="any" value="{{ $amount }}" name="amount" id="donationAmnt">
                        </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">Start date</label>
                      <div class="col-sm-4">
                          <div class='input-group startdp'>
                              <input value="{{$start_date}}" type="text" placeholder="From Date &amp; Time" class="form-control xsdate" name="start_date" disabled="disabled">
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">End date</label>
                      <div class="col-sm-4">
                          <div class='input-group enddp'>
                              <input value="{{$end_date}}" type="text" placeholder="To Date &amp; Time"class="form-control xedate" name="end_date" >
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                      </div>
                  </div>
                  {!! csrf_field() !!}
                  <button type="submit" class="btn btn-darkblue btn-lg this-button" style="width:60%;margin-right:20%;margin-left:20%;">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;Save
                  </button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection
