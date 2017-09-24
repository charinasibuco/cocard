@section('title') Isteward @endsection
@include('includes.navbar-blue')
<div class="side-nav default">
    <img class="user-pic" src="{{ asset('images/'.(is_null(Auth::user()->image) ? 'icons/user.png' : Auth::user()->image )) }}" alt="isteward logo" />
    <h4 class="user-name">Hi {{ Auth::user()->first_name }}!</h4>
    <ul>
        <!-- <li title="Organization">
            <a class="desktop_link" href="{{ url('/organizations') }}">
                <i class="fa fa-sitemap"  aria-hidden="true"></i>
                <span class="txt_align">@lang('dashboard.organization')</span>
            </a>
            <a class="desktop_active" href="{{ url('/organizations') }}">
                <i class="fa fa-sitemap"  aria-hidden="true"></i>&nbsp;
                <span class="txt_align">@lang('dashboard.organization')</span>
            </a>
        </li> -->
        <li title="Organization"><a href="{{ url('/organizations') }}"><i class="fa fa-sitemap" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.organization')</span></a></li>
        <li title="Users"><a href="{{ route('users')}}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.users')</span></a></li>
        <li title="Pages"><a href="{{ route('page')}}"><i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.pages')</span></a></li>
        <li title="Permission List"><a href="{{ route('permission') }}"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.permission_list')</span></a></li>
        <li title="Activity Log"><a href="{{ route('activity-log') }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;<span class="txt_align">@lang('dashboard.activity_log')</span></a></li>
    </ul>
</div>
<div class="side-nav-overlay"></div>
