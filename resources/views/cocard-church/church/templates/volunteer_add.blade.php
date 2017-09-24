<div id="volunteer-{{ $count }}-container">
    <div class="form-group">
        <label for="volunteer-{{ $count }}-name">Name: </label><input class="form-control volunteer-name" type="text" id="volunteer-{{ $count }}-name" name="volunteer[{{ $count }}][name]" placeholder="name" required>
    </div>
    <div class="form-group">
        <label for="volunteer-{{ $count }}-email">Email: </label><input class="form-control volunteer-email" type="email" id="volunteer-{{ $count }}-email" data-unique_url="{{ route('volunteer_unique_email',$event_id) }}" name="volunteer[{{ $count }}][name]" placeholder="email" unique="email" required>
    </div>
    <div class="form-group">
        <label for="volunteer-{{ $count }}-role">Role: </label>
        <select class="form-control" type="text" id="volunteer-{{ $count }}-role" name="volunteer_role_id">
            <option value="">None</option>
            @foreach($event->VolunteerRoles() as $roles)
                <option value="{{ $role->id }}" >{{ $roles->title }}</option>
            @endforeach
        </select>
    </div>
</div>