@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<ul class="nav donation-nav">
			<li class="active"><a href="{{ url('/organization/'.$slug.'/administrator/donation-category')}}">Donation Category</a></li>
			<li><a href="{{ url('/organization/'.$slug.'/administrator/donation-list')}}">Donation List</a></li>
		</ul>
		<br>
		@if(session('success'))
		<div class="alert alert-success alert-dismissable">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			{{ session("success") }}
		</div>
		@endif
		@if(session('failed'))
		<div class="alert alert-danger alert-dismissable">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			{{ session("failed") }}
		</div>
		@endif
		<div class="tab-content" style="margin-top:0;">
			@can('view_donation_category')
			<div id="donation_category" class="">
				<div class="row">
					<div class="col-md-6">
						<h3>List of Donation Category</h3>
					</div>
					<div class="col-md-6">
						<h3>
							<div class="clearfix">
								@can('add_donation_category')
								<a href="{{ url('/organization/'.$slug.'/administrator/donation/create-donation-category') }}" class="btn btn-darkblue float-right">
									Add Donation Category&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
								</a>
								@endcan
							</div>
						</h3>
					</div>
				</div>
				@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{{ Session::get('message') }}
				</div>
				@elseif(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					{{ Session::get('error') }}
				</div>
				@endif
				<div class="table-main panel panel-default">
				<div class="table-responsive">
					<table class="table table-striped" id="tabledata">
						<thead class="theader">
							<th><a title = "Sort by Name" href="{{url('/organization/'.$slug.'/administrator/donation-category').'?order_by=name&sort='. $sort}}">Name</a></th>
							<th><a title = "Sort by Description" href="{{url('/organization/'.$slug.'/administrator/donation-category').'?order_by=description&sort='. $sort}}">Description</a></th>
							<th>Status</th>
							<th>Action</th>
						</thead>
						<tbody>
							
							@foreach($donationCategory as $list)
							<?php
							$count = App\DonationList::where('donation_category_id',$list->id)->where('status','Active')->count();
							?>
							@if($count == 0)
								<div class="delete-modal-container" data-id="{{$list->id}}">
								    <div class="modal-delete">
								        <div class="modal-header">
								            <h5>Donation Category: Delete {{ $list->name }}.</h5>
								        </div>
								        
									        <div class="modal-body">
									            <p>Are you sure you want to delete this?</p>
									        </div>
									        <div class="modal-footer">
									            <div class="row">
									                <div class="col-md-6">
									                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('/organization/'.$slug.'/administrator/donation/delete-donation-category', $list->id) }}">
									                        YES
									                    </a>
									                </div>
									                <div class="col-md-6">
									                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$list->id}}">Cancel</button>
									                </div>
									            </div>
									        </div>								        
								    </div>
								</div>
							@else
								<div class="delete-modal-container" data-id="{{$list->id}}">
								    <div class="modal-delete">
								        <div class="modal-header">
								            <h5>Donation Category: Delete {{ $list->name }}.</h5>
								        </div>
								        <div class="modal-body"> 
								            <p style="color:#ff0000;">Warning! There @if($count == 1)is @endif @if($count >1 )are @endif{{$count}} @if($count == 1)donation @endif @if($count > 1)donations @endif under this category.
								            <br>Please reassign before deleting. </p>
								        </div>
								        <div class="modal-footer">
								            <div class="row align-center">
								                <div class="col-md-6">
								                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$list->id}}">Cancel</button>
								                </div>
								            </div>
								        </div>						        
								    </div>
								</div>
							@endif
							<tr>
								<td> {{ $list->name }} </td>
								<td> {{ $list->description }} </td>
								<td> {{ $list->status }} </td>
								<td>
									@can('edit_donation_category')
									<a href="{{ url('/organization/'.$slug.'/administrator/donation/edit-donation-category', $list->id)}}">
										<span class="fa-stack fa-lg icon-edit">
											<i class="fa fa-square fa-stack-2x"></i>
											<i class="fa fa-pencil fa-stack-1x"></i>
										</span>
									</a>
									@endcan
									@can('delete_donation_category')
									<a class="delete_modal" data-id="{{$list->id}}" style="cursor:pointer;">
										<span class="fa-stack fa-lg icon-delete">
											<i class="fa fa-square fa-stack-2x"></i>
											<i class="fa fa-trash-o fa-stack-1x"></i>
										</span>
									</a>
									@endcan
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					{!! $donationCategory->render() !!}
				</div>
				</div>
			</div>
			@endcan
		</div>
	</div>
</div>
@endsection
