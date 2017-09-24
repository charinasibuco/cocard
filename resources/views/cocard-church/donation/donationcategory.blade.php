<div id="donation_category" class="tab-pane fade in active">
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
				<th><a title = "Sort by Name" href="{{url('/organization/'.$slug.'/administrator/donation').'?order_by=name&sort='. $sort}}">Name</a></th>
				<th><a title = "Sort by Description" href="{{url('/organization/'.$slug.'/administrator/donation').'?order_by=description&sort='. $sort}}">Description</a></th>
				<th>Status</th>
				<th>Action</th>
			</thead>
			<tbody>
				@foreach($donationCategory as $list)
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
