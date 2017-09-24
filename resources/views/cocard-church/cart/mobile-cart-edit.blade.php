
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>Frequency: </td>
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
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>Category: </td>
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
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>Amount: </td>
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
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>Quantity: </td>
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
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>Start date: </td>
        <td>
            @if($item->getFrequencyId() == 0 )
            @if(($item->getDonationCategoryId() == 0) )
            {{ $item->getStartDate() }}
            <input type="hidden" placeholder="From Date &amp; Time" class="form-control xsdate" name="start_date" value="{{ $item->getStartDate() }}" required>
            @else
            ---
            @endif
            @else
            <div class="input-group startdp" >
                <input type="text" placeholder="From Date & Time" class="form-control" name="start_date" value="{{ $item->getStartDate() }}"required>
                <span class="input-group-addon">
                    <span class="glyphicon-calendar glyphicon"></span>
                </span>
            </div>
            <!-- <input type="text" placeholder="From Date &amp; Time" class="form-control xsdate" name="start_date" value="{{ $item->getStartDate() }}" required> -->
            @endif
        </td>
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>End date: </td>
        <td>
            @if($item->getFrequencyId() == 0 )
            @if(($item->getDonationCategoryId() == 0) )
            {{ $item->getEndDate() }}
            <input type="hidden" placeholder="From Date &amp; Time"  class="form-control xedate" name="end_date" value="{{ $item->getEndDate() }}" >
            @else
            ---
            @endif
            @else
            <div class='input-group enddp'>
                <input type="text" placeholder="To Date & Time" class="form-control" name="end_date"  value="{{ $item->getEndDate() }}" >
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
            <!-- <input type="text" placeholder="From Date &amp; Time"  class="form-control xedate" name="end_date" value="{{ $item->getEndDate() }}" > -->
            @endif
        </td>
    </tr>
    <tr class="cart-edit" data-id="{{ $item->getID() }}">
        <td>
            <button onchange="checkDec(this)" class="btn btn-primary btn-full" title="Save Changes">
                Save
            </button>
        </td>
        <td>
            <a  class="btn btn-primary btn-cancel-modify btn-full"data-id="{{ $item->getID() }}"  title="Cancel">
                Cancel
            </a>
        </td>
    </tr>
</form>
