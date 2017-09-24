@foreach($volunteer_groups as $group)
<div class="row">
	<div class="col-md-6"><span>---{{ $group->type}}</span></div>
	<div class="col-md-6"><span>{{ count($group->approved_volunteers) }} /{{ $group->volunteers_needed}}</span></div>
</div>
@endforeach
	