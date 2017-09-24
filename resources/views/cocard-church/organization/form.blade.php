<div class="well">
    <form class="form form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ route('organization_storeorg') }}">
        <div class="form-group{{ $errors->has('name') ? ' highlight_error' : '' }}">
            <input required placeholder="@lang('dashboard_details.name_of_organization')" type="text" class="form-control" name="name" value="{{ old('name') }}">
        </div>
        <div class="form-group{{ $errors->has('contact_person') ? ' highlight_error' : '' }}">
            <input  required placeholder="@lang('dashboard_details.contact_person')" type="text" class="form-control" name="contact_person" value="{{ old('contact_person') }}">
        </div>
        <div class="form-group">
            <select class="form-control" name="position">
                <option  disabled>--@lang('dashboard_details.select_position')--</option>
                <option selected>Admin</option>
            </select>
        </div>
        <div class="row">
            <div class="col-md-8" style="padding: 5px;">
                <div class="form-group">
                    <label for="">isteward.tastradedev.com/organization/</label>
                </div>
            </div>
            <div class="col-md-4" style="padding: 0;">
                <div class="form-group{{ $errors->has('url') ? ' highlight_error' : '' }}">
                    <input  required placeholder="Url Link" type="text" class="form-control" name="url" value="{{ old('url') }}">
                </div>
            </div>
        </div>
        <div class="form-group{{ $errors->has('contact_number') ? ' highlight_error' : '' }}">
            <input  required placeholder="@lang('dashboard_details.contact_number')" type="text" class="form-control tel" name="contact_number" value="{{ old('contact_number') }}">
        </div>
        <div class="form-group{{ $errors->has('email') ? ' highlight_error' : '' }}">
            <input  required placeholder="@lang('dashboard_details.email_address')" type="email" class="form-control" name="email" value="{{ old('email') }}">
        </div>

        <div id="password_field">
            <label class="" for="password">@lang('dashboard_details.password'):</label>
            <div class="input-group">
                <input required  class="form-control" id="passwordfield" name="password" type="password" placeholder="******">
                <div class="input-group-addon">
                    <span class="fa fa-eye float-right" id ="fa-eye-p" aria-hidden="true" style=""></span>
                </div>
            </div>
            <label class="" for="password_confirmation">@lang('dashboard_details.confirm_password'):</label>
            <div class="input-group">
                <input  required class="form-control" id="passwordconffield" name="password_confirmation" type="password" placeholder="******">
                <div class="input-group-addon">
                    <span class="fa fa-eye float-right" id ="fa-eye-cp" aria-hidden="true" style=""></span>
                </div>
            </div>
        </div>

        <div class="form-group ">
            <div class="clearfix">
                <br>
                {!! csrf_field() !!}
                <div class="pull-right">
                    <button type="submit" class="btn btn-darkblue">
                        Submit
                    </button>
                    <a href="{{ url('/organizations') }}" class="btn btn-red">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

@section('script')
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script>
$("form.form").validate({
    rules : {
        password : {
            minlength : 5
        },
        password_confirmation : {
            minlength : 5,
            equalTo : "#passwordfield"
        }
    }
});
</script>
@endsection
