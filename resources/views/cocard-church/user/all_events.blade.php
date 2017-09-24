<form action="{{ url('organization/'. $slug .'/user/events/export/'. $id) }}" method="post" style="display:none">
	<table>
		<tbody>
			<?php
	            $y = 0;
	        ?>
			@foreach($participants_all as $participant_all)
			<?php
	            $y++;
	        ?>	
			<tr>
                <span class="dataTz" id="start_date_{{ $y }}" data-date="{{ $participant_all->event_start_date }}" style="display:none"> &nbsp; </span>
                <td id="start_date_timezone_{{ $y }}" style="display:none"></td>
                <td><input type="text" value="" name="input_start_date_timezone[]" id="start_date_input_timezone_{{ $y }}"></td>
                <span class="dataTz" id="end_date_{{ $y }}" data-date="{{ $participant_all->event_end_date }}" style="display:none"> &nbsp; </span>
                <td id="end_date_timezone_{{ $y }}" style="display:none"></td>
                <td><input type="text" value="" name="input_end_date_timezone[]" id="end_date_input_timezone_{{ $y }}"></td>
                <span class="dataTz" id="created_date_{{ $y }}" data-date="{{ $participant_all->participant_created_at }}" style="display:none"> &nbsp; </span>
                <td id="created_date_timezone_{{ $y }}" style="display:none"></td>
                <td><input type="text" value="" name="input_created_date_timezone[]" id="created_date_input_timezone_{{ $y }}"></td>
			</tr>
			@endforeach
		</tbody>
	</table>
<input type="submit" id="submit_event" value="Go">
</form>