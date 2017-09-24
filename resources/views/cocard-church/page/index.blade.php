@extends('layouts.app')
@include('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content">
	<div class="margin-mob-top">
			<div class="row">
				<div class="col-md-6">
					<h3> Page List</h3>
				</div>
				<div class="col-md-6">
					<h3> 
						@can('add_page')
							<div class="clearfix">
								<a href="{{ route('page_create')}}" class="btn btn-darkblue float-right">
									<i class="fa fa-file-text" aria-hidden="true"></i> &nbsp;Create New Page
								</a>
							</div>
						@endcan
					</h3>
				</div>
			</div>
			@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				{{ Session::get('message') }}
			</div>
			@elseif(count($errors) > 0)
			@foreach ($errors->all() as $error)
			<div class="alert alert-warning alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<li style="list-style:none">{{ $error }}</li>
			</div>
			@endforeach
			@endif
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<th>Title</th>
						<th>Parent Page</th>
						<th>Slug</th>
						<th>Status</th>
				{{-- 		<th>Order</th> --}}
						<th>Action</th>
					</thead>
					<tbody>
						@foreach($pages as $page)
						<tr>
							<td>{{$page->title}}</td>
							<td>{{ $page->parent->title or 'None'}}</td>
							<td>{{$page->slug}}</td>
							<td>{{$page->status}}</td>
			{{-- 				<td>{{$page->order}}</td> --}}
							<td>
				                @can('edit_page')
				               <a href="{{ route('page_edit', $page->id)}}">
									<span class="fa-stack fa-lg icon-edit">
										<i class="fa fa-square fa-stack-2x"></i>
										<i class="fa fa-pencil fa-stack-1x"></i>
									</span>
								</a>
				                @endcan
				                @can('delete_page')
				               <a class="delete_page" data-id="{{$page->id}}">
									<span class="fa-stack fa-lg icon-delete">
										<i class="fa fa-square fa-stack-2x"></i>
										<i class="fa fa-trash-o fa-stack-1x"></i>
									</span>
								</a>
				                @endcan
				                @include('cocard-church.page.confirmation')
				            </td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@if($pages->count() == 0)
				<div class="norecords">No records to show</div>
				@if($search)
				<div class="for-search">for {{ $search }}</div>
				@endif
				@endif
			</div>
			{!! str_replace('/?', '?', $pages->render()) !!}
	</div>
</div>
@endsection
