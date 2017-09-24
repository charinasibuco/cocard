Hi {{ $volunteer->name}}!
<br>
<br>
Your status now is {{ $status }} for Volunteer Group {{$volunteer->volunteer_group->type}} with the following details:
<br>
<br>
Volunteer Group Name:{{$volunteer->volunteer_group->type}}
<br>
Start Date:{{ $request->start_date}}
<br>
End Date:{{ $request->end_date}}
<br>
<br>
--Admin