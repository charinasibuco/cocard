@extends('layouts.app')
@section('title', $page->meta_title != '' ? $page->meta_title : $page->title)
@section('meta_title',$page->meta_title)
@section('keywords', $page->keywords)
@section('description', $page->description)
<style type="text/css">
.navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus, li.dropdown > a:hover, li.dropdown > a:focus, ul.dropdown-menu > li > a:hover {
    background-color: transparent;
    color: #333;
}
.small-content {
    position: absolute;
    bottom: 0;
    width: 100%;
}
.hidden{
    display: none;
}
</style>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{asset('images/isteward_logo.png')}}" alt="isteward logo" />
            </a>
        </div>
        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if(Auth::guest())
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/about-us') }}">About us</a></li>
                <li><a href="{{ url('/features') }}">Features</a></li>
                <li><a href="{{ url('/register') }}">Contact</a></li>
                @include('includes.custom-menu-items', array('items' => $FrontMenu->roots()))
                @else
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li><a href="{{ url('/about-us') }}">About us</a></li>
                <li><a href="{{ url('/features') }}">Features</a></li>
                <li><a href="{{ url('/register') }}">Contact</a></li>
                @include('includes.custom-menu-items', array('items' => $FrontMenu->roots()))
                <li><a href="{{ url('/logout') }}">Logout</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
@section('content')
{!! htmlspecialchars_decode($page->content) !!}
<input type="hidden" id="content" value="{{ strlen($page->content)}}">
@include('includes.footer')
@endsection

