<form method="get" action="{{url('/organization/'.$slug.'/donations/update-cart-item',$item->getID())}}">
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>
            @if($item->getFrequencyId() == 0 )
            ---
            @else
            <select class="form-control" name="frequency_id">
                @foreach($frequency as $row)
                <option value="{{ $row->id }}" {{ ($item->frequency_id == $row->id) ? 'selected="selected"' : ' '}} >{{ $row->title }}</option>
                @endforeach
            </select>
            @endif
        </td>
        <td>
            @if($item->type == "donation")
            <select class="form-control" name="donation_category_id">
                @foreach($donation_list as $row)
                <option @if($item->getDonationCategoryId() == $row->id) selected @endif value="{{ $row->id }}" >{{ $row->name }}</option>
                @endforeach
            </select>
            @else
              {{ $item->getEventName()->name}}
            @endif
        </td>
        <td>
            @if($item->type == 'donation')
            <input type="number" class="form-control" step="any" name="amount" value="{{ number_format($item->getAmount(),2,'.','')}}" onchange="checkDec(this)">
            @else
            <input type="hidden" class="form-control" step="any" name="amount" value="{{ number_format($item->getAmount(),2,'.','')}}">
            <input type="hidden" class="form-control" step="any" name="type" value="event">
            <input type="hidden" class="form-control" step="any" name="prev_qty" value="{{$item->getQty()}}">
            {{ number_format($item->getAmount(),2,'.','')}}
            @endif
        </td>
        <td>
            @if($item->type == 'donation')
                ---
            @else
            <?php
            $event = App\Event::where('id',$item->event_id)->first();
            $available = $event->capacity - $event->pending;
            ?>
            <input type="hidden" value="{{ $available }}" id="available{{ $item->id }}">
            <input type="number" id="qty{{ $item->id }}" class="form-control" step="any" name="qty" value="{{ $item->getQty() }}">
            @endif
        </td>
        <td>
            @if($item->getFrequencyId() == 0 )
                @if(($item->getDonationCategoryId() == 0) )
                {{ $item->getStartDate() }}
                <input type="hidden" placeholder="From Date &amp; Time" class="form-control xsdate" name="start_date" value="{{ $item->getStartDate() }}" required>
                @else
                ---
                @endif
            @else
                <div class="input-group startdp">
                    <input type="text" placeholder="From Date & Time" class="form-control" name="start_date" id="start_date" value="{{ $item->getStartDate() }}"required>
                    <span class="input-group-addon">
                        <span class="glyphicon-calendar glyphicon"></span>
                    </span>
                </div>
                <!-- <input type="text" placeholder="From Date &amp; Time" class="form-control xsdate" name="start_date" value="{{ $item->getStartDate() }}" required> -->
            @endif
        </td>
        <td>
            @if($item->getFrequencyId() == 0 )
            @if(($item->getDonationCategoryId() == 0) )
            {{ $item->getEndDate() }}
            <input type="hidden" placeholder="From Date &amp; Time"  class="form-control xedate" name="end_date" value="{{ $item->getEndDate() }}" required>
            @else
            ---
            @endif
            @else
            <!-- <input type="text" placeholder="From Date &amp; Time"  class="form-control xedate" name="end_date" value="{{ $item->getEndDate() }}" required> -->
            <div class='input-group enddp'>
                <input type="text" placeholder="To Date & Time" class="form-control" name="end_date"  value="{{ $item->getEndDate() }}">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
            @endif
        </td>
{{--         <td>
            {{ ($item->getDonationType() ? $item->getDonationType(). ' Donation' : 'Event') }}
        </td> --}}
        <td>
            <button onchange="checkDec(this)" class="btn btn-primary btn-full btn_cart_save" title="Save Changes" style="margin-bottom:5px;">
                Save
            </button><br>
            <a  class="btn btn-cancel-modify btn-full" data-id="{{ $item->getID() }}" title="Cancel" style="color:#fff; background-color:#ff3333;">
                Cancel
            </a>
        </td>
    </tr>
</form>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script>
    function checkDec(xElement) {
        theNum = xElement.value.toString();
        var regExp = /^\d{0,}\.\d{2}$/;    //format  .## required
        //var regExp = /^\d{1,}\.\d{2}$/;  //format #.## required
        var formatLine = theNum.match(regExp);
        var convert = Math.round(theNum*100)/100
        if(!formatLine){ //Test if there was no match
          alert("ERROR:\n\nThe amount entered: " + theNum + " is not in the correct format of .## It will be converted to " + convert + " upon submission."); //Display Error
          xElement.focus();  //Force User To Enter Correct Amount
        }
    }
     $('#qty'+'{{ $item->id}}').on('input', function(){
        var available = parseInt($('#available' + '{{ $item->id }}').val());
        var qty       = parseInt($('#qty'+'{{ $item->id}}').val());
       //alert(qty);
        if(qty > available){
            alert('You reach the maximum capacity');
            $('#qty'+'{{ $item->id}}').val(' ');
        }
    });
</script>
