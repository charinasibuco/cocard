@extends('layouts.app')
@include('cocard-church.church.navigation')
@section('content')
<div class="modal fade calendar-modal" id="event-modal" tabindex="-1" role="dialog" data-slug="{{ $slug }}" data-event_modal_url="{{ route('volunteer_group_modal_details') }}" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<button type="button" class="btn btn-darkblue" data-toggle="modal" data-target=".bs-example-modal-sm">View Cart</button>
			
		</div>
	</div>
</div>
<br/>
<div class="container content" style="min-height:400px;">
	<div class="row">
		<div class="col-md-12">
			<div class="clearfix" style="margin-bottom:15px; display:none" id="main">
		        <div class="pull-right">
		            <a href="{{ url('/organization/'.$slug.'/home') }}" class="btn btn-green">
		                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
		            </a>
		        </div>
		    </div>
		    <div class="clearfix" style="margin-bottom:15px; display:none" id="group">
		        <div class="pull-right">
		            <a onclick="volunteerMainTable(this)" class="btn btn-green">
		                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
		            </a>
		        </div>
		    </div>
		</div>
	</div>
{{-- 	<div class="row">
		<div class="col-md-12">
			<div class="volunteer-role-filter-container">
				<div class="row">
					<div class="clearfix" style="margin-bottom:15px;">
				        <div class="pull-right">
				            <a href="{{ url('/organization/'.$slug.'/home') }}" class="btn btn-green">
				                <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
				            </a>
				        </div>
				    </div>
					<div class="col-md-2">
						<label class="control-label" for="role-filter-input">Filter by Role:</label>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select id="role-filter-input" class="form-control" type="text" name="volunteer_role_id">
								<option value="" selected>None</option>
								@foreach($volunteer_role_titles as $title)
									<option value="{{ $title }}">{{ $title }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-7">
						&nbsp;
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="event-list-container">
	</div> --}}
	{{-- 	<div class="row">
            <div class="col-xs-6">
                <h3>Volunteer Groups</h3>
            </div>
            <div class="col-xs-6" style="padding-left:0">
                <h3>Events</h3>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-xs-12">
                <h3>List of Volunteer Groups</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12" id="volunteer-main-table">
                
            </div>
           {{--  {{ $volunteer_groups->render() }} --}}
        </div>

    </div>
</div>
@include('includes.footer')
@endsection

@section('script')
	<script type="text/javascript">
		//var filter_events = "{{ route('filter_events',TRUE) }}";
		var filter_events = "{{ route('filter_events',[TRUE,$slug]) }}";
		var token = "{{ csrf_token() }}";
		function filterEvents(post_url,title,token){
			$.post(post_url,{_token:token,role_title: title,slot_filter: "filtered"}).done(
				function(data){
					$("#event-list-container").empty();
					$("#event-list-container").html(data);
				}
			);
		}

		function volunteerTable(){
		    var slug ='{{ $slug }}';
		    var volunteer_group_id= '{{ $volunteer_groups }}';
		    $.get('{{ route("volunteer_table",$page) }}',{slug:slug}).done(function(data){
		        $('#volunteer-table').empty().html(data);
		    });
		    $('#pagination').css('display','block');
		}
		function volunteerMainTable(){
		    var slug ='{{ $slug }}';
		    var volunteer_group_id= '{{ $volunteer_groups }}';
		    $.get('{{ route("volunteer_main_table",$page) }}',{slug:slug}).done(function(data){
		        $('#volunteer-main-table').empty().html(data);
		    });
		    $('#pagination').css('display','block');
		}

		loadEvent = function(e){
		    var event = new Object();
		    event.id = $(e).data('volunteer_id');
		    event.volunteer_group_id = $(e).data('volunteer_id');
		    loadEventDetails(event);
		}

		$(document).ready(function(){
			//filterEvents(filter_events,"",token);
			//volunteerTable();
			volunteerMainTable();
		});
		//alert(events);


		$('#role-filter-input').change(function(){
			filterEvents(filter_events,$("#role-filter-input").val(),token);
		});

		$('#event-modal').on('hidden.bs.modal', function () {
		     volunteerTable();
		});
	</script>
@endsection
