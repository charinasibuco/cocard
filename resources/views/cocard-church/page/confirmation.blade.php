<div class="delete-modal-container" data-id="{{$page->id}}">
    <div class="modal-delete">
        <div class="modal-header">
            <h5>Page: Delete {{$page->title}}.</h5>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this page?</p>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6">
                    <a class="btn btn-red btn-full"  style="color:#fff;background-color: #F05656;"  href="{{ route('page_delete', $page->id)}}">
                        YES
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="button" style="color:#fff;background-color: #012732;" class="btn btn-full cancel_page" data-id="{{$page->id}}">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
