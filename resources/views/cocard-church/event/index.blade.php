
<table class="table table-striped" id="tabledata">
	<thead class="theader">
		<th>Event</th>
		<th>Description</th>
		<th>Quantity</th>
		<th>Fee</th>
		<th>Details</th>
		<th>Action</th>
	</thead>
	<tbody>
		@foreach($events as $event)
		<tr>
			<td> {{ $event->name }}</td>
			<td> {{ $event->description }}</td>
			<td> {{ $event->capacity }}</td>
			<td> {{ $event->fee }}</td>
			<td> 
				<a href="{{ url('/organization/'.$slug.'/administrator/events/view-details/'.$event->id) }}" title="View Details">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-newspaper-o fa-stack-1x"></i>
					</span>
				</a>
				@if($event->volunteers_needed > 0)
					<a href="{{ url('/organization/'.$slug.'/administrator/events/view-volunteers/'.$event->id) }}" title="View Volunteers">
						<span class="fa-stack fa-lg icon-edit">
							<i class="fa fa-square fa-stack-2x"></i>
							<i class="fa fa-male fa-stack-1x"></i>
						</span>
					</a>
				@endif
			</td>
			<td>
				<a href="{{ url('/organization/'.$slug.'/administrator/events/edit/'.$event->id) }}" title="Edit">
					<span class="fa-stack fa-lg icon-edit">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-pencil fa-stack-1x"></i>
					</span>
				</a>
				<a href="{{ url('/organization/'.$slug.'/administrator/events/deactivate/'.$event->id)  }}" title="Deactivate">
					<span class="fa-stack fa-lg icon-delete">
						<i class="fa fa-square fa-stack-2x"></i>
						<i class="fa fa-trash-o fa-stack-1x"></i>
					</span>
				</a>
				<a href="" data-toggle="modal" data-target="#event_id{{ $event->id }}">
                    <span class="fa-stack fa-lg icon-edit">
                        <i class="fa fa-square fa-stack-2x"></i>
                        <i class="fa fa-envelope-o fa-stack-1x"></i>
                    </span>
                </a>
			</td>
		</tr>
		 <!-- Modal -->
        <div class="modal fade" id="event_id{{ $event->id }}" role="dialog">
            <div class="modal-dialog">
                  <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Send Message to {{ $event->name }}</h4>
                    </div>
                    <div class="modal-body">
                      <form action=" " method="post" >
                        <label>To:</label>
                 {{--      	@foreach($event->Volunteers as $volunteers)
                      	{{ $volunteers->email}}
                      	@endforeach --}}
                        <br>
                        <label>Subject:</label>
                            <input class="form-control" value="" name="subject" required>
                        <label>Message:</label>
                        <textarea class="form-control" value="" name="message" required></textarea>
                    </div>
                    <div class="modal-footer">
                        {!! csrf_field() !!}
                      <button type="submit" class="btn btn-default">Send</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                  </div>
                </div>
            </div>
        </div>
		@endforeach
	</tbody>
</table>
