<h4>Thanks for signing up at iSteward Church Management Software</h4>
<p>Please click on the following link to verify your email address:</p>
	<a href="{{ url('/organization/'.$organization->url.'/administrator/')}}">
		<button style="background-color:#90be4a; padding:10px">Confirm your Organization Account</button>
	</a>
<p>
	If the above link does not work, you can paste the following address into your browser:<a href="{{ url('/organization/'.$organization->url.'/administrator/')}}">
	{{ url('/organization/'.$organization->url.'/administrator/')}}</a>
</p>
