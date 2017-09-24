<form class="form-horizontal" role="form" method="POST" action="{{ route('settings_update',$id) }}" enctype="multipart/form-data">
    <input type="hidden" name="slug" value="{{ $slug }}">
    <div class="row">
        <div class="col-md-5">
            <label for="">Logo</label>
            <div class="form-group{{ $errors->has('logo') ? ' highlight_error' : '' }} ">
                <input type="file" class="form-control" placeholder="Logo" name="logo" value="{{ $logo }}">
            </div>
        </div>
        <div class="col-md-offset-1 col-md-5">
            <label for="">Banner Image</label>
            <div class="form-group{{ $errors->has('banner_image') ? ' highlight_error' : '' }} ">
                <input type="file" class="form-control" placeholder="Logo" name="banner_image" value="{{ $banner_image }}">
            </div>
        </div>
    </div>
    <h4 class="text-center">Change Color Scheme</h4>
    <div class="row">
        <div class="col-md-5">
            <label for="">Top Navbar Background</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme1}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Background Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme2}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Footer Background</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme3}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Button Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme4}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Side Navigation <small>(User Dashboard)</small></label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme5}}" class="form-control color_scheme" readonly>
            </div>
        </div>
        <div class="col-md-offset-1 col-md-5">
            <label for="">Header Text Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme6}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Text Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme7}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Link Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme8}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Button Text Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme9}}" class="form-control color_scheme" readonly>
            </div>
            <label for="">Navigation Text Color</label>
            <div class="form-group{{ $errors->has('$scheme') ? ' highlight_error' : '' }} ">
                <input type="text" name="scheme[]" value="{{$scheme10}}" class="form-control color_scheme" readonly>
            </div>
        </div>
    </div>
    <h4 class="text-center">NMI Account Credentials</h4>
    <div class="row">
        <div class="col-md-5">
            <label for="">Username</label>
            <div class="form-group{{ $errors->has('$nmi_user') ? ' highlight_error' : '' }}">
                <input type="text" class="form-control" placeholder="Demo" name="nmi_user" value="{{ $nmi_user }}">
            </div>
        </div>
        <div class="col-md-offset-1 col-md-5">
            <label for="">Password</label>
            <div class="form-group{{ $errors->has('$nmi_pass') ? ' highlight_error' : '' }}">
                <input type="password" class="form-control" placeholder="Demo123" name="nmi_pass" value="{{ $nmi_pass }}">
            </div>
        </div>
        <div class="col-md-11">
        <div class="form-group">
            {!! csrf_field() !!}
            <button type="submit" class="btn btn-darkblue float-right">
                Submit
            </button>
        </div>
        </div>
    </div>

</form>
