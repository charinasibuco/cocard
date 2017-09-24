<div class="table-responsive">
<table class="table table-striped" id="tabledata">
	<thead class="theader">
		<th><a title = "Sort by Title" href="{{url('/organization/'.$slug.'/administrator/role').'?order_by=title&sort='. $sort}}">Title</a></th>
		<th><a title = "Sort by Description" href="{{url('/organization/'.$slug.'/administrator/role').'?order_by=description&sort='. $sort}}">Description</th>
		<th>Action</th>
	</thead>
	<tbody>
		@foreach($roles as $role)
		<div class="delete-modal-container" data-id="{{$role->id}}">
		    <div class="modal-delete">
		        <div class="modal-header">
		            <h5>Role: Delete {{ $role->title }} .</h5>
		        </div>
		        <?php
		        	$user_roles = App\AssignedUserRole::where('role_id', $role->id)->where('assigned_user_roles.status', 'Active')->join('users', 'users.id', '=', 'assigned_user_roles.user_id')->where('users.status', 'Active')->get();
		        ?>
		        @if(count($user_roles) > 0)
		        <div class="modal-body">
		            <p style="margin-left: 80px; margin-right: 80px;"><b>Warning:</b> There are users attached to this role.</p>
		        </div>
		        <div class="modal-footer">
		            <div class="row">
		                <div class="col-md-6">
		                    <a class="btn btn-red btn-full"  style="color:#fff; background-color: gray; pointer-events: none;" href="#">
		                        YES
		                    </a>
		                </div>
		        @else
		        <div class="modal-body">
		            <p>Are you sure you want to delete this role?</p>
		        </div>
		        <div class="modal-footer">
		            <div class="row">
		                <div class="col-md-6">
		                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" href="{{ url('/organization/'.$slug.'/administrator/role/delete/'.$role->id) }}">
		                        YES
		                    </a>
		                </div>
		        @endif
		                <div class="col-md-6">
		                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$role->id}}">Cancel</button>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<tr>
			<td> {{ $role->title }} </td>
			<td> {{ $role->description }} </td>
			<td>
				@if($role->title != "administrator")
				@can('update_role_permission')
				<a href="{{ url('organization/'. $slug .'/administrator/role/edit/'.$role->id)}}">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-pencil fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@can('delete_role_permission')
				<a class="delete_modal" data-id="{{$role->id}}" style="cursor:pointer;">
					<span class="fa-stack fa-lg icon-delete">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-trash-o fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@endif
			</td>
		</tr>
		@endforeach
	</tbody>
</table>
</div>
