@section('title') {{ $organization->name}} Church User Dashboard @endsection
@section('style')
<style media="screen">
body{
    background-color: {{ $scheme2 }};
}
body, p, span{
    color: {{ $scheme6 }};
}
h1,h2,h3,h4,h5,h6{
    color: {{ $scheme5 }};
}
a{
    color: {{ $scheme7 }};
}
.navbar{
    background-color: {{ $scheme1 }};
}
i, i:hover, i:focus{
   color: {{ $scheme6 }};
}
.navbar-default .navbar-nav > li > a, .navbar-default .navbar-nav > li > a:hover, .navbar-nav > li > a i, .navbar-nav > li > a span, .navbar-brand-container a i{
    color: #fff;
}
.nav-tabs li a{
    color: #222;
}
.bannerContainer{
    background: url({{ url('images/'. $banner) }});
}
.bannerContainer{
    margin-top:0;
}
.bg-banner{
    background: url({{ url('images/'. $banner) }});
}
footer{
    background-color: {{ $scheme3 }};
}
.btn, .btn:hover, .btn:focus{
    color: {{ $scheme9 }};
}
.btn, .btn:hover, .btn:focus{
    background: {{ $scheme4 }};
}

.btn i, .btn i:hover, .btn i:focus{
    color: {{ $scheme8 }};
}

.side-nav{
    background-color: {{ $scheme5 }};
}
.side-nav a span{
    color: {{ $scheme10 }};
}
.side-nav a i, .side-nav h4{
    color: {{ $scheme10 }};
}
</style>
@endsection
<div class="side-nav user-nav">
    <ul style="margin-top:70px;">
        <li><a href="{{ url('/organization/'.$slug.'/home')}}"><i class="fa fa-home" aria-hidden="true"></i>&nbsp;Home</a></li>
        <li><a href="{{ url('/organization/'.$slug.'/donations')}}"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;Donate</a></li>
        <li><a href="{{ url('/organization/'.$slug.'/events')}}"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i>&nbsp;Events</a></li>
        <li><a href="{{ url('/organization/'.$slug.'/volunteer_listing')}}"><i class="fa fa-hand-paper-o" aria-hidden="true"></i>&nbsp;Volunteer</a></li>
    </ul>
</div>
<nav class="navbar navbar-default navbar-blue  navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <div  class="navbar-brand-container">
                <a id="menu-toggle" href="#" class="btn-menu toggle">
                    <i class="fa fa-bars"></i>
                </a>

                <a class="navbar-brand" href="{{ url('/organization/'.$slug) }}">
                    @if($organization->logo == null )
                    <img src="{{asset('images/isteward_logo_w.png')}}" alt="isteward logo" />
                    @else
                    <img class="user-pic" src="{{ URL::to('/images/'.$organization->logo.'/')}}"  />
                    @endif
                </a>
            </div>
            <button type="button" class="navbar-toggle collapsed nav-down" data-toggle="collapse" data-target="#nav-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-caret-down" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</nav>
