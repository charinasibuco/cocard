@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')

<div class="content">
	<div class="container">
		@if(Session::has('message'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('message') }}
		</div>
		@elseif(Session::has('error'))
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('error') }}
		</div>
		@endif
		<!-- Small modal -->
		<div class="tab-content" style="margin:0;">
			<div class="col-md-offset-3 col-md-6">
				<form method="POST" action="{{ url('organization/'.$slug.'/donation/payment-info/proceed-to-payment') }}" >
					<h2 style="text-align:center;">Payment Information</h2>
					<hr>
					<div class="well payment-info">
						<div class="form-group">
							@if($user!= null)
							<h5 class="textbelow"> User</h5>
							<h4>{{$user->first_name}} {{$user->last_name }}</h4>

								@endif
								<h5 class="textbelow">Total amount</h5>
								<h4>${{ number_format($total,2,'.',',') }}</h4>
								<hr>
								<h5>Enter credit card number</h5>
								@if($user!= null)
								<input type="hidden" class="form-control text-amount" name="userid" value="{{ $user->id }}" >
								@else
								<input type="hidden" class="form-control text-amount" name="userid" value="0" >

								@endif
								<input type="hidden" class="form-control text-amount" name="total" value="{{ $total }}" >
								<input type="hidden" class="form-control text-amount" name="slug" value="{{ $slug }}" >
								<input type="hidden" class="form-control text-amount" name="organization_id" value="{{ $organization_id }}">
								<input type="number" class="form-control text-amount" name="creditcard" placeholder="Credit Card Number">
								<br>
								<h5>Enter credit card account name</h5>
								<input type="text" class="form-control text-amount" name="acctname" placeholder="Account Name">
								<br>
								<h5>Security Code</h5>
								<div class="input-group">
									<input type="number" class="form-control text-amount" name="acctname" placeholder="Security Code">
									<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
								</div>
								<br>
								<h5>Enter credit card exp date: &nbsp;</h5>

								<div class="row">
									<div class="col-md-6">
										<input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type = "number" maxlength = "2" name="month" style="font-size:14px;padding:10px;"type="text" class="form-control text-amount" placeholder="mm">
									</div>
									<div class="col-md-6">
										<input oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type = "number" maxlength = "4" name="year" style="font-size:14px;padding:10px;"type="text" class="form-control text-amount" placeholder="yyyy">
									</div>
								</div>
								<br>
							</div>
						</div>
						<div class="row">
							{!! csrf_field() !!}
							<div class="col-md-6">
								<button type="submit" class="btn btn-primary btn-blue btn-full">
									<i class="fa fa-btn fa-user"></i>&nbsp;Process Payment
								</button>
							</div>
							<div class="col-md-6">
								@if($user!= null)
								<a href="{{ url('/organization/'.$slug.'/user/donate', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
								@else
								<a href="{{ url('/organization/'.$slug.'/donations', Session::flash('error_code', 5))}}" class="btn btn-full">Cancel</a>
								@endif
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	@include('includes.footer')
	@endsection