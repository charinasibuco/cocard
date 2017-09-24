<form method="GET" action="">
	<div class="search form-group">
	    <div class="input-group" style="width: 500px;">
	        <span class="input-group-addon" id="basic-addon1">
	            <i class="fa fa-search"></i>
	        </span>
	        <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="{{ $search }}">
	        <span class="input-group-btn">
	            <button class="btn btn-darkblue" type="submit" style="z-index: 1; float: right;">@lang('dashboard_details.go')</button>
	        </span>
	    </div>
	 </div>
 </form>
<div class="table-responsive">
<table class="table table-striped" id="tabledata">
	<thead class="theader">
		<th>Name</th>
		<th>Birthdate</th>
		<th>Gender</th>
		<th>Roles</th>
		<th>Action</th>
	</thead>
	<tbody>
	@if(count($members) == 0)
	<tr>
		<td><i>No members to show.</i></td>
	</tr>
	@else
		@foreach($members as $row)
		<div class="delete-modal-container" data-id="{{$row->id}}">
		    <div class="modal-delete">
		        <div class="modal-header">
		            <h5>Member: Delete {{ $row->first_name}} {{ $row->last_name}}.</h5>
		        </div>
		        <?php
                	$assigned_roles = App\AssignedUserRole::where('user_id', $row->id)->where('status', 'Active')->get();
            	?>
		        <div class="modal-body">
		        	@if(count($assigned_roles) > 1)
		                <p><b>WARNING! This user has many roles.</b> Are you sure you want to delete this?</p>
		            @else
		                <p>Are you sure you want to delete this?</p>
		            @endif
		        </div>
		        <div class="modal-footer">
		            <div class="row">
		                <div class="col-md-6">
		                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ url('/organization/'.$slug.'/administrator/members/delete-member', $row->id) }}">
		                        YES
		                    </a>
		                </div>
		                <div class="col-md-6">
		                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$row->id}}">Cancel</button>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		<tr>
			<td style="max-width: 400px;"> {{ $row->first_name}} {{ $row->middle_name}} {{ $row->last_name}} </td>
			<td> {{ Carbon\Carbon::parse($row->birthdate )->format('n/d/Y') }} </td>
			<td> {{ $row->gender}} </td>
			<td>
				<ul>
                @foreach($assigned_roles as $assigned_role)
                    <?php
                        $role = App\Role::where('id', $assigned_role->role_id)->first();
                    ?>
                    <li>{{ strtolower($role->title) }}</li>
                @endforeach
                </ul>
			</td>
			<td>
				@can('view_member')
				<a href="{{ url('/organization/'.$slug.'/administrator/members/view-details/'.$row->id) }}" title="View Member Details">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-eye fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@can('edit_member')
				<a href="{{ url('/organization/'.$slug.'/administrator/members/edit-member', $row->id)}}" title="Modify Member Details">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-pencil fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@can('assign_role')
				<a href="" title="Assign Role" data-toggle="modal" data-target="#{{ $row->id  }}_role_modal">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-user fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@can('assign_family')
				<a href="" title="Assign to Family" @if(count($family_groups) == 0) style="pointer-events: none;" href="#" @else data-toggle="modal" data-target="#{{ $row->id  }}_family_modal" @endif>
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x" @if(count($family_groups) == 0) style="color: gray;" @endif></i>
						<i class="fa fa-users fa-stack-1x"></i>
					</span>
				</a>
				@endcan
				@can('delete_member')
				<a class="delete_modal" data-id="{{$row->id}}" style="cursor:pointer;">
					<span class="fa-stack fa-lg icon-delete">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-trash-o fa-stack-1x"></i>
					</span>
				</a>
				@endcan
			</td>
		</tr>
		<div id="{{ $row->id  }}_role_modal" class="modal fade" role="dialog">
		    <div class="modal-dialog">
		        <!-- Modal content-->
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal">&times;</button>
		                <h4 class="modal-title"><label for="status">Assign Role to {{ $row->first_name}}</label></h4>
		            </div>
		            <div class="modal-body">
		               <form action="{{ route('user_assign_role', $row->id) }}" method="post">
		               	  <select name="role" id="role" class="form-control">
		               	  	@foreach($roles as $role)
		               	  	<option value="{{ $role->id  }}"  @if($row->Role()->first()->id == $role->id))) selected @endif >{{ $role->title }}</option>
		               	  	@endforeach
		               	  </select>
		               	  <input type="hidden" name="slug" value="{{ $slug }}">
		               	  <input type="hidden" name="type" value="{{ $type }}">
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
		<div id="{{ $row->id  }}_family_modal" class="modal fade" role="dialog">
		    <div class="modal-dialog">
		        <!-- Modal content-->
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal">&times;</button>
		                <h4 class="modal-title"><label for="status">Assign {{ $row->first_name }} to a Family</label></h4>
		            </div>
		            <div class="modal-body">
		               <form action="{{ route('assign_to_family', $row->id) }}" method="post">
		               	  <select name="family_id" id="family_id" class="form-control">
		               	  	@foreach($family_groups as $family_group)

		               	  	<option value="{{ $family_group->id  }}">{{ $family_group->name }}</option>
		               	  	@endforeach
		               	  </select>
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
	@endif
	</tbody>
</table>
{{ $members->render() }}
</div>
