<div class="col-md-offset-3 col-md-6">
    <form method="POST" action="{!! url($form_url) !!}" id="ccform">
        <input type="hidden" id="slug_field" name="slug_field" value="{{ $slug }}">
        <h2 class="text-center">Payment Information</h2>
        <hr>
        <div class="well payment-info">
            <h5 class="text-center">Total amount</h5>
            <h4 class="text-center">${{ number_format($total,2,'.',',') }}</h4>
            <hr>
            <div class="form-group">
                <label for="">Credit Card Number</label>
                <input autofocus type="number" class="form-control" id="billing-cc-number" name="billing-cc-number" value="" placeholder="4111111111111111" required>
            </div>
            <div class="form-group">
                <label for="">Expiration Date</label>
                <input type="number" class="form-control" id="billing-cc-exp" name="billing-cc-exp" value="" placeholder="1012" required>
            </div>
            <div class="form-group">
                <label for="">CVV</label>
                <input type="number" class="form-control" id="cvv" name="cvv" value="" placeholder="123" required>
            </div>
        </div>
        <div class="row">
            {!! csrf_field() !!}
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-blue btn-full"><i class="fa fa-btn fa-user"></i> Process Payment</button>
            </div>
               @if($user && $user->first_name)
            <div class="col-md-6">
                <a href="{{ url('organization/'.$slug.'/user/donate', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
            </div>
            @else
            <div class="col-md-6">
                <a href="{{ url('organization/'.$slug.'/donations', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
            </div>
            @endif
        </div>
    </form>
</div>
        
