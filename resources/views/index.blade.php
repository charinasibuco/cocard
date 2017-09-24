@extends('layouts.app')
@include('includes.navigation')
@section('content')
@include('includes.banner')
<div id="home">
    <div class="content home-head">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <h3>LOREM IPSUM</h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
                        magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="content g-content home-content">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <h3>LOREM IPSUM DOLOR SIT AMET</h3>
                    <i class="fa fa-minus" aria-hidden="true"></i>
                    <br><br>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <i class="fa fa-users" aria-hidden="true"></i><br>
                    <h4>sunt explicabo</h4>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
                        totam rem aperiam,
                        eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
                <div class="col-md-3">
                    <i class="fa fa-credit-card" aria-hidden="true"></i><br>
                    <h4>sunt explicabo</h4>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
                        totam rem aperiam,
                        eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
                <div class="col-md-3">
                    <i class="fa fa-cog" aria-hidden="true"></i><br>
                    <h4>sunt explicabo</h4>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
                        totam rem aperiam,
                        eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
                <div class="col-md-3">
                    <i class="fa fa-info" aria-hidden="true"></i><br>
                    <h4>sunt explicabo</h4>
                    <p>
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
                        totam rem aperiam,
                        eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
