@extends('layouts.app')
@section('title', 'Users')
@section('styles')
    {{--<link rel="stylesheet" href="{{ asset('css/jquery.steps.css')  }}">--}}
    <link rel="stylesheet" href="{{ asset("js/jquery-confirm/css/jquery-confirm.css") }}">
@stop
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="navbar navbar-default bootstrap-admin-navbar-thin">
                <ol class="breadcrumb bootstrap-admin-breadcrumb">
                    <li><a href="">Dashboard</a></li>
                    <li class="active">Users</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default bootstrap-admin-no-table-panel">
                <div class="panel panel-default bootstrap-admin-no-table-panel">
                    <div class="panel-heading">
                        <div class="text-muted bootstrap-admin-box-title">Users List</div>
                    </div>
                    <div class="bootstrap-admin-no-table-panel-content bootstrap-admin-panel-content collapse in">
                        <div class="row">
                            <div class="col-sm-12">
                                @if(Session::has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        {{ Session::get('message') }}
                                    </div>
                                @elseif(count($errors) > 0)
                                    @foreach ($errors->all() as $error)
                                        <div class="alert alert-warning alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <li style="list-style:none">{{ $error }}</li>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                @can('add_user')<a title="Add a new User" class="btn btn-primary" href="{{ route('user_create') }}"><i class="fa fa-user-plus"></i> Add User</a> @endcan
                            </div>
                            <div class="col-sm-6">
                                <form method="get" action="{{ route('users')}}">
                                    <div class="search form-group" style="float:right">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                                            <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="">
                                            <span class="input-group-btn clearer">
                                                <button class="btn btn-primary" type="button" style="border:none; background-color:transparent; z-index:999; margin-left:-35px; font-size:10px"><a href="{{ route('users')}}">X</a></button>
                                            </span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-secondary" type="submit">Go</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                @include('cocard-church.user.templates.user_list')
                                {{--<table width="100%" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th width="15%"></th>
                                        <th width="25%"><a href="">Email</a></th>
                                        <th width="25%"><a href="">First Name</a></th>
                                        <th width="25%"><a href="">Last Name</a></th>
                                        <th width="10%"><a href="">Position</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($users)
                                        @foreach($users as $row)
                                            <tr>
                                                <td style="text-align:center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Options
                                                            <span class="caret"></span></button>
                                                        <ul class="dropdown-menu">
                                                            @if($row->getRole() == "client")
                                                                <li>
                                                                    <a title="View Results of {{ $row->full_name  }}" href = "{{ route('dashboard_show', $row->id) }}">
                                                                        <i class="fa fa-lg fa-user"></i>View Results
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <a title="View Profile of {{ $row->full_name  }}" href = "">
                                                                    <i class="fa fa-lg fa-user"></i>View Profile
                                                                </a>
                                                            </li>
                                                            @can('assign_role')
                                                            <li>
                                                                <a title="Assign role to {{ $row->full_name  }}" href="" data-toggle="modal" data-target="#{{ $row->id  }}_role_modal">
                                                                    <i class="fa fa-lg fa-eject"></i> Assign Role
                                                                </a>
                                                            </li>
                                                            @endcan
                                                            @can('update_user')
                                                            <li>
                                                                <a title="Edit User {{ $row->full_name  }}" href = "">
                                                                    <i class="fa fa-lg fa-pencil-square-o"></i> Edit User</a>
                                                            </li>
                                                            @endcan
                                                            @can('delete_user')
                                                            @if(Auth::user()->id != $row->id)
                                                                <li>
                                                                    <a title="Delete User {{ $row->full_name  }}" href = "" class="delete_confirmation">
                                                                        <i class="fa fa-lg fa-user-times"></i> Delete User</a>
                                                                </li>
                                                            @endif
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                    <!-- Modal -->
                                                    <div id="{{ $row->id  }}_role_modal" class="modal fade" role="dialog">
                                                        <div class="modal-dialog">
                                                            <!-- Modal content-->
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title"><label for="status">Assign Role</label></h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form action="{{ route('user_assign_role', $row->id) }}" method="post">
                                                                        <select name="role" id="role" class="form-control">
                                                                            <option value="" selected disabled>-Choose Role-</option>
                                                                            @foreach(App\Role::all() as $role)
                                                                                <option value="{{ $role->id  }}"  @if($row->hasRole($role->name)) selected @endif >{{ $role->name }}</option>
                                                                            @endforeach
                                                                        </select>

                                                                        <br/>
                                                                        <button class="btn btn-primary" type="submit">Submit</button>
                                                                        {!! csrf_field() !!}
                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $row->email }}</td>
                                                <td>{{ $row->first_name  }}</td>
                                                <td>{{ $row->last_name  }}</td>
                                           --}}{{--      <td>{{ $row->getRole() }}</td> --}}{{--
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>

                                </table>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset("js/jquery-confirm/js/jquery-confirm.js") }}"></script>
    <script>
//        $.confirm();
       $('.delete_confirmation').on('click',function(){
           var r = confirm('Are you sure you want to delete this User?');
           if(r == false){
               return false;
           }
       });
    </script>
@stop

