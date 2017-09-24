<div class="table-responsive">
<table class="table">
    <thead>
        <tr> @if (isset($staffs))
            <th><a title = "Sort by Name" href="{{url('/organization/'.$slug.'/administrator/staff').'?order_by=first_name&sort='. $sort}}">Name</a></th>
            @else
            <th>Name</th>
            @endif
            <th>Role</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        @include('cocard-church.user.templates.confirmation')
        <tr>
            <td>{{ $item->full_name }}</td>
            <?php
                $assigned_roles = App\AssignedUserRole::where('user_id', $item->id)->where('status', 'Active')->get();
            ?>
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
            <td>{{ $item->email or ""  }}</td>
            <td>
                @can('edit_staff')
                <a  href="{{ $item->edit_url }}"  title="Modify Staff Details">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-pencil fa-stack-1x"></i>
					</span>
				</a>
                @endcan
                @if (isset($staffs))
                <a title="View Roles" @if(count($assigned_roles) == 1) style="pointer-events: none;" @else data-toggle="modal" data-target="#{{ $item->id  }}_view_role_modal" href="{{ url('organization/'.$slug.'/administrator/staff/role-modal-delete/'.$item->id) }}" @endif>
                    <span class="fa-stack fa-lg icon-edit">
                        <i class="fa fa-square fa-stack-2x" @if(count($assigned_roles) == 1) style="color: gray;" @endif></i>
                        <i class="fa fa-users fa-stack-1x"></i>
                    </span>
                </a>
                <div id="{{ $item->id  }}_view_role_modal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                        </div>
                    </div>
                </div>
                @can('assign_role')
                <a href="" title="Assign Role" data-toggle="modal" data-target="#{{ $item->id  }}_role_modal">
                    <span class="fa-stack fa-lg icon-edit">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-user fa-stack-1x"></i>
                    </span>
                </a>
                @endcan
                @endif
                @if($item->id != 1)
                @can('delete_staff')
                <a class="delete_modal" data-id="{{$item->id}}" style="cursor:pointer;">
					<span class="fa-stack fa-lg icon-delete">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-trash-o fa-stack-1x"></i>
					</span>
				</a>
                @endcan
                @endif
            </td>
        </tr>
        @if (isset($staffs))
        <div id="{{ $item->id }}_role_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><label for="status">Assign Role to {{ $item->first_name }}</label></h4>
                    </div>
                    <div class="modal-body">
                       <form action="{{ route('user_assign_role', $item->id) }}" method="post">
                          <select name="role" id="role" class="form-control">
                            @foreach($roles as $role)
                            <option value="{{ $role->id  }}">{{ $role->title }}</option>
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
        @endif
        @endforeach
    </tbody>
</table>
</div>
@section('script')
<script>
$(document).ready(function() {
    var change_status_url = "{{ route('change_status')  }}";
    var token = "{{ csrf_token() }}";
    $(".staff-status").click(function(){
        var switch_ = $(this);
        var value = switch_.closest("td").find(".status-container").html();
        $.post(change_status_url,{id:switch_.data("staff_id"),status:value,_token:token}).done(function(data){
            switch_.closest("td").find(".status-container").html(data);
        });
    });
    $(".delete_modal").click( function (){
    var id = $(this).attr('data-id');
    $('.delete-confirmation[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-confirmation[data-id="' + id + '"]').css('display','none');
    });
});
</script>
@endsection
