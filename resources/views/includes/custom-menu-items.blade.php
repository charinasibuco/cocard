@foreach($items as $item)
  <li @if($item->hasChildren()) class="dropdown"@endif class="{{ $item->class }}">
      <a href="{!! $item->url() !!}" style="display:inline-block">{!! $item->title !!} </a>
      @if($item->hasChildren()) <a href="#" class="test dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="display:inline-block"> <span class="caret"></span></a>
        <ul class="dropdown-menu">
              @include('includes.custom-menu-items', array('items' => $item->children()))
        </ul> 
      @endif
  </li>
@endforeach
@section('script')
<script type="text/javascript">
$(document).ready(function(){
  $('.dropdown a.test').on("click", function(e){
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });

if($('#content').val() < 2100){
$('#footer-content').addClass('small-content');
}
else{
	$('#footer-content').removeClass('small-content');
}
});
</script>
@endsection