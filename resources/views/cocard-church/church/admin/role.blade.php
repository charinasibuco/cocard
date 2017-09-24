@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Role List</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        @can('add_role_permission')
                        <a href="{{ url('/organization/'.$slug.'/administrator/role/create')}}" class="btn btn-darkblue float-right">
                            Add Role&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                        </a>
                        @endcan
                    </div>
                </h3>
            </div>
        </div>
        <div class="table-main panel panel-default">
            @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div>
            @endif
            @include('cocard-church.role.index')
        </div>
    </div>
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
