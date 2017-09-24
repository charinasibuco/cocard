@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content" id="organizations" >
    <div class="margin-mob-top">
        <form method="get" action="{{ route('pending_organization_status',$status)}}">
            <div class="search form-group">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="">
                    <span class="input-group-btn clearer">
                        <button class="btn btn-primary" type="button" id="x_button">
                            <a href="#">X</a>
                        </button>
                    </span>
                    <span class="input-group-btn">
                        <button class="btn btn-darkblue" type="submit">@lang('dashboard_details.go')</button>
                    </span>
                </div>
            </div>
        </form>

        <table width="100%" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><a href="">@lang('dashboard.organization') @lang('dashboard_details.name')</a></th>
                    <th><a href="">@lang('dashboard_details.status')</a></th>
                    <th><a href="">@lang('dashboard_details.contact_person')</a></th>
                    <th><a href="">@lang('dashboard_details.contact_number')</a></th>
                    <th><a href="">@lang('dashboard_details.email')</a></th>
                    <th><a href="">@lang('dashboard_details.action')</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingOrganizationUser as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->contact_person }}</td>
                    <td>{{ $row->contact_number }}</td>
                    <td>{{ $row->email }}</td>

                    @if( $row->status == "InActive" )
                    <td>
                        <a title="Activate Organization"href=" {{ route('pending_organization_update_active',$row->id) }}">
                            <button type="submit" class="btn btn-primary btn-green">
                                @lang('dashboard_details.activate')
                            </button>
                        </a>
                    </td>
                    @endif
                    @if( $row->status == "Active" )
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <a title="Modify Organization"href="{{ route('pending_organization_review',$row->id) }}">
                                    <button type="submit" class="btn btn-darkblue">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </button>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a title="Deactivate Organization"href="{{ route('pending_organization_update_inactive',$row->id) }}">
                                    <button type="submit" class="btn btn-red">
                                        <i class="fa fa-power-off" aria-hidden="true"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </td>
                    @endif
                    @if( $row->status == "Declined" )
                    <td>
                        <a title="Approve Organization"href=" {{ route('pending_organization_review_pending',$row->id) }}">
                            <button type="submit" class="btn btn-darkblue">
                                @lang('dashboard_details.approve')
                            </button>
                        </a>
                    </td>
                    @endif
                    @if( $row->status == "Pending" )
                    <td>
                        <a title="Approve Organization"href=" {{ route('pending_organization_review_pending',$row->id) }}">
                            <button type="submit" class="btn btn-darkblue">
                                @lang('dashboard_details.approve')
                            </button>
                        </a>
                        <a title="Decline Organization"href="{{ route('pending_organization_update_declined',$row->id) }}">
                            <button type="submit" class="btn btn-red float-left">
                                @lang('dashboard_details.decline')
                            </button>
                        </a>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
