<div class="bannerContainer">
    <div class="container">
        <div class="bannerHeader">
            <h1>{{ $organization->name}}</h1>

        </div>
        <br><br>
        <div class="banner-btn">
        	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        		<div class="hovereffect clearfix">
                    <a href="{{ url('/organization/'.$slug.'/donations')}}">
        			<i class="icon-present" aria-hidden="true"></i>
        				<h2>Donate Now</h2>
                    </a>
        		</div>
        	</div>
        	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        		<div class="hovereffect">
                    <a href="{{ url('/organization/'.$slug.'/events')}}">
        			<i class="icon-calendar" aria-hidden="true"></i>
        				<h2>Join an Event</h2>
                    </a>
        		</div>
        	</div>
        	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        		<div class="hovereffect">
                    <a href="{{ url('/organization/'.$slug.'/volunteer_listing')}}">
        			<i class="icon-people" aria-hidden="true"></i>
        				<h2>Be a Volunteer</h2>
                    </a>
        		</div>
        	</div>
            <div class="col-md-offset-4 col-md-4 col-sm-6 col-xs-12">
                <div class="hovereffect">
                    <a href="{{ url('/organization/'.$slug.'/login')}}">
        			<i class="fa fa-sign-in" aria-hidden="true"></i>
        				<h2>Login</h2>
                    </a>
        		</div>
            </div>
        </div>
    </div>
</div>
