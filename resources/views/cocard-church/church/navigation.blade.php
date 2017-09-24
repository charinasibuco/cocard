@section('title') {{ $organization->name}} @endsection
@section('style')
<style media="screen">
body{
    background-color: {{ $scheme2 }};
}
body{
    color: {{ $scheme6 }};
}
.navbar{
    background-color: {{ $scheme1 }};
}
.navbar-default .navbar-nav > li > a, .navbar-default .navbar-nav > li > a:hover{
    color: {{ $scheme10 }};
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
 i, i:hover, i:focus{
    color: {{ $scheme7 }};
}
.btn i, .btn i:hover, .btn i:focus{
    color: {{ $scheme9 }};
}
h1,h2,h3,h4,h5,h6{
    color: {{ $scheme6 }};
}
ul li, p, span{
    color: {{ $scheme7 }};
}
a{
    color: {{ $scheme8 }};
}
#calendar a{
    color:#222;
}
.banner-btn a i,.banner-btn a h2{
    color: {{ $scheme8 }};
}
.bannerHeader h1{
    color: {{ $scheme6 }};
}
</style>
@endsection
