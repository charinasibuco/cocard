@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="container">
<div class="panel panel-default" style="margin-top:25px; padding:20px;">
		<div class="margin-mob-top">
			<div class="row">
				<h3>List of Volunteers Needed</h3>
				<div class="table-main panel panel-default">
                    @include('cocard-church.volunteer.index')
                </div>
			</div>
		</div>
	</div>
</div>
@endsection
