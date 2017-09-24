
<form class="form-horizontal" method="POST" action="" >
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Organization</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" name="organization_id" value="{{ $organization->id }}" >
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Event name</label>
        <div class="col-sm-9">
            <input type="hidden" name="slug" value="{{ $slug }}" >
            <input type="text" class="form-control" placeholder="Event name" name="name" value="{{$name}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Description</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" placeholder="Description" name="description" value="{{$description}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Capacity</label>
        <div class="col-sm-9">
            <input type="number" class="form-control" placeholder="Capacity" name="capacity" value="{{$capacity}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Fee</label>
        <div class="col-sm-9">
            <input type="number" class="form-control" placeholder="Fee" name="fee" value="{{$fee}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Start date</label>
        <div class="col-sm-9">
            <input type="text" placeholder="From Date & Time" id="fdate" class="form-control" name="start_date" value="{{$start_date}}">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">End date</label>
        <div class="col-sm-9">
            <input type="text" placeholder="To Date & Time" id="tdate"  class="form-control" name="end_date"  value="{{$end_date}}">
        </div>
    </div>

    <div class="btn-right">
        {!! csrf_field() !!}
        <button type="submit" class="btn btn-darkblue float-right" id="edit_button">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Save
        </button>
    </div>
    <br>
</form>
