@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="content">
	<div class="container">
		<div class="input-group">
			<input type="text" class="form-control" placeholder="Search for...">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button">Go!</button>
			</span>
		</div><!-- /input-group -->
		<br>
		<table class="table table-striped donation-list">
			<thead>
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($donationCategory as $row)
				<tr>
					<td>{{ $row->name }}</td>
					<td>{{ $row->description }}</td>
					<td>{{ $row->status }}</td>
					<td class="col3">
						<a href="{{ url('/organization/'.$slug.'/donations')}}" class="btn btn-darkblue">Donate&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
            			</a>
            		</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
