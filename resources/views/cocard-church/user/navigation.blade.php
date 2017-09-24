@section('title') {{ $organization->name}}   @endsection
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
table a i, table a i:hover, table a i:focus{
    color: #fff;
    border-radius: 10px;
    background-color: #012732;
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
.side-nav a, .side-nav h4{
    color: {{ $scheme10 }};
}
.side-nav a i{
    color: #fff;
}
ul.sub-nav li a, ul li a img{
    color: {{ $scheme10 }};
}
</style>
@endsection
@if(Auth::user())
<div class="side-nav default">
    <img class="user-pic" src="{{ asset('images/'.(is_null(Auth::user()->image) ? 'icons/user.png' : Auth::user()->image )) }}" alt="User Image" />
    <h4>Hi {{ Auth::user()->first_name }}!</h4>
    <ul>
        <?php
            $assigned_roles = App\AssignedUserRole::where('user_id', Auth::user()->id)->where('status', 'Active')->get();
        ?>
        @if(count($assigned_roles) > 1)
        <li>
            <a href="{{ url('/organization/'.$slug.'/user/login-as') }}"><i class="fa fa-exchange"></i></span>&nbsp;
                <span class="txt_align" style="color: white;">Switch Account</span>
            </a>
        </li>
        @endif
        <li class="toggle_donation"><a><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.donations') <i class="fa fa-caret-down"></i></span></a></li>
        <ul class="sub-nav" id="donation_options">
            <li><a href="{{ url('/organization/'.$slug.'/user/donate')}}">Donate</a></li>
            <li><a href="{{ url('/organization/'.$slug.'/user/donation')}}">My Donations</a></li>
        </ul>
        <li class="toggle_event"><a><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.events') <i class="fa fa-caret-down"></i></span></a></li>
        <ul class="sub-nav" id="event_options">
            <li><a  href="{{ url('/organization/'.$slug.'/user/calendar')}}">Sign Up</a></li>
            <li><a href="{{ url('/organization/'.$slug.'/user/events')}}">My Events</a></li>
        </ul>
        <li class="toggle_volunteer"><a><i class="fa fa-hand-paper-o" aria-hidden="true"></i>&nbsp;<span class="txt_align">Volunteers <i class="fa fa-caret-down"></i></span></a></li>
        <ul class="sub-nav" id="volunteer_options">
            <li><a href="{{ url('/organization/'.$slug.'/user/event/volunteer')}}">Sign Up</a></li>
            <li><a href="{{ url('/organization/'.$slug.'/user/volunteer')}}">My Volunteer Spots</a></li>
        </ul>
        <li>
            <a href="{{ url('/organization/'.$slug.'/user/profile')}}"><img src="{{asset('images/icons/settings.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.settings')</span>
            </a>
        </li>
        <!-- <li><a href="{{ url('/organization/'.$slug.'/user/family')}}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.family')</span></a></li>
        <li><a href="{{ url('/organization/'.$slug.'/user/profile')}}"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.profile')</span></a></li> -->
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
                <i class="fa fa-caret-down" aria-hidden="true" style="color:#fff;"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-list" id="nav-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <form id="form-language" method="post" action="{{ route('save_language', Auth::user()->id)}}">
                        <label>Select Language</label>
                            <select name="locale" id="locale" style="color:#333">
                                <option value="en" {{ (Auth::user()->locale == 'en') ? 'selected="selected"' : ''}}>English</option>
                                <option value="es" {{ (Auth::user()->locale == 'es') ? 'selected="selected"' : ''}}>Spanish</option>
                            </select>
                            {!! csrf_field() !!}
                            <button type="submit" class="btn btn-darkblue">Go</button>
                    </form>
                </li>
                <li><a href="{{ url('organization/'.$slug. '/logout') }}"><i class="fa fa-btn fa-sign-out"></i>&nbsp;<span class="txt_align">Logout</span></a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="side-nav-overlay"></div>
@endif
