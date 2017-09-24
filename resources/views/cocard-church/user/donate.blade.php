@extends('layouts.app')
@include('cocard-church.user.navigation')
@section('content')
<div class="d-content administrators donation">
    <div class="row">
        <div class="col-sm-12">
            @include('cocard-church.donation.modal')
            <div class="tab-content donation-layout">
                @if(Session::has('message'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <button type="button" class="btn btn-darkblue btn-block" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart <span class="badge badge-danger">{{count($cart)}}</span></button>
            </div>
        </div>
        <div class="col-sm-9">
            <form method="GET" action="">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="{{ $search }}">
                    <span class="input-group-btn">
                        <button class="btn btn-darkblue" type="submit">@lang('dashboard_details.go')</button>
                    </span>
                </div>
            </form>
        </div>
        <div class="col-sm-12">
            <div class="" style="margin-top: 10px;">
                <table class="table table-striped" style="margin-left: 0;width:100%;">
                    <thead>
                        <th>Donation Name</th>
                        <th>Description</th>
                        <th></th>
                    </thead>
                    @foreach($donationList as $q)
                    <tr>
                        <td>{{ $q->name }}</td>
                        <td>{{ $q->description }}</td>
                        <td><a href="{{ url('/organization/'.$slug.'/user/donate/fund',$q->id) }}" class="btn btn-darkblue">Donate</a></td>
                    </tr>
                    @endforeach
                </table>
                {{ $donationList->render() }}
            </div>
        </div>  
    </div>
</div>
@endsection
@section("script")
@if(!empty(Session::get('error_code')) && Session::get('error_code') == 5)
<script type="text/javascript">
$(function() {
    $('#large-modal').modal('show');
});
</script>
@endif
@endsection
