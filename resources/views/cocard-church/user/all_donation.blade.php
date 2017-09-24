<form action="{{ url('organization/'. $slug .'/user/donation/export/'. $id) }}" method="post" style="display:none">
	<table>
		<tbody>
			<?php
	            $y = 0;
	        ?>
			@foreach($donations_all as $donation_all)
			<?php
	            $y++;
	        ?>	
			<tr>
				
				@if($donation_all->DonationListId != 0)
					<span class="dataTz" id="created_date_{{ $y }}" data-date="{{  $donation_all->Date }}" style="display:none"> &nbsp; </span>
                    <td id="created_date_timezone_{{ $y }}"></td>
                    <td><input type="text" value="" name="input_created_date_timezone[]" id="created_date_input_timezone_{{ $y }}"></td>
				@endif
			</tr>
			@endforeach
		</tbody>
	</table>
	<input type="submit" id="submit_donations" value="Go">
</form>
