@extends('layouts.app')
@if(Auth::user()->hasRole('member'))@include('cocard-church.user.navigation')@else @include('cocard-church.church.admin.navigation')@endif
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
		<div class="row">
			<h3>
				<div class="clearfix">
					@can('edit_user_family_member')
                    <a href="{{ url('organization/'. $slug .'/administrator/family/family-member/edit/'.$id) }}" class="btn btn-darkblue float-right">
                        <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit
                    </a>
                    @endcan
                    @if((Auth::user()->hasRole('member')) && ($user_id == 0 || $user_id == Auth::user()->id))
                    <a href="{{ url('/organization/'.$slug.'/user/family/family-member/edit/'.$id) }}" class="btn btn-darkblue float-right">
                        <i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit
                    </a>
                    @endif
					<a href="@if(Auth::user()->hasRole('member')){{ url('/organization/'.$slug.'/user/family/'.$family_id)}}@else{{ url('/organization/'.$slug.'/administrator/family/'.$family_id)}}@endif" class="btn btn-darkblue float-left">
						<i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Back
					</a>
				</div>
			</h3>
		</div>

		<div class="panel panel-primary panel-information" style="margin-top:10px;">
			<div class="panel-heading personal-information"><i class="fa fa-user" aria-hidden="true"></i> &nbsp;Details</div>
			<div class="panel-body">
				<div class="div-viewfamily">
					<img src="{{ asset('images/'.(($img != null) ? $img : 'user.png')) }}">
					<h1 class="family-names" style="">{{ $first_name }} {{ $middle_name }} {{ $last_name }}</h1>
					<h4 class="family-info">Birthdate: <strong>{{ $birthdate }}</strong></h4>
					<h4 class="family-info"style="margin-left:232px;">Gender: <strong>{{ $gender }}</strong></h4>
					<h4 class="family-info">Relationship: <strong>{{ $relationship }}</strong></h4>
					<h4 class="family-info">Additional Information: <strong>{{ $additional_info }}</strong></h4>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
