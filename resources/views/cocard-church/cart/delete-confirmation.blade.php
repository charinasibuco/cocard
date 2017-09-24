<div class="modal modal-delete cart_delete" data-id="{{ $item->getID() }}">
    <div class="modal-body">
        Delete item?
    </div>
    <div class="modal-footer">
        <div class="col-md-6">
            <a class="btn btn-primary" style="width:100%;" href="{{url('/organization/'.$slug.'/donations/remove-cart-item',$item->getID())}}" data-id="{{ $item->getID() }}">
                Delete
            </a>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-red btn-cancel-cart" data-id="{{ $item->getID() }}">Cancel</button>
        </div>
    </div>
</div>
<div class="delete-modal-container">
    <div class="delete-confirmation">
        <div class="alert alert-danger alert-dismissable">
            Are you sure you'd like to empty your cart?
        </div>
        <hr>
        <a href="{{ url('/organization/'.$slug.'/donations/clear-cart-item') }}">
            YES
        </a>
        <a class="hide_modal">
            CANCEL
        </a>
    </div>
</div>
