@extends('layouts.app')
@section('title') {{ $organization->name}}   @endsection
@section('content')
<div class="bg-banner" style="padding:130px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-offset-3 col-md-6">
                <div class="transparent-box" style="margin-top:0;">
                    <h1 style="text-align:center";>{{ ($organization)?$organization->name:''}}</h1>
                    <img src="{{asset('images/icons/user_icon.png')}}" alt="user icon" />
                    @if(Session::has('message'))
                    <div class="col-md-offset-1 col-md-10">
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ Session::get('message') }}
                        </div>
                    </div>
                    @endif
                    <h5 style="text-align:center;"><b>LOGIN AS:</b></h5>
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('post_multiple_user_login') }}">
                        <div class="row">
                            <div class="col-md-offset-2 col-md-8">
                                <div class="form-group">
                                        <input type="hidden" name="slug" value="{{ ($organization)?$organization->url:'' }}">
                                        <input type="hidden" name="id" value="{{ ($organization)?$organization->id:'' }}">
                                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                </div>
                                <select  class="form-control" name="role_id" value="{{ old($role_id) }}">
                                @foreach($user_roles as $user_role)
                                <?php
                                    $roles = App\Role::where('id',$user_role->role_id)->get();
                                ?>
                                    @foreach($roles as $role)
                                            <option  value="{{ $role->id }}"  @if($role->id == $role_id) selected="selected" @endif>{{ ucfirst($role->title) }}</option>
                                    @endforeach
                                @endforeach
                                </select>
                                <br>
                                <div class="form-group">
                                    <div class="col-md-6 col-xs-6">
                                    {!! csrf_field() !!}
                                    <button type="submit" class="btn btn-green btn-full center">
                                        <i class="fa fa-btn fa-sign-in"></i>&nbsp;Login
                                    </button>
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                    <a @if(Auth::user()->hasRole('member')) href="{{ url('organization/'.$slug. '/user/dashboard') }}" @else href="{{ url('organization/'.$slug. '/administrator/dashboard') }}" @endif class="btn btn-green btn-full center">
                                        Cancel
                                    </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function() {
    var user_email = $('#user_email').val();
    // $('#pw').css('display','none');

    $('select').change(function() {
        if(this.value == user_email){
            $('#pw').css('display','none');
        }else{
            $('#pw').css('display','block');
        }
    });

    function dropdownBox() {
        if ($('#dropdown_email').val() == user_email){
            $('#pw').css('display','none');
        }else{
            $('#pw').css('display','block');
        }
    }

    dropdownBox();
});
</script>
@endsection
