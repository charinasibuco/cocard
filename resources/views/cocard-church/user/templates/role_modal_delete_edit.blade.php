        <?php
            $assigned_role_staffs = App\AssignedUserRole::where('user_id', $item_id)->where('role_id', '!=', '3')->where('status', 'Active')->get();
            $user = App\User::where('id', $item_id)->first();
        ?>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><label for="status">{{ $user->first_name }}'s Roles</label></h4>
                    </div>
                    <div class="modal-body" style="padding-left: 30px;">       
                        <table style="width: 530px;">
                            <thead> 
                                <tr>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $x = 0;
                                ?>
                                @foreach($assigned_role_staffs as $assigned_role_staff)
                                <?php
                                    $role = App\Role::where('id', $assigned_role_staff->role_id)->first();
                                ?>
                                <tr>
                                    <td>{{ strtolower($role->title) }}</td>
                                    <td>
                                        <a class="delete_modal" data-id="{{$x}}" style="cursor:pointer;">
                                        <span class="fa-stack fa-lg icon-delete">
                                            <i class="fa fa-square fa-stack-2x"></i>
                                            <i class="fa fa-trash-o fa-stack-1x"></i>
                                        </span>
                                        </a>
                                    </td>
                                </tr>
                                <div class="delete-modal-container" data-id="{{$x}}">
                                    <div class="modal-delete">
                                        <div class="modal-header">
                                            <h5>Roles: Delete {{$role->title}}.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;" id="{{ $x }}_delete_role_id" onclick="deleteRole(this)" data-url="{{ route('delete_user_role') }}" data-user_id="{{ $assigned_role_staff->user_id }}" data-role_id="{{ $assigned_role_staff->role_id }}">
                                                        YES
                                                    </a>
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$x}}">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    $x++;
                                ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>

<script>
$(document).ready(function() {
    deleteRole = function(e){
        var user = new Object();
        var url = $('#'+e.id).data('url');
        var user_id = $('#'+e.id).data('user_id');
        var role_id = $('#'+e.id).data('role_id');



        $.post(url,{user_id:user_id, role_id:role_id}).done(function(){
            alert('Deleted Role Successfully!');
            window.location.reload();
        });
    }

    $(".delete_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','none');
    });
});
</script>
