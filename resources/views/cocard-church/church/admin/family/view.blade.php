@extends('layouts.app')
@if(Auth::user()->hasRole('member'))@include('cocard-church.user.navigation')@else @include('cocard-church.church.admin.navigation')@endif
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<div class="row">
			<h3>
				<div class="clearfix">
					@can('edit_family')
                    <a href="{{ url('organization/'. $slug .'/administrator/family/edit/'.$id) }}" class="btn btn-darkblue float-right">
                        <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit
                    </a>
                    @endcan
					<a href="@if(Auth::user()->hasRole('member')){{ url('/organization/'.$slug.'/user/family/'.$family_id)}}@else{{ url('/organization/'.$slug.'/administrator/family/'.$family_id)}}@endif" class="btn btn-darkblue float-left">
						<i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
					</a>
				</div>
			</h3>
		</div>
		<div class="panel panel-primary panel-information" style="margin-top:10px;">
			<div class="panel-heading personal-information"><i class="fa fa-users" aria-hidden="true"></i> &nbsp;{{ $name }} Family Members</div>
			<div class="panel-body">
			@if(count($family_members) < 1)
			<i>No family members to show.</i>
			@endif
				<ul style="margin-left: 30px;">
					@foreach($family_members as $family_member)
					<li>{{ $family_member->first_name }} {{ $family_member->middle_name }} {{ $family_member->last_name }}</li>
					@endforeach
				</ul>		
			</div>
		</div>
		<div class="panel panel-primary panel-information" style="margin-top:10px;">
			<div class="panel-heading personal-information"><i class="fa fa-phone" aria-hidden="true"></i> &nbsp;Contact Details</div>
			<div class="panel-body">
				<table>
					<tbody>
						<tr>
							<td>Primary Phone:</td>
							<td>{{ $primary_phone }}</td>
						</tr>
						<tr>
							<td>Secondary Phone:</td>
							<td>{{ $secondary_phone }}</td>
						</tr>
						<tr>
							<td>Primary Email:</td>
							<td>{{ $primary_email }}</td>
						</tr>
						<tr>
							<td>Secondary Email:</td>
							<td>{{ $secondary_email }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel panel-primary panel-information" style="margin-top:10px;">
			<div class="panel-heading personal-information"><i class="fa fa-home" aria-hidden="true"></i> &nbsp;Family Address</div>
			<div class="panel-body">
				<table>
					<tbody>
						<tr>
							<td>Address 1:</td>
							<td>{{ $address_1 }}</td>
						</tr>
						<tr>
							<td>Address 2:</td>
							<td>{{ $address_2 }}</td>
						</tr>
						<tr>
							<td>City:</td>
							<td>{{ $city }}</td>
						</tr>
						<tr>
							<td>State:</td>
							<td>{{ $state }}</td>
						</tr>
						<tr>
							<td>Zipcode:</td>
							<td>{{ $zipcode }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
