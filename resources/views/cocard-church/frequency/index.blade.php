@extends('layouts.app')
@section('title', 'Frequency')
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
                    <li class="active">Frequency</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default bootstrap-admin-no-table-panel">
                <div class="panel panel-default bootstrap-admin-no-table-panel">
                    <div class="panel-heading">
                        <div class="text-muted bootstrap-admin-box-title">Frequency List</div>
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
                               <a title="Add a new Frequency" class="btn btn-primary" href="{{ route('frequency_create') }}"><i class="fa"></i> Add Frequency</a>
                            </div>
                            <div class="col-sm-6">
                                <form method="get" action="{{ route('frequency')}}">
                                    <div class="search form-group" style="float:right">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                                            <input type="text" class="search-form form-control" placeholder="Search" name="search" aria-describedby="basic-addon1" value="">
                                            <span class="input-group-btn clearer">
                                                <button class="btn btn-primary" type="button" style="border:none; background-color:transparent; z-index:999; margin-left:-35px; font-size:10px"><a href="">X</a></button>
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
                                <table width="100%" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th width="15%"></th>
                                        <th width="25%"><a href="">Title</a></th>
                                        <th width="25%"><a href="">Description</a></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($frequency as $row)
                                        <tr>

                                            <td style="text-align:center">
                                                <div class="dropdown">
                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Options
                                                        <span class="caret"></span></button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a title="Edit Frequency {{ $row->title  }}" href="{{ route('frequency_edit',$row->id) }}">
                                                                <i class="fa fa-lg fa-pencil-square-o"></i> Edit Frequency</a>
                                                        </li>

                                                        @if(Auth::user()->id != $row->id)
                                                            <li>
                                                                <a title="Delete Frequency {{ $row->title  }}" href = "{{ route('frequency_destroy',$row->id) }}" class="delete_confirmation">
                                                                    <i class="fa fa-lg fa-user-times"></i> Delete Frequency</a>
                                                            </li>
                                                        @endif

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

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $row->title }}</td>
                                            <td>{{ $row->description  }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                                @if($frequency->count() == 0)
                                    <div style="text-align:center; color:#333; font-size:18px">No records to show</div>
                                    @if($search)
                                        <div style="text-align:center; color:#333; font-size:18px">for {{ $search }}</div>
                                    @endif
                                @endif

                                {!! str_replace('/?', '?', $frequency->render()) !!}
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
