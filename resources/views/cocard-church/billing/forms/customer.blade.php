@inject('countries','Acme\Helper\Country')
<div class="col-md-offset-3 col-md-6">
    <form method="POST" action="">
        <input type="hidden" name="DO_STEP_1" value="true">
        <input type="hidden" id="slug_field" name="slug_field" value="{{ $slug }}">
        <h2 class="text-center">Customer Information</h2>
        <hr>

        @if (count($errors) > 0)
        <div class="alert alert-danger" role="alert">
            <span class="sr-only">Error:</span> Please fill in the required fields below.
        </div>
        @endif

        <div class="well payment-info">            
            <div class="form-group @if($errors->has('billing-address-company')) has-error @endif">
                <label for="">Company</label>
                <input type="text" class="form-control" id="" name="billing-address-company" value="{{ old('billing-address-company') }}" placeholder="">
            </div>
            <div class="form-group @if($errors->has('billing-address-first-name')) has-error @endif">
                <label for="">First Name*</label>
                @if($user && $user->first_name)
                <input type="text" class="form-control" id="" name="billing-address-first-name" value="{{ $user->first_name }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-first-name" value="{{ old('billing-address-first-name') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-last-name')) has-error @endif">
                <label for="">Last Name*</label>
                @if($user && $user->last_name)
                <input type="text" class="form-control" id="" name="billing-address-last-name" value="{{ $user->last_name }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-last-name" value="{{ old('billing-address-last-name') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-address1')) has-error @endif">
                <label for="">Address*</label>
                @if($user && $user->address)
                <input type="text" class="form-control" id="" name="billing-address-address1" value="{{ $user->address }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-address1" value="{{ old('billing-address-address1') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group">
                <label for="">Address 2</label>
                <input type="text" class="form-control" id="" name="billing-address-address2" value="{{ old('billing-address-address2') }}" placeholder="">
            </div>
            <div class="form-group @if($errors->has('billing-address-city')) has-error @endif">
                <label for="">City*</label>
                @if($user && $user->city)
                <input type="text" class="form-control" id="" name="billing-address-city" value="{{ $user->city }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-city" value="{{ old('billing-address-city') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-zip')) has-error @endif">
                <label for="">Zip/Postal</label>
                @if($user && $user->zipcode)
                <input type="text" class="form-control" id="" name="billing-address-zip" value="{{ $user->zipcode }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-zip" value="{{ old('billing-address-zip') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-country')) has-error @endif">
                <label for="">Country</label>
                <select id="billing-address-country" name="billing-address-country" class="form-control">
                    @foreach($countries::all() as $country => $code)
                        <option value="{{ $code }}" @if((old('billing-address-country')==$code)) selected @endif>{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group @if($errors->has('billing-address-state')) has-error @endif">
                <label for="">State/Province*</label>
                @if($user && $user->state)
                <input type="text" class="form-control" id="" name="billing-address-state" value="{{ $user->state }}" placeholder="" required>
                @else
                <input type="text" class="form-control" id="" name="billing-address-state" value="{{ old('billing-address-state') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-phone')) has-error @endif">
                <label for="">Phone Number*</label>
                @if($user && $user->phone)
                <input type="text" class="form-control tel" id="" name="billing-address-phone" value="{{ $user->phone }}" placeholder="" required>
                @else
                <input type="text" class="form-control tel" id="" name="billing-address-phone" value="{{ old('billing-address-phone') }}" placeholder="" required>
                @endif
            </div>
            <div class="form-group @if($errors->has('billing-address-fax')) has-error @endif">
                <label for="">Fax Number</label>
                <input type="text" class="form-control" id="" name="billing-address-fax" value="{{ old('billing-address-fax') }}" placeholder="">
            </div>
            <div class="form-group @if($errors->has('billing-address-email')) has-error @endif">
                <label for="">Email Address*</label>
                @if($user && $user->email)
                <input type="text" class="form-control" id="" name="billing-address-email" value="{{ $user->email }}" placeholder="" required>
                @else
                <input type="email" class="form-control" id="" name="billing-address-email" value="{{ old('billing-address-email') }}" placeholder="" required>
                @endif
            </div>
        </div>    
        <div class="row">
            {!! csrf_field() !!}
            <div class="col-lg-6 col-sm-7">
                <button type="submit" class="btn btn-primary btn-blue btn-full"><i class="fa fa-btn fa-user"></i> Proceed to Payment</button>
            </div>
            @if($user && $user->first_name)
            <div class="col-lg-6 col-sm-5">
                <a href="{{ url('organization/'.$slug.'/user/donate', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
            </div>
            @else
            <div class="col-lg-6 col-sm-5">
                <a href="{{ url('organization/'.$slug.'/donations', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
            </div>
            @endif
        </div>
    </form>
</div>