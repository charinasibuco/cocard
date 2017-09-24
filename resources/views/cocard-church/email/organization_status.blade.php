Hi {{ $get_organization->contact_person}}!
<br>
<br>
Your status is now {{ $status }} for {{$get_organization->name}} Organization application.
<br>
@if($status == 'Activated' || $status == 'Approved')
<br>
You can check your applied organization page on this link:
<br>
{{ url('organization/'.$get_organization->url)}}
<br>
@endif
<br>
--Admin