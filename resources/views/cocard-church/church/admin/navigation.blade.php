@section('title') {{ $organization->name }} Administrator @endsection
<div class="side-nav default admin-side-nav" style="width: 260px;">
    <img class="user-pic" src="{{ asset('images/'.(is_null(Auth::user()->image) ? 'icons/user.png' : Auth::user()->image )) }}" alt="isteward logo" />
    <h4>Hi {{ Auth::user()->first_name }}!</h4>
    <ul>
        <?php
            $assigned_roles = App\AssignedUserRole::where('user_id', Auth::user()->id)->where('status', 'Active')->get();
        ?>
        @if(count($assigned_roles) > 1)
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/login-as') }}"><i class="fa fa-exchange"></i></span>&nbsp;
                <span class="txt_align" style="color: white;">Switch Account</span>
            </a>
        </li>
        @endif
        @can('view_member_list')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/members')}}"><img src="{{asset('images/icons/members.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.members')</span>
            </a>
        </li>
        @endcan
        @can('view_event')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/events')}}"><img src="{{asset('images/icons/events.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.events')</span>
            </a>
        </li>
        @endcan
        @can('view_donation')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/donation-category')}}"><img src="{{asset('images/icons/donations.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.donations')</span>
            </a>
        </li>
        @endcan
        @can('view_family')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/family')}}"><img src="{{asset('images/icons/family.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.family')</span>
            </a>
        </li>
        @endcan
        @can('view_volunteer')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/volunteer')}}"><i class="icon-people"></i>&nbsp;
                <span class="txt_align">@lang('dashboard.volunteers')</span>
            </a>
        </li>
        @endcan
        @can('view_staff')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/staff')}}"><img src="{{asset('images/icons/volunteers.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.staff')</span>
            </a>
        </li>
        @endcan
        @can('view_roles')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/role')}}"><i class="icon-grid"></i>&nbsp;
                <span class="txt_align">@lang('dashboard.role')</span>
            </a>
        </li>
        @endcan
        @can('view_email_group')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/email-group')}}"><i class="icon-envelope"></i>&nbsp;
                <span class="txt_align">@lang('dashboard.emailgroup')</span>
            </a>
        </li>
        @endcan
        @can('backup_database')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/backup')}}"><img src="{{asset('images/icons/backup.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.backup')</span>
            </a>
        </li>
        @endcan
        @can('generate_report')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/reports')}}"><img src="{{asset('images/icons/reports.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.reports')</span>
            </a>
        </li>
        @endcan
        @can('view_quickbooks')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/quickbooks')}}"><img src="{{asset('images/icons/backup.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">QuickBooks</span>
            </a>
        </li>
        @endcan
        @can('view_admin_log')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/logs')}}"><img src="{{asset('images/icons/logs.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.logs')</span>
            </a>
        </li>
        @endcan
        @can('edit_admin_settings')
        <li>
            <a href="{{ url('/organization/'.$slug.'/administrator/settings')}}"><img src="{{asset('images/icons/settings.png')}}" alt="isteward logo" />&nbsp;
                <span class="txt_align">@lang('dashboard.settings')</span>
            </a>
        </li>
        @endcan
    </ul>
</div>
<div class="side-nav-overlay"></div>

<nav class="navbar navbar-default navbar-blue  navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <div  class="navbar-brand-container">
                <a id="menu-toggle" href="#" class="btn-menu toggle">
                    <i class="fa fa-bars"></i>
                </a>
                <a class="navbar-brand" href="{{ url('organization/'.$slug. '/') }}">
                    <img src="{{asset('images/isteward_logo_w.png')}}" alt="isteward logo" />
                </a>
            </div>
            <button type="button" class="navbar-toggle collapsed nav-down" data-toggle="collapse" data-target="#nav-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <i class="fa fa-caret-down" aria-hidden="true"></i>
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
                        <input type="submit" value="Go" class="btn btn-darkblue">
                    </form>
                </li>
                <li><a href="{{ url('organization/'.$slug. '/logout') }}"><i class="fa fa-btn fa-sign-out"></i>&nbsp;<span class="txt_align">Logout</span></a></li>
            </ul>
        </div>
    </div>
</nav>
