@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Quickbooks</h3>
            </div>
            <div class="col-md-6"> </div>
        </div>
        <div class="table-main panel panel-default">
            <form id="quickbooks-form" class="form-horizontal" method="POST" action="{{ $action }}" >
                <div class="form-group">
                    <label for="qb_company_id">QB_COMPANY:</label><input type="text" class="form-control credentials" id="qb_company_id" name="qb_company_id" value="{{ $qb_company_id or '' }}" placeholder="Leave blank for sandbox mode">
                </div>
                <div class="form-group">
                    <label for="qb_token">QB_TOKEN:</label><input type="text" class="form-control credentials" id="qb_token" name="qb_token" value="{{ $qb_token or '' }}">
                </div>
                <div class="form-group">
                    <label for="qb_consumer_key">QB_CONSUMER_KEY:</label><input type="text" class="form-control credentials" id="qb_consumer_key" name="qb_consumer_key" value="{{ $qb_consumer_key or '' }}">
                </div>
                <div class="form-group">
                    <label for="qb_consumer_secret">QB_CONSUMER_SECRET:</label><input type="text" class="form-control credentials" id="qb_consumer_secret" name="qb_consumer_secret" value="{{ $qb_consumer_secret or '' }}">
                </div>
                {!! csrf_field() !!}
                <div class="clearfix">
                    <div class="pull-right">
                     <span id="quickbooks-connect" style="display:none">Click Here to Connect to QuickBooks <i class='fa fa-hand-o-right fa-lg'></i> <ipp:connectToIntuit></ipp:connectToIntuit></span>
                        <button type="submit" id="save-credentials" class="btn btn-darkblue">Submit</button>
                        <a href="{{ url('/organization/'.$slug.'/administrator/events')}}" class="btn btn-red ">
                            Cancel
                        </a>
                    </div>
                </div>
               
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
<script>
token = "{{ csrf_token() }}";
slug = "{{ $slug }}";

intuit.ipp.anywhere.setup({
    menuProxy: '',
    grantUrl: '{{ route("qb_connect",$organization->id) }}'
    // outside runnable you can point directly to the oauth.php page
});
$("#quickbooks-form").on("submit",function(e){
    e.preventDefault();
    form = $(this);
    $.post(form.prop("action"),form.serialize()).done(function(data){
        alert("Credentials Saved, Please Connect to QuickBooks now");
        $(".credentials").attr("disabled",true);
        $("#save-credentials").hide("fast");
        $("#quickbooks-connect").show("fast");
    });
});
</script>
@endsection
