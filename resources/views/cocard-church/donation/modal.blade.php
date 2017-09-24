<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="large-modal">
    <div class="modal-dialog modal-lg modal-md hidden-xs" role="document">
        <div class="modal-content">
            <div class="clearfix">
                <div class="float-right" style="padding: 15px;">
                    {{--add form--}}
                    <a class="btn btn-primary float-right" data-dismiss="modal" style="margin-left:5px;background-color:#ff3333;">
                        <i class="fa fa-times" aria-hidden="true"></i> Close
                    </a>
                    {{--<a href="{{ url('/organization/'.$slug.'/donations/payment-info') }}" class="btn btn-primary float-right payment_cart">
                        Go to Payment
                    </a>--}}

                    @if($cart != null )
                    @if($total != 0)
                    <a href="{{ url('/organization/'.$slug.'/api/billing') }}" class="btn btn-primary float-right payment_cart">
                        Go to Payment
                    </a>
                    @else
                    {{-- <a href="{{ url('/organization/'.$slug.'/user/transaction') }}"> --}}
                        <a href="{{ url('/organization/'.$slug.'/transaction?token='. md5(uniqid(rand(), true))) }}">
                            <button type="submit" class="btn btn-primary float-right payment_cart">Save Transaction</button>
                        </a>
                        @endif
                        <a id="delete_modal" style="margin-right:5px;" href="#" class="btn btn-primary float-right payment_cart delete_modal">
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
                <div class="cart" style="padding-top:10px;">
                    <h2 class="text-center">Cart</h2>
                    <div class="table-responsive" id="cart-table">
                        @if(count($cart) == 0)
                        <hr>
                        <h3 style="text-align:center">Cart is empty</h3>
                        <hr>
                        @endif
                        @if($user!= null)
                        <input type="hidden" class="form-control" name="userid" value="{{ $user->id }}" >
                        @else
                        <input type="hidden" class="form-control" name="userid" value="0" >
                        @endif
                        <div class="panel panel-primary" id="event_panel">
                            <div class="panel-heading">EVENT</div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Frequency</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Quantity</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Action</th>
                                            {{--<th>Occurence</th>--}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $event_sum = 0;
                                        $event_count = 0;
                                        ?>  
                                        @foreach($cart as $item)
                                        @if($item->type == 'event')
                                        <tr class="cart-display" data-id="{{ $item->getID() }}" >
                                            <td>{{ $item->getEventFrequency()}}</td>
                                            {{--  <td hidden="hidden">{{ $item->getID() }}</td> --}}
                                            <td>{{ $item->getEventName()->name}}</td>
                                            @if(($item->getAmount() =='') )
                                            <td>---</td>
                                            @else
                                            <td>${{ number_format($item->getAmount(),2,'.',',') }}</td>
                                            @endif
                                            @if(($item->getQty() == 0) )
                                            <td>---</td>
                                            @else
                                            <td>{{ $item->getQty()  }}</td>
                                            @endif
                                            <td>{{ $item->getStartDate() }}</td>
                                            <td>{{ $item->getEndDate() }}</td>
                                            <td>
                                                <a class="modify_cart_item" style="padding-right:10px;" title="Modify Item"  data-id="{{ $item->getID() }}">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a class="delete_cart_item" style="padding-right:10px;" data-id="{{ $item->getID() }}" title="Remove Item">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                            {{-- <td>{{ $item->getOccurence()  }}</td>--}}
                                        </tr>
                                        @include('cocard-church.cart.cart-confirmation')
                                        <?php
                                        $event_count += count($item->type);
                                        $event_sum += $item->getAmount();
                                        ?>
                                        @include('cocard-church.cart.cart-edit')
                                        @endif
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>Sub Total:</b></td>
                                            <td><b>${{ number_format($event_sum,2,'.',',')  }}</b></td>
                                        </tr>
                                        <input value="{{ $event_count }}" id="event_count" type="hidden">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-primary" id="donation_panel">
                            <div class="panel-heading">DONATION</div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Frequency</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Quantity</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $donation_sum = 0;
                                        $donation_count = 0;
                                        ?>
                                        @foreach($cart as $item)
                                        @if($item->type == 'donation')
                                        <tr class="cart-display" data-id="{{ $item->getID() }}" >
                                            @if(($item->getFrequencyId() =='' || $item->getFrequencyId() == 0 ) )
                                            <td>---</td>
                                            @else
                                            <td>{{ $item->frequency_title }}</td>
                                            @endif
                                            <td>{{ $item->donationList_title  }}</td>
                                            <td>${{ number_format($item->getAmount(),2,'.',',') }}</td>
                                            <td>---</td>
                                            @if(($item->getStartDate() =='' || $item->getStartDate() == '00-00-0000') )
                                            <td>---</td>
                                            @else
                                            <td>{{ $item->getStartDate() }}</td>
                                            @endif
                                            @if(($item->getEndDate() =='' || $item->getEndDate() == '00-00-0000') )
                                            <td>---</td>
                                            @else
                                            <td>{{ $item->getEndDate() }}</td>
                                            @endif
                                            <td>
                                                <a class="modify_cart_item" style="padding-right:10px;" title="Modify Item"  data-id="{{ $item->getID() }}">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                                <a class="delete_cart_item" style="padding-right:10px;" data-id="{{ $item->getID() }}" title="Remove Item">
                                                    <i class="fa fa-times" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @include('cocard-church.cart.cart-confirmation')
                                        <?php
                                        $donation_sum += $item->getAmount();
                                        $donation_count += count($item->type);
                                        ?>
                                        @include('cocard-church.cart.cart-edit')
                                        @endif
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><b>Sub Total:</b></td>
                                            <td><b>${{ number_format($donation_sum,2,'.',',')  }}</b></td>
                                        </tr>
                                        <input value="{{ $donation_count }}" id="donation_count" type="hidden">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @include('cocard-church.cart.empty-cart')
                    </div>
                    <br>
                    <p style="margin-left:40%"><b>Total Amount: ${{ number_format($total,2,'.',',')  }}</b></p>
                </div>
            </div>
        </div>
        @include('cocard-church.cart.mobile-cart')
    </div>

    @section('script')
    <script>
    $(document).ready(function() {


        $("#event_panel").hide();
        $("#donation_panel").hide();

        if($("#event_count").val() > 0){
            $("#event_panel").show();
        }

        if($("#donation_count").val() > 0){
            $("#donation_panel").show();
        }
    });
    </script>
    @endsection
