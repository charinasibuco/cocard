<div class="delete-modal-container activate-confirmation" data-id="{{$row->id}}">
    <div class="modal-delete">
        <div class="modal-header">
            <h5>Organization: Activate {{ $row->name }}.</h5>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to activate this organization?</p>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ route('pending_organization_update_active',$row->id) }}">
                        YES
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_activate" data-id="{{$row->id}}">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="delete-modal-container deactivate-confirmation" data-id="{{$row->id}}">
    <div class="modal-delete">
        <div class="modal-header">
            <h5>Organization: Deactivate {{ $row->name }}.</h5>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to deactivate this organization?</p>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ route('pending_organization_update_inactive',$row->id) }}">
                        YES
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full hide_deactivate" data-id="{{$row->id}}">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
