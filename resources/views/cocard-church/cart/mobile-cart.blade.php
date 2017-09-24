<!-- ///////mobile cart -->
<div class="modal-dialog modal-xs hidden-lg hidden-md hidden-sm" role="document">
    @if(Session::has('message'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('message') }}
    </div>
    @elseif(Session::has('error'))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('error') }}
    </div>
    @endif
    <div class="modal-content">
        <div class="top-cart" style="height:auto;">
            <div class="row">
                <div class="col-md-6">
                    <h2>Cart</h2>
                </div>
                <div class="col-md-6">
                    <div class="clearfix">
                        <a class="btn float-right" data-dismiss="modal" style="margin-left:5px;background-color:transparent;">
                            &times;
                        </a>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                @if($cart != null )
                    @if($total != 0)
                    <a href="{{ url('/organization/'.$slug.'/api/billing') }}" class="btn btn-primary float-left payment_cart">
                        Go to Payment
                    </a>
                    @else
                    <a href="{{ url('/organization/'.$slug.'/user/transaction?token='. md5(uniqid(rand(), true))) }}">
                        <button type="submit" class="btn btn-primary float-right payment_cart">Save Transaction</button>
                    </a>
                    @endif
                    <a id="delete_modal_mobile" style="margin-right:5px;" href="#" class="btn btn-primary float-right payment_cart delete_modal">
                        Clear Cart
                    </a>
                @else
                    <a href="{{ url('/organization/'.$slug.'/api/billing') }}" class="btn btn-primary float-right payment_cart" disabled>
                        Go to Payment
                    </a>
                    <a style="margin-right:5px;" href="{{ url('/organization/'.$slug.'/donations/clear-cart-item') }}" class="btn btn-primary float-right payment_cart" disabled>
                        Clear Cart
                    </a>
                @endif
            </div>

        </div>
        <div class="cart">
            @foreach($cart as $item)
            <div class="inner-cart">
                <div class="row">
                    <table class="cart-mobile-modal">
                        @if($item->type == 'donation')
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Frequency: </td>
                            <td>
                                @if(($item->getFrequencyId() =='' || $item->getFrequencyId() == 0 ) )
                                <b>---</b>
                                @else
                                <b>{{ $item->frequency_title }}</b>
                                @endif
                            </td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Category: </td>
                            <td>{{ $item->donationList_title  }}</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Amount: </td>
                            <td>${{ number_format($item->getAmount(),2,'.',',') }}</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Quantity: </td>
                            <td>---</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Start date: </td>
                            @if(($item->getStartDate() =='' || $item->getStartDate() == '00-00-0000') )
                            <td>---</td>
                            @else
                            <td>{{ $item->getStartDate() }}</td>
                            @endif
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>End Date: </td>
                            @if(($item->getEndDate() =='' || $item->getEndDate() == '00-00-0000') )
                            <td>---</td>
                            @else
                            <td>{{ $item->getEndDate() }}</td>
                            @endif
                        </tr>
                        @endif
                        @if($item->type == 'event')
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Frequency: </td>
                            <td>---</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Category: </td>
                            <td>{{ $item->getEventName()->name}}</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Amount: </td>
                            @if(($item->getAmount() =='') )
                            <td>---</td>
                            @else
                            <td>${{ number_format($item->getAmount(),2,'.',',') }}</td>
                            @endif
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Quantity: </td>
                            @if(($item->getQty() == 0) )
                            <td>---</td>
                            @else
                            <td>{{ $item->getQty()  }}</td>
                            @endif
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>Start date: </td>
                            <td>{{ $item->getStartDate() }}</td>
                        </tr>
                        <tr class="cart-display" data-id="{{ $item->getID() }}">
                            <td>End Date: </td>
                            <td>{{ $item->getEndDate() }}</td>
                        </tr>
                        @endif
                        <form method="get" action="{{url('/organization/'.$slug.'/donations/update-cart-item',$item->getID())}}">
                        @include('cocard-church.cart.mobile-cart-edit')
                        </form>
                    </table>
                    <div style="margin:10px;text-align:center;" class="cart-display cart-buttons" data-id="{{ $item->getID() }}">
                        <a class="modify_cart_item btn btn-primary btn-sm" title="Modify Item"  data-id="{{ $item->getID() }}">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Modify
                        </a>
                        <a class="delete_cart_item btn btn-red btn-sm" style="background-color: #F05656;" data-id="{{ $item->getID() }}" title="Remove Item" >
                            <i class="fa fa-times" aria-hidden="true"></i> Remove
                        </a>
                    </div>
                    @include('cocard-church.cart.delete-confirmation')
                </div>
            </div>
            <div class="modal fade confirmdel" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                <div class="modal-dialog modal-md hidden-xs" role="document">
                    <div class="modal-content">
                        <div class="cart">
                            <h2 class="text-center">Are you sure you want to remove this donation?</h2>
                            <div class="float-right">
                                {{--add form--}}
                                <a class="btn btn-primary float-right" style="margin-left:5px;background-color:#ff3333;">
                                    <i class="fa fa-times" aria-hidden="true"></i> No
                                </a>
                                <a href="{{url('/organization/'.$slug.'/donations/remove-cart-item',$item->getID())}}" class="btn btn-primary float-right">
                                    Yes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div style="text-align:center;background-color:#e6e6e6;border-radius:10px;">
                <br>
                <p><b>Total Donation: {{ number_format($total, 2) }}</b></p>
                <br>
            </div>
        </div>
    </div>
</div>
