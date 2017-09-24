{{-- Hi {{ $res->billing->{'first-name'} }} --}}
<?php
    $transaction_id = App\Transaction::where('transaction_key', $data['trans_id'])->first();
    $transaction_details = App\TransactionDetails::where('transaction_id', $transaction_id->id)->get();
    $user =App\User::where('email', $data['user_email'])->first();
    $event = App\TransactionDetails::where('transaction_id', $transaction_id->id)->where('event_id', '!=', '0')->get();
?>
Hi {{ (Auth::user()) ? $user->first_name : $data['email'] }},
<br/>
<p>We appreciate your generosity.</p><br/>
<p>Below is the details of your transaction.</p>
<h4 style="text-align:center; text-transform:uppercase"><b>{{ $data['organization_name'] }}</b></h4>
<h5 style="text-align:center; line-height:1px"><b>{{ $data['organization_contact_number'] }}</b></h5>
<h5 style="text-align:center; line-height:1px"><b>{{ $data['organization_email'] }}</b></h5>       
<h5 style="text-align:center; text-transform:uppercase; line-height:1px"><b>Transaction ID: {{ $data['trans_id'] }}</b></h5>
<h5 style="text-align:center; text-transform:uppercase; line-height:1px"><b>Date:{{ $data['date_now_to_email'] }}</b></h5>
@if($request->event_count > 0)
    <h3 style="width:100%; display:inline-block; padding-left:190px;">Event</h3>
    @for($x = 0; $x < $request->event_count; $x++)
        <div style="background:#ffffff; width:100%; height:auto; padding-left:150px; padding-right:150px;">
        <ul style="list-style-type:none;">
           {{--  <li style="width:100%;"><div style="width:50%; display:inline-block">Code:</div><div style="width:50%; display:inline-block">-----</div> </li> --}}
       @if(isset( $event[$x]->Event))
            <li style="width:100%;"><div style="width:50%; display:inline-block">Authorization Code:</div><div style="width:50%; display:inline-block">{{ (isset($request->event_product[$x])?$request->event_product[$x]:'---') }}</div> </li>
            <li style="width:100%;"><div style="width:50%; display:inline-block">Event Name:</div><div style="width:50%; display:inline-block">{{(isset($request->event_name[$x])?$request->event_name[$x]:'---')}}</div> </li>
            <li style="width:100%;"><div style="width:50%; display:inline-block">Event Description:</div><div style="width:50%; display:inline-block">{{(isset($request->event_description[$x])?$request->event_description[$x]:'---')}}</div> </li>
            <li style="width:100%;"><div style="width:50%; display:inline-block">Event Fee:</div><div style="width:50%; display:inline-block">${{(isset($request->fee[$x])?$request->fee[$x]:'---')}}</div> </li>

            {{-- <li style="width:100%;"><div style="width:50%; display:inline-block">Qty:</div><div style="width:50%; display:inline-block">{{ $product[$x]->{'total-amount'} /  $transaction_details[$x]->Event->fee}}</div> </li> --}}
            <li style="width:100%;"><div style="width:50%; display:inline-block">Qty:</div><div style="width:50%; display:inline-block">{{ (isset($request->qty[$x])?$request->qty[$x]:'---')}}</div> </li>
            <li style="width:100%;"><div style="width:50%; display:inline-block">Event Start Date:</div><div style="width:50%; display:inline-block">{{(isset($request->event_start_date[$x])? $request->event_start_date[$x] : '---')}}</div> </li>
            <li style="width:100%;"><div style="width:50%; display:inline-block" >Event End Date:</div><div style="width:50%; display:inline-block">{{(isset($request->event_end_date[$x])? $request->event_end_date[$x] : '---')}}</div> </li>
            @if($request->recurring[$x] > 0)
               <li style="width:100%;"><div style="width:50%; display:inline-block">Event No. of Repetition:</div><div style="width:50%; display:inline-block">{{ (isset($request->no_of_repetition[$x])?$request->no_of_repetition[$x]:'---') }}</div> </li> 
               <li style="width:100%;"><div style="width:50%; display:inline-block">Event Recurring End Date:</div><div style="width:50%; display:inline-block">{{ (isset($request->recurring_end_date[$x])?$request->recurring_end_date[$x]:'---') }}</div> </li> 
               <li style="width:100%;"><div style="width:50%; display:inline-block">Recurring Event Frequency:</div><div style="width:50%; display:inline-block">
               @if($request->recurring[$x] == 1)
                Weekly
                @elseif($request->recurring[$x] == 2)
                Monthly
                @elseif($request->recurring[$x] == 3)
                Yearly
                @else
                -----
                @endif
                </li>
            @endif
            <li style="width:100%;"><div style="width:50%; display:inline-block">Amount:</div><div style="width:50%; display:inline-block"><b>${{ (isset($request->event_total[$x])?$request->event_total[$x]:'---') }}</b></div></li>
            <li style="width:100%;">
                <hr>
            </li>
        </ul>
        @endif
    </div>
    @endfor
@endif
<div style="width:50%; display:inline-block; padding-left:190px;"><b>Total Amount:</b></div><div style="margin-left:-175px; display:inline-block"><b>${{ $data['amount'] }}</b></div>
</div>
@section('script')
    <script>
    $("#event_panel").hide();
    $("#donation_panel").hide();

        if($("#event_count").val() > 0){
            // $("#event_panel").show();
            $("#event_panel").css('display', 'block');
        }

        if($("#donation_count").val() > 0){
            $("#donation_panel").css('display', 'block');
        }
    </script>
@endsection
        
        

