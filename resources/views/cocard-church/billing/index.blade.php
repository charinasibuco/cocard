@extends('layouts.app')
@if(Auth::user()) @include('cocard-church.user.navigation') @endif
@section('content')
<div class="content">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				@if($step == 1)
                    @include('cocard-church.billing.forms.customer')
                @elseif($step == 2)
					@include('cocard-church.billing.forms.creditcard')
				@else
                    <div class="alert alert-danger" role="alert">Nothing to see here, move along.</div>
                @endif
			</div>
		</div>
	</div>
</div>
@endsection
