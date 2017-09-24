<div class="delete-modal-container">
    <div class="modal-delete ">
        <div class="modal-header">
            <h5>Cart: Empty Cart.</h5>
        </div>
        <div class="modal-body">
            Are you sure you'd like to empty your cart?
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red"  style="width:100%;background-color: #F05656;" href="{{ url('/organization/'.$slug.'/donations/clear-cart-item') }}">
                        YES
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-full hide_modal" >Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
