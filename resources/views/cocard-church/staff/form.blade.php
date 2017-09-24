<div class="col-md-6">
    <form class="form" action="{{ $post_url }}">
        <div class="form-group">
            <label for="status_active">status:</label>
            <label class="checkbox-inline"><input type="radio" name="status" id="status_active" value="Active" @if(is_null($status) || (isset($status) && $status != "Inactive")) checked @endif>Active</label>
            <label class="checkbox-inline"><input type="radio" name="status" id="status_inactive" value="Inactive" @if(isset($status) && $status == "Inactive") checked @endif>Inactive</label>
        </div>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" class="form-control" name="first_name" placeholder="First Name" value="{{ $first_name or "" }}" required="required">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last Name" value="{{ $last_name or "" }}" required="required">
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" id="role" class="form-control" name="role" placeholder="Role" value="{{ $role or "" }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" class="form-control" name="email" placeholder="Email" value="{{ $email or "" }}">
        </div>

        <div class="form-group">
            <label for="email">Contact Number:</label>
            <input type="text" id="contact_number" class="form-control" name="contact_number" placeholder="Contact Number" value="{{ $contact_number or "" }}">
        </div>

        <input type="hidden" id="organization_id" name="organization_id" value="{{ $organization_id }}">
        <a href="{{ url('/organization/'.$slug.'/administrator/staff/') }}" class="btn btn-danger">Back</a>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<div class="col-md-6">&nbsp;</div>
