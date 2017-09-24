<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0"/>
    <meta name="meta_title" content="@yield('meta_title')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="description" content="@yield('description')">
    <title>@yield('title')</title>

    @include('blocks.global_fonts')
    @include('blocks.global_styles')
    @yield('style')

</head>
<body id="app-layout">

    @yield('content')
    @include('blocks.global_scripts')
    @yield('script')
    <!-- JavaScripts -->
    @if(!empty(Session::get('error_code')) && Session::get('error_code') == 5)
        <script type="text/javascript">
            $(function() {
                $('#large-modal').modal('show');
            });
        </script>
    @endif

</body>
</html>
