@extends('layouts.app')
@include('includes.navigation')
@section('content')
<div class="about-banner">
    <div class="caption">
        <span class="border"> ABOUT US </span>
    </div>
</div>
<div class="container"> 
    <div class="about-content">
        <h2 class="h2"><span>History</span></h2>
        <div class="well">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis a elit in diam egestas aliquam. Mauris vestibulum ligula nisl, non lobortis enim auctor quis. Nullam sollicitudin rutrum nulla et porttitor. Duis sollicitudin commodo augue a faucibus. Pellentesque porta, nisi sit amet elementum dapibus, erat enim sollicitudin magna, eget porta nisi neque eget tortor. Phasellus et sodales metus. Donec eget lorem quam. Sed feugiat est vel nisi sagittis tempus. Nam at eleifend tortor, eu consectetur arcu. Morbi faucibus nunc eu sagittis vulputate. Sed nec risus vestibulum, aliquet sapien a, auctor sapien. Maecenas faucibus temor lorem.</p>
            <p>Suspendisse pharetra augue sit amet ex dapibus varius. Vivamus id justo auctor, condimentum erat ac, sollicitudin velit. Vestibulum ut lacinia enim. Donec quis lacinia dolor. Nullam libero tellus, rhoncus sed suscipit eu, fermentum eget eros. Curabitur sapien nibh, malesuada eu lorem vitae, convallis accumsan erat. Cras vitae tempus felis, eget volutpat massa. Fusce fermentum elementum ipsum, eget vestibulum dolor rhoncus eu. Nulla mattis turppis felis, vitae fermentum magna pretium a.</p>
        </div>
        <h2 class="h2"><span> Lorem Ipsum </span></h2>
        <div class="well">
            <p>Nullam neque nulla, congue non nulla pulvinar, vulputate mattis mi. Mauris placerat ligula eu posuere laoreet. Praesent luctus nibh quis neque pellentesque, a facilisis libero pretium. Morbi magna sem, maximus vel sollicitudin non, cursus nec magna. Aenean finibus ante interdum interdum iaculis. Ut quis lobortis leo, sit amet gravida libero. Maecenas dapibus, eros a hendrerit pharetra, metus arcu fermentum nisi, at scelerisque erat elit in est. Integer sollicitudin in elit at volutpat. Phasellus eleifend porta dignissim. Praesent sit amet ante vitae felis mollis ullamcorper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce vel fringilla nisl, vel ultricies leo.
            </p>
            <p>In ac feugiat mauris. Nullam nec arcu sapien. Nullam hendrerit eros metus, id feugiat nisi elementum at. Proin elementum eros eget risus mollis, id scelerisque tellus tristique. Cras tortor diam, auctor vel luctus ut, feugiat nec quam. Sed semper sapien ultrices, venenatis lorem id, accumsan eros. Maecenas malesuada nunc eu justo luctus lobortis. Vestibulum eleifend nibh lorem, eu mollis ligula commodo sit amet. Donec imperdiet facilisis velit in volutpat. Donec lectus purus, placerat sed turpis ut, consectetur vehicula mi. Nulla luctus, ex a pulvinar rutrum, ligula nisi rhoncus dolor, quis cursus nibh enim ut mi. Nam non purus auctor, ullamcorper dolor sit amet, porttitor ante. Nunc vitae urna venenatis, tempus ligula sed, commodo elit. Mauris dapibus et tortor ac imperdiet.</p>
        </div>
        <div class="row" id="image-content">
            <div class="col-sm-4" >
                <img src="/images/user.png">
                <h1>CEO</h1>
            </div>
            <div class="col-sm-4">
                <img src="/images/user.png">
                <h1>COO</h1>
            </div>
            <div class="col-sm-4">
                <img src="/images/user.png">
                <h1>CTO</h1>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
