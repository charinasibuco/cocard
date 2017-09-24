@extends('layouts.app')
@extends('cocard-church.superadmin.navigation')
@section('content')
<div class="d-content ">
    <div class="" id="organizations">
        <div class="margin-mob-top">
            <br>
            @if(count($errors) > 0)
            <?php $required =  $errors->first()?>
            @if(strpos($required, 'required'))
            <div class="alert alert-warning alert-dismissible">@lang('messages.required_error') <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
            @endif
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
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" action="">
                        <div class="search form-group">
                            <div class="input-group" style="width: 500px;">
                                <span class="input-group-addon" id="basic-addon1">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="{{ $search }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-darkblue" type="submit" style="float: right;">@lang('dashboard_details.go')</button>
                                </span>
                            </div>
                         </div>
                     </form>
                </div>
                <div class="col-md-6">
                    <h3>
                        <div class="clearfix">
                            <a href="{{ url('register-superadmin')}}" class="btn btn-darkblue float-right" title="@lang('dashboard_details.view_admins')">
                                Add Organization&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                        </div>
                    </h3>
                </div>
            </div>
                {!! $organization->render() !!} 
            <div class="row">
                <div class="table-responsive" style="width:100%;">
                    <table class="table table-striped table-hover org-table">
                        <thead>
                            <tr>
                                <th><a title = "Sort by Organization Name" href="{{url('/organizations').'?order_by=name&sort='. $sort}}"> @lang('dashboard_details.organization_name')</a></th>
                                <th><a title = "Sort by URL" href="{{url('/organizations').'?order_by=url&sort='. $sort}}">URL</a></th>
                                <th><a title = "Sort by Status" href="{{url('/organizations').'?order_by=status&sort='. $sort}}"> @lang('dashboard_details.status')</a></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organization as $row)
                            @include('cocard-church.superadmin.confirmation')
                            <tr>
                                <td data-th="Organization Name">{{ $row->name }}</td>
                                @if( $row->status == "Declined" || $row->status == "Pending"|| $row->status == "InActive")
                                <td data-th="URL" style="text-align:center;"><a title="Links for Declined or Pending are not Available" style="color:blue;" href="#">-</a></td>
                                @else
                                <td data-th="URL"><a title="Go to Website" style="color:blue;" href="{{ url('/organization/'.$row->url .'/administrator/dashboard')}}">isteward.tastradedev.com/organization/{{ $row->url }}</a></td>
                                @endif
                                <td data-th="Status">{{ $row->status }}</td>
                                <td>
                                    @if( $row->status == "InActive" )
                                        <a href="{{ route('org_list_admins',$row->id)}}" title="@lang('dashboard_details.view_admins')">
                                            <span class="fa-stack fa-lg icon-edit">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-eye fa-stack-1x"></i>
                                            </span>
                                        </a>
                                        <button type="submit" class="btn btn-darkblue activate" title="Activate Organization" data-id="{{$row->id}}">
                                            @lang('dashboard_details.activate')
                                        </button>
                                    @endif
                                    @if( $row->status == "Declined" )
                                        <a title="Approve Organization"href=" {{ route('pending_organization_review_pending',$row->id) }}">
                                            <button type="submit" class="btn btn-darkblue">
                                                @lang('dashboard_details.approve')
                                            </button>
                                        </a>
                                    @endif
                                    @if( $row->status == "Pending" )
                                        <a title="Approve Organization"href=" {{ route('pending_organization_review_pending',$row->id) }}">
                                            <button type="submit" class="btn btn-darkblue">
                                                <i class="icon-like" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                        <a title="Decline Organization"href="{{ route('pending_organization_update_declined',$row->id) }}">
                                            <button type="submit" class="btn btn-red">
                                                <i class="icon-dislike" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    @endif 
                                    @if( $row->status == "Active" )
                                        <a href="{{ route('org_list_admins',$row->id)}}" title="@lang('dashboard_details.view_admins')">
                                            <span class="fa-stack fa-lg icon-edit">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-eye fa-stack-1x"></i>
                                            </span>
                                        </a>
                                        <a href="{{ route('pending_organization_review',$row->id) }}"  title="Modify Organization">
                                            <span class="fa-stack fa-lg icon-edit">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-pencil fa-stack-1x"></i>
                                            </span>
                                        </a>
                                        <a class="deactivate" data-id="{{$row->id}}" style="cursor:pointer;" title="Deactivate Organization">
                                            <span class="fa-stack fa-lg icon-delete">
                                                <i class="fa fa-square fa-stack-2x"></i>
                                                <i class="fa fa-trash-o fa-stack-1x"></i>
                                            </span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                @if($organization->count() == 0)
                <div class="norecords_prompt">No records to show</div>
                @if($search)
                <div class="for_search">for {{ $search }}</div>
                @endif
                @endif
                {!! $organization->render() !!}
            </div>
        </div>
    </div>
</div>

@endsection
