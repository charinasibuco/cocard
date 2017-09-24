<div id="category_list" class="tab-pane fade">
	<div class="row">
		<div class="col-md-6">
			<h3>List of Donations</h3>
		</div>
		<div class="col-md-6">
			<h3>
				<div class="clearfix">
					@can('add_donation_list')
					<a href="{{ url('/organization/'.$slug.'/administrator/donation/create-donation-list') }}" class="btn btn-darkblue float-right">
						Add Donation List&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
					</a>
					@endcan
				</div>
			</h3>
		</div>
	</div>
	<div class="table-main panel panel-default">
	<div class="table-responsive">
		<table class="table table-striped" id="tabledata">
			<thead class="theader">
				<th><a title = "Sort by Category" href="{{url('/organization/'.$slug.'/administrator/donation').'?order_by=name&sort='. $sort.'#category_list'}}">Category</a></th>
				<th><a title = "Sort by Name" href="{{url('/organization/'.$slug.'/administrator/donation').'?order_by=name&sort='. $sort.'#category_list'}}">Name</a></th>
				<th style="width: 30%;"><a title = "Sort by Decription" href="{{url('/organization/'.$slug.'/administrator/donation').'?order_by=description&sort='. $sort.'#category_list'}}">Description</a></th>
				{{--<th>Recurring</th>--}}
				<th>Status</th>
				<th width="15%">Action</th>
			</thead>
			<tbody>
				@foreach($donationList as $list)
				<div class="delete-modal-container" data-id="{{$list->id}}">
				    <div class="modal-delete">
				        <div class="modal-header">
				            <h5>Donation List: Delete {{ $list->name }}.</h5>
				        </div>
				        <div class="modal-body">
				            <p>Are you sure you want to delete this?</p>
				        </div>
				        <div class="modal-footer">
				            <div class="row">
				                <div class="col-md-6">
				                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('/organization/'.$slug.'/administrator/donation/delete-donation-list', $list->id) }}">
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
					<td> {{ $list->donationCategory->name }}</td>
					<td> {{ $list->name }} </td>
					<td> {{ $list->description }} </td>
					{{--@if($list->recurring == 1)
						<td> Recurring Donation </td>
						@else
						<td> One-Time Donation </td>
						@endIf
					--}}
					<td> {{ $list->status }} </td>
					<td>
						@can('edit_donation_list')
						<a href="{{ url('/organization/'.$slug.'/administrator/donation/edit-donation-list', $list->id)}}">
							<span class="fa-stack fa-lg icon-edit">
								<i class="fa fa-square fa-stack-2x"></i>
								<i class="fa fa-pencil fa-stack-1x"></i>
							</span>
						</a>
						@endcan
						@can('delete_donation_list')
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
		{!! $donationList->fragment('category_list')->render() !!}
	</div>
	</div>
</div>
