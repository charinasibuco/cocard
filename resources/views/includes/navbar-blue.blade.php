<nav class="navbar navbar-default navbar-blue navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <div  class="navbar-brand-container">
                <a id="menu-toggle" href="#" class="btn-menu toggle">
                    <i class="fa fa-bars"></i>
                </a>

                <a class="navbar-brand" href="{{ url('/') }}">
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
                        <label>@lang('navigation.select_language')</label>
                            <select name="locale" id="locale" style="color:#333">
                                <option value="en" {{ (Auth::user()->locale == 'en') ? 'selected="selected"' : ''}}>English</option>
                                <option value="es" {{ (Auth::user()->locale == 'es') ? 'selected="selected"' : ''}}>Spanish</option>
                            </select>
                            {!! csrf_field() !!}
                            <input type="submit" value="@lang('dashboard_details.go')" class="btn btn-darkblue">
                    </form>
                </li>
                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>&nbsp;<span class="txt_align">@lang('navigation.logout')</span></a></li>
            </ul>
        </div>
    </div>
</nav>
