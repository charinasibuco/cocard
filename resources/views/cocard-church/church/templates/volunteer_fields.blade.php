<style type="text/css">
.fieldset-border{
    border: #DCDCDC solid 1px;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}
.delete-btn{
    padding: 2px 8px 2px 8px;
    background-color: #F05656;
    border-radius: 2px;
    color:#FFF;
}
</style>
<fieldset class="volunteer-fieldset fieldset-border" id="{{ $event->id }}-{{ $count }}-volunteer-fieldset" style="display:none">
    <a href="javascript:void(0)" class="delete-volunteer pull-right"><span class="delete-btn">x</span></a> &nbsp;
    <div class="form-group">
        <label for="{{ $event->id }}-{{ $count  }}-name">
            Name: </label><input class="form-control" type="text" id="{{ $event->id }}-{{ $count  }}-name" name="volunteers[{{ $count }}][name]" placeholder="name" required>
    </div>
    <div class="form-group">
        <label for="{{ $event->id }}-{{ $count  }}-email">Email: </label><input class="form-control" type="email" id="{{ $event->id }}-{{ $count  }}-email" data-unique_url="{{ route('volunteer_unique_email',$event->id) }}" name="volunteers[{{ $count }}][email]" placeholder="email" unique="email" required>
    </div>
    <div class="form-group">
        {{-- <label for="{{ $event->id }}-volunteer_group_id">Group: </label>
        <select class="form-control" type="text" id="{{ $event->id }}-volunteer_group_id" name="volunteers[{{ $count }}][volunteer_group_id]" required>
            <option value="" selected disabled>Select A Group</option>
            @foreach($event->volunteer_groups as $group)
                @if($group->available_slots > 0)
                    <option value="{{ $group->id }}" >{{ $group->type }}</option>
                @endif
            @endforeach
        </select> --}}
        <input type="hidden" value="{{ $volunteer_group->id}}" id="{{ $event->id }}-volunteer_group_id" name="volunteers[{{ $count }}][volunteer_group_id]">
    </div>
</fieldset>
