@extends('layouts.app')
@section('style')
<style type="text/css">
.toggle{
	width: 50% !important;
}
.hidden{
	display: none;
}
.highlight_error{
	border: red 1px solid;
}
</style>
@stop
@include('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top" style="padding: 20px 0;">
		<h1>{{ $header }} Page</h1>
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#main">Main</a></li>
			<li><a data-toggle="tab" href="#template">Meta</a></li>
		</ul>
		&nbsp;
		@if(count($errors) > 0)
		<div class="alert alert-warning alert-dismissible">Error: Highlight fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
		@foreach ($errors->all() as $error)
		@if(strpos($error, 'required') == false)
		<div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
		@endif
		@endforeach
		@endif
		@if(Session::has('message'))
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			{{ Session::get('message') }}
		</div>
		@endif
		<form class="form" action="{{ $action }}" method="post">
			<div class="tab-content">
				<div id="main" class="tab-pane fade in active">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<label>Title:</label>
									<input id="titletext" class="form-control {{ ($errors->has('title')) ? 'highlight_error' : '' }}" name="title" placeholder="Please Enter Title" value="{{ $title }}" required>
									<br>
								</div>
								@if(App\Page::where('parent_id',$page_id)->first())
									<?php 
									$sub_page_id = App\Page::where('parent_id',$page_id)->first()->id;

									$x = $page_id; 
									$array ="";
									?>

									@foreach($pages as $page)
										@while($x == $page->parent_id)
										{{-- 	{{ $page->title}} --}}
											<?php $array .= $page->title; ?>
											<?php $x++; ?>
										@endwhile
									@endforeach
								@else
								<?php 
									$sub_page_id = '';
									$x= ' '; 
								?>
								@endif
	
								<div class="col-sm-6">
									<label>Parent Page:</label>
									{{-- <select class="form-control" name="parent_id" id="parent_name">
										<option value=""></option>
										@foreach($pages as $page)
										<option value="{{$page->id}}" {{ ($parent_id == $page->id) ? 'selected="selected"' : ''}} {{($parent_id == 0 || $page->parent_id == $page_id || $page->parent_id == $sub_page_id)? (isset($title) ? 'disabled' : ' ') : ' '}}>{{ $page->title}}</option >
											@endforeach
									</select> --}}
									<select class="form-control" name="parent_id" id="parent_name">
										<option value=""></option>
									</select>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12">
										<label>Content:</label>
										<textarea class="form-control" rows="3" placeholder="Put Content here" name="content" id="mytextarea">{{ $content }}</textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="pull-right" style="margin-top:30px;margin-bottom:30px">
							<a href="{{ route('page')}}">
								<button type="button" class="btn btn-red"> Cancel</button>
							</a>
							<a data-toggle="tab" href="#template" id="next" class="btn btn-darkblue">
								Next
							</a>
						</div>
					</div>
					<div id="template" class="tab-pane fade">
						<div class="row">
							{{-- <div class="col-sm-6">
								<label>Template:</label>
								<select class="form-control" name="template">
									<option>--Please Select Template--</option>
									<option value="single_column" {{($template == 0) ? 'selected="selected"' : ''}}>Single Column Template</option>
									<option value="two_column" {{($template == 1) ? 'selected="selected"' : ''}}>Two Column Template</option>
									<option value="three_column" {{($template == 2) ? 'selected="selected"' : ''}}>Three Column Template</option>
								</select>
							</div> --}}
							<div class="col-sm-12">
								<label>Meta Title:</label>
								<input class="form-control" name="meta_title" value="{{ $meta_title}}">
							</div>
						</div>
						&nbsp;
						<div class="row">
							<div class="col-sm-12">
								<label>Meta Keywords:</label>
								<input class="form-control" name="keywords" value="{{ $keywords }}">
							</div>
						</div>
						&nbsp;
						<div class="row">
							<div class="col-sm-6">
								<label>Meta Description:</label>
								<textarea class="form-control" name="description">{{ $description }}</textarea>
							</div>
							<div class="col-sm-6">
								<label>Status: </label><br>
									{{-- <input type="checkbox" class="btn btn-primary btn-lg" style="padding:10px" data-toggle="toggle" data-on="Enabled" data-off="Disabled"> --}}
									<input type="checkbox" id="toggle-two">
									<input type="hidden" value="{{ $status }}" name="status" id="status_value">
							</div>
						</div>
						&nbsp;
						<div class="row">
							<div class="col-sm-6">
								<div><b>Order:</b></div>
								<input type="hidden" class="form-control" name="order" id="order" value="{{($page_id == 0)? 1: $order}}" placeholder="Choose Order" min="1" required>
								<input type="text" name="old_order" class="hidden" value="{{ $order }}">
								<div class="col-sm-6" style="padding:5px 5px 0 0">
									<select class="form-control order" id="destination"name="destination">
										<option>Plese Select</option>
										<option class="form-control" value="before" {{ (($last_id != $page_id)?((count($pages) > 0)? 'selected="selected"' : '') : ' ')}}>Before</option>
										<option class="form-control" value="after" {{ ($last_id == $page_id) ? 'selected' : ' '}}>After</option>
									</select>
								</div>
								<div class="col-sm-6"  style="padding:5px 5px 0 0">
									<select class="form-control order" id="page" {{ (count($pages) == 0)? 'disabled' : ' '}}  >
										<option selected="selected" disabled>{{ (count($pages) == 0)? 'No Pages available' : 'Select Page'}}</option>
									{{-- 	@foreach($pages as $page)
										<option value="{{$page->order}}" {{ ($parent_id == $page->id) ? 'selected="selected"' : ''}} {{ ($page_id == $page->id)? 'disabled' : ' '}}>{{ $page->title}}</option >
											@endforeach --}}
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<label>Slug: &nbsp;<span id="display_slug"></span></label>
								<input value="" id="slug_id" name="display_slug" style="display:none">
								<input name="slug" id="slug_field" class="form-control  {{ ($errors->has('slug')) ? 'highlight_error' : '' }}" placeholder="please-enter-slug" value="{{ $slug }}" required>
								<input type="hidden" name="old_parent_id" value="{{ $parent_id}}">
							</div>
						</div>
						
						&nbsp;
						<div class="row">
							<div class="col-sm-6">
								<div class="clearfix">
									<div class="pull-left">
										<a data-toggle="tab" href="#main" id="prev">
											<button type="button" class="btn btn-darkblue">Back</button>
										</a>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="clearfix">
									<div class="pull-right" style=" margin-bottom:30px;">
										{{ csrf_field() }}
										
										<a href="{{ route('page')}}">
											<button type="button" class="btn btn-red"> Cancel</button>
										</a>
										<input type="submit" class="btn btn-darkblue" value="Submit">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">
$("#titletext").on("change keyup paste autocompletechange mouseenter keydown input", function(){
	var title = $('#titletext').val();
	title = title.replace(/\s+/g, '-').toLowerCase();
    $('#slug_field').val(title);

    $('#parent_name').on('change', function() {
        var parent =  $('#parent_name :selected').text();
        parent = parent.replace(/\s+/g, '-').toLowerCase();
        var title = $('#titletext').val();
        title = title.replace(/\s+/g, '-').toLowerCase();
        $('#display_slug').text(parent +'/');
        $('#slug_field').val(title);
        $('#slug_id').val(parent +'/');
    });
});
 $(function() {
    $('#toggle-two').bootstrapToggle({
      on: 'Publish',
      off: 'Hide'
    });
    $('#toggle-two').change(function() {
    	if($(this).prop('checked') == true){
    		var status_value = 'published';
    	}else{
    		var status_value = 'hidden';
    	}
      $('#status_value').val(status_value);
    })

    if($('#status_value').val() == 'published'){
    	$('#toggle-two').bootstrapToggle('on');
    }else{
    	$('#toggle-two').bootstrapToggle('off')
    }
  });
 // $('#destination').click(function(){
 // 	alert($('#destination :selected').val());
 // });
 // $('#page').click(function(){
 // 	alert($('#page :selected').val());
 // });
var filter_pages = '{{ route("filter_pages", TRUE)}}';
var all_pages    = '{{ route("all_pages", TRUE)}}'
var token = '{{ csrf_token() }}';
var pageId = '{{ $page_id }}';
var order_on_load  = '{{ $order }}';
var last_id = '{{ $last_id }}';
var parent_id = '{{ $parent_id }}';
function allPages(post_url,token){
	$.post(all_pages,{_token :token}).done(function(data){
		$('#parent_name').each(function () {
			var mydata= $.parseJSON(data);
			var data_count = Object.keys(mydata).length;
			console.log(mydata);
			var y = pageId;
			for(var x= 0; x < data_count; x++){
				if(parent_id ==  mydata[x]['id']){
					var selected = 'selected ';
				}else{
					var selected = ' ';
				}
				var p_id = [];
				while(y ==  mydata[x]['parent_id']){
					y++; 
					p_id = mydata[x]['parent_id'];
				}
				console.log(p_id);
				if(p_id == mydata[x]['parent_id']){
					var disabled = 'disabled';
				}else{
					var disabled = ' ';
				}
				if(parent_id !== 0){
					if(mydata[x]['parent_id'] == 0){
					var disabled = ' ';
					}
				}
				if(mydata[x]['id'] == pageId){
					var disabled = 'disabled';
				}
				// if(parent_id == 0){
				// 	var disabled ='disabled';
				// }

				$(this).append('<option value="'+ mydata[x]['id'] +'" '+ selected + disabled+' >' + mydata[x]['title'] + '</option>');
			}
		});
	});
}
function filterPages(post_url,field,token){
			$.post(filter_pages,{parent_id: $('#parent_name :selected').val(),_token :token}).done(function(data){
				 $('#page').each(function () {
				 	var mydata= $.parseJSON(data);
				 	var data_count = Object.keys(mydata).length;
				 	if(data_count == 0){
				 		$(this).append('<option selected="selected" value="" disabled>No Page available</option>');
				 		$('#destination').attr('disabled', true);
				 		$('#page').attr('disabled', true);
				 		$('#order').val(1);
				 	}else{
				 		$(this).find('option').remove().end();
				 		$(this).append('<option selected="selected" value="">Plese Select Page</option>');
				 		$('#destination').attr('disabled', false);
				 		$('#page').attr('disabled', false);
				 	}
				 	for(var x= 0; x < data_count; x++){
				 		// $(this).find('option').remove();
				 		if(mydata[x]['id'] == pageId){
				 			var disabled = 'disabled ';
				 		}else{
				 			var disabled = ' ';
				 		}
				 		if(last_id !== pageId){
				 			var destination = (parseInt(order_on_load) + 1);
					 		if(mydata[x]['order'] == destination){
					 			var selected = 'selected';
					 		}else{
					 			var selected = ' ';
					 		}
				 		}else{
				 			var destination = (parseInt(order_on_load) - 1);
					 		if(mydata[x]['order'] == destination){
					 			var selected = 'selected';
					 		}else{
					 			var selected = ' ';
					 		}
				 		}
				 		

				 		if(data_count == 1 && x == 0 && mydata[x]['id'] == pageId){
				 			$('#page').attr('required',false);
				 			$(this).find('option').remove().end();
				 			$(this).append('<option selected="selected" value="" disabled>No Page available</option>');
					 		$('#destination').attr('disabled', true);
					 		$('#page').attr('disabled', true);
				 		}else{
				 			$('#page').attr('required',true);
				 		}
				 		// console.log(x);
				 	
					 	$(this).append('<option value="'+ mydata[x]['order'] +'" '+ disabled + selected +' >' + mydata[x]['title'] + '</option>');
					 
				 }
			    });
			})
		}
$(document).ready(function(){
	filterPages(filter_pages,$('#parent_name :selected').val(),token);
	$('#order').val(order_on_load);

});
$(document).ready(function(){
	allPages(all_pages,token);
});

$('#parent_name').change(function(){
	var option = $('#parent_name :selected').val();
	$('#page').find('option').remove();
	$('#order').val('');
	filterPages(filter_pages,option,token);
});
var pages = '{{ count($pages) }}';
$('.order').on('click',function(){
	$('#order').val(order_on_load);
	if($('#destination :selected').val() =='before'){
		var order = (parseInt($('#page :selected').val()) - 1) ;
	}else{
		var order = (parseInt($('#page :selected').val()) + (1)) ;
	}
	if(pages == 0){
		$('#order').val(1);
	}else{
		$('#order').val(order);
	}
});
if($('#destination :selected').val() =='before'){
	var order = (parseInt($('#page :selected').val()) - 1) ;
}else{
	var order = (parseInt($('#page :selected').val()) + (1)) ;
}
if(pages == 0 || pageId == 0){
	$('#order').val(1);
}else{
	$('#order').val(order);
}
$('#order').val(order_on_load);
$('#parent_name').on('change', function() {
	if($('#parent_name :selected').val() > 0){
		var parent_id = $('#parent_name :selected').val();
	}
});
</script>
@stop