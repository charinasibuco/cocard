<div class="delete-modal-container cart_delete"  data-id="{{ $item->getID() }}">
    <div class="modal-delete ">
        <div class="modal-header">
            <h5>Cart: Remove {{ $item->donationList_title  }} from cart.</h5>
        </div>
        <div class="modal-body">
            Are you sure you want to delete this?
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red"  style="width:100%;background-color: #F05656;" href="{{url('/organization/'.$slug.'/donations/remove-cart-item',$item->getID())}}" data-id="{{ $item->getID() }}">
                        Delete
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-cancel-cart"  data-id="{{ $item->getID() }}">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
