<table class="table table-striped" id="tabledata">
	<thead class="theader">
		<th>Name</th>
		<th>Role</th>
		<th>Email</th>
		<th>Status</th>
		<th>Action</th>
	</thead>
	<tbody>
		@foreach($users as $user)
		<tr>
			<td>{{ $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name}}</td>
			<td>{{ $user->role->first()->title or "" }}</td>
			<td>{{ $user->email}}</td>
			<td>{{ $user->status }}</td>
			<td>
				<a href="{{ url('organization/' .$slug. '/administrator/users/edit/'.$user->id )}}">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-pencil fa-stack-1x"></i>
					</span>
				</a>
				<a href="" title="Assign Role" data-toggle="modal" data-target="#{{ $user->id  }}_role_modal">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-user fa-stack-1x"></i>
					</span>
				</a>
				<a href="{{ url('organization/' .$slug. '/administrator/users/delete/'.$user->id )}}">
					<span class="fa-stack fa-lg icon-delete">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-trash-o fa-stack-1x"></i>
					</span>
				</a>
				
			</td>
		</tr>
		<div id="{{ $user->id  }}_role_modal" class="modal fade" role="dialog">
		    <div class="modal-dialog">
		        <!-- Modal content-->
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal">&times;</button>
		                <h4 class="modal-title"><label for="status">Assign Role</label></h4>
		            </div>
		            <div class="modal-body">
		               <form action="{{ route('user_assign_role', $user->id) }}" method="post">
		               	  <select name="role" id="role" class="form-control">
		               	  	<option value="" selected disabled>-Choose Role-</option>
		               	  	@foreach(App\Role::all() as $role)
		               	  	<option value="{{ $role->id  }}">{{ $role->title }}</option>
		               	  	@endforeach
		               	  </select>
		               	   <input type="hidden" name="type" value="{{ $type }}">
		               	  <input type="hidden" name="slug" value="{{ $slug }}">
		               	  <br/>
		                    <button class="btn btn-primary" type="submit">Submit</button>
		                    {!! csrf_field() !!}
		               </form>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		            </div>
		        </div>

		    </div>
		</div>
		@endforeach
	</tbody>
</table>