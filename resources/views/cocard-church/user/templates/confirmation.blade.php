<div class="delete-modal-container" data-id="{{$item->id}}">
    <div class="modal-delete">
        <div class="modal-header">
            <h5>Users: Delete {{$item->first_name}}.</h5>
        </div>
        <?php
            $user_roles = App\AssignedUserRole::where('user_id', $item->id)->where('status', 'Active')->get();
        ?>
        <div class="modal-body">
            @if(count($user_roles) > 1)
                <p><b>WARNING! This user has many roles.</b> Are you sure you want to delete this?</p>
            @else
                <p>Are you sure you want to delete this?</p>
            @endif
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ $item->delete_url }}">
                        YES
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_modal" data-id="{{$item->id}}">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
