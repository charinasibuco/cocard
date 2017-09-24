@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>{{ $name }} Family Members</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('organization/'. $slug .'/administrator/family/'.$family_id.'/family-member/create') }}"  class="btn btn-darkblue float-right">
                            Add Family Member&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            @include('cocard-church.church.admin.familymember.index')
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
$(".delete_modal").click( function (){
	var id = $(this).attr('data-id');
    $('.delete-confirmation[data-id="' + id + '"]').css('display','block');
});
$(".hide_modal").click( function (){
	var id = $(this).attr('data-id');
    $('.delete-confirmation[data-id="' + id + '"]').css('display','none');
});
});
</script>
@endsection
