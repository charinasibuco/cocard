@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<div class="sub-table">
			<h3 class="permissiontitle">Permission List</h3>
			<div class="table-main panel panel-default">
				<div class="table-responsive">
					{!! str_replace('/?', '?', $permission->render()) !!}
					<table class="table table-striped" id="tabledata">
						<thead class="theader">
							<th class="p_title">Title</th>
							<th class="p_desc">Description</th>
						</thead>
						<tbody>
							@foreach($permission as $row)
							<tr>
								<td>{{ $row->title }}</td>
								<td>{{ $row->description }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@if($permission->count() == 0)
				<div class="norecords_prompt">No records to show</div>
				@if($search)
				<div class="for_search">for {{ $search }}</div>
				@endif
				@endif
				{!! str_replace('/?', '?', $permission->render()) !!}
			</table>
		</div>
	</div>
</div>
@endsection
