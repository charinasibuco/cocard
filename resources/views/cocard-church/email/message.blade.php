@if(isset($group_name))
Hi {{ $group_name}} Group,
@elseif(isset($emailStrip))
Hi {{ $emailStrip[$i]}},
@elseif(isset($name))
Hi {{ $name }},
@elseif(isset($email))
Hi {{ $email }},
@endif
<br>
<br>
	@if(isset($custom_message))
		{{ $custom_message }}
		<br>
	@endif
	{{ isset($request->message) ? $request->message : $request['message']}}
<br>
	@if(isset($start_date) && isset($end_date))
	<br>
		Event Name: {{$event_name}}
		<br>
		Start Date:{{ $start_date }}
		<br>
		End Date:{{ $end_date }}
		<br>
	@endif
<br>
Thank you!
