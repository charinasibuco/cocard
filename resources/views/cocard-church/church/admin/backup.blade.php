@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content" id="activity-log">
    <div class="margin-mob-top">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Backup</h3>
                    </div>
                    <div class="col-md-6">
                     
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                           <h3>
                            <div class="clearfix">
                                <a href="{{ url('/organization/'.$slug.'/administrator/backup/download') }}" class="btn btn-darkblue float-right">
                                    Backup database&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>
                                </a>
                            </div>
                        </h3>
 
                    </div>
                </div>
    </div>
</div>
@endsection
