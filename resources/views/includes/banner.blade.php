<div class="bannerContainer">
    <div class="container">

        <div class="bannerHeader">
        	@if(count($errors) > 0)
        	<div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        	@foreach ($errors->all() as $error)
        	@if(strpos($error, 'required') == false)
        	<div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        	@endif
        	@endforeach
        	@endif
        	@if(Session::has('message'))
        	<div class="alert alert-success alert-dismissible" role="alert">
        		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        		{{ Session::get('message') }}
        	</div>
        	@endif
            <h1>iSteward</h1>
            <h2>Church Management Software</h2>
            <a href="{{ url('/register') }}" class="btn btn-primary btn-green center">
                <i class="fa fa-btn fa-user"></i>&nbsp;Contact Us
            </a>
        </div>
    </div>
</div>
