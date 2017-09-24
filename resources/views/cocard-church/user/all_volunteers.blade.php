<form action="{{ url('organization/'. $slug .'/user/volunteer/export/'. $id) }}" method="post" style="display:none">
	<table>
		<tbody>
			<?php
	            $y = 0;
	        ?>
			@foreach($volunteers_all as $volunteer_all)
			<?php
	            $y++;
	        ?>	
			<tr>
                <span class="dataTz" id="start_date_{{ $y }}" data-date="{{$volunteer_all->volunteer_group->event->start_date}}" style="display:none"> &nbsp; </span>
                <td id="start_date_timezone_{{ $y }}" ></td>
                <td><input type="text" value="" name="input_start_date_timezone[]" id="start_date_input_timezone_{{ $y }}"></td>
                <span class="dataTz" id="end_date_{{ $y }}" data-date="{{$volunteer_all->volunteer_group->event->end_date}}" style="display:none"> &nbsp; </span>
                <td id="end_date_timezone_{{ $y }}" ></td>
                <td><input type="text" value="" name="input_end_date_timezone[]" id="end_date_input_timezone_{{ $y }}"></td>
			</tr>
			@endforeach
		</tbody>
	</table>
<input type="submit" id="submit_volunteer" value="Go">
</form>