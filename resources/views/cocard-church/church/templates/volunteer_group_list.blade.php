@foreach($volunteer_groups as $group)
<tr>
	<td>-{{ $group->type}}</td>
	<td>{{ count($group->approved_volunteers) }} /{{ $group->volunteers_needed}}</td>
</tr>
@endforeach
