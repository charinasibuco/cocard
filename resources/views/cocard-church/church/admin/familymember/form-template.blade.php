@if($action_name == "Add")
<fieldset>
    <div class="well add-family-form">
        <div class="row">
            <div class="col-md-6" style="padding-top:10px;">
                <div class="search form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                        <input type="text" class="search-form form-control search_text_{{ $count }}" placeholder="Search Members" name="term" aria-describedby="basic-addon1" value="" style="width: 500px;">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" class="form-control" name="item[{{ $count  }}][family_id]" value="{{ $family_id }}">
        <input type="hidden" class="form-control" id="user_id_{{ $count  }}" name="item[{{ $count  }}][user_id]" value="{{ $user_id }}">
        <div class="col-md-4">
            <div class="form-group">
                <label for="inputEmail3">Firstname</label>
                <input required type="text" id="first_name_{{ $count }}" class="form-control" placeholder="First name" name="item[{{ $count  }}][first_name]" value="{{ $first_name }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="inputPassword3">Middlename</label>
                <input type="text" id="middle_name_{{ $count }}" class="form-control" placeholder="Middle name" name="item[{{ $count }}][middle_name]" value="{{ $middle_name }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="inputPassword3">Lastname</label>
                <input required type="text" id="last_name_{{ $count }}" class="form-control" placeholder="Last name" name="item[{{ $count }}][last_name]" value="{{ $last_name }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="first-name">Birthdate:</label>
                <div class="form-group">
                    <div class='input-group bdatepicker'>
                        <input  placeholder="M/D/YYYY" id="membdate_{{ $count }}" type="text" class="form-control" name="item[{{ $count }}][birthdate]">
                        <input type="hidden" class="value" >
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="inputPassword3" class="control-label">Gender</label>
                <select id="gender_{{ $count }}" class="form-control" name="item[{{ $count }}][gender]" value="{{ $gender }}">
                    <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                    <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label hidden for="inputPassword3" class="control-label">Allergies</label>
                <input type="hidden" class="form-control" placeholder="Allergies" name="item[{{ $count }}][allergies]" value="{{ $allergies }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label hidden for="inputPassword3" class="control-label">Child Number</label>
                <input type="hidden" class="form-control" placeholder="Child Number" name="item[{{ $count }}][child_number]" value="{{ $child_number }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="inputPassword3" class="control-label">Image</label>
                <input type="file" id="img_{{ $count }}" class="form-control" placeholder="Image" name="item[{{ $count }}][img]" value="{{ $img }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="inputPassword3" class="control-label">Relationship</label>
                <input type="text" class="form-control" placeholder="Relationship" name="item[{{ $count }}][relationship]" value="{{ $relationship }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="inputPassword3" class="control-label">Additional Info</label>
                <input type="text" class="form-control" placeholder="Additional Information" name="item[{{ $count }}][additional_info]" value="{{ $additional_info }}">
            </div>
        </div>
    </div>
<a href="javascript:void(0)" type="button" id="close_{{ $count }}" class="btn btn-default delete-family-member" >
        <i class="fa fa-minus" aria-hidden="true"></i>
</a>
</fieldset>
@else
<div class="well add-family-form">
    <input type="hidden" class="form-control" name="family_id" value="{{ $family_id }}">
    <input type="hidden" class="form-control" id="user_id" name="user_id" value="{{ $user_id }}">
    <div class="col-md-4">
        <div class="form-group">
            <label for="inputEmail3">Firstname</label>
            <input required type="text" id="first_name" class="form-control" placeholder="First name" name="first_name" value="{{ $first_name }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="inputPassword3">Middlename</label>
            <input type="text" id="middle_name" class="form-control" placeholder="Middle name" name="middle_name" value="{{ $middle_name }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="inputPassword3">Lastname</label>
            <input required type="text" id="last_name" class="form-control" placeholder="Last name" name="last_name" value="{{ $last_name }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="first-name">Birthdate:</label>
            <div class="form-group">
                <div class='input-group datepicker dtp'>
                    @if($birthdate == '11/30/-0001')
                    <input placeholder="M/D/YYYY" type="text" class="form-control" name="birthdate"  value="" >
                    @else
                    <input placeholder="M/D/YYYY" type="text" class="form-control" name="birthdate"  value="{{ $birthdate }}" >
                    @endif
                    <input type="hidden" class="value" >
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="inputPassword3" class="control-label">Gender</label>
            <select id="gender" class="form-control" name="gender" value="{{ $gender }}">
                <option value="Male" @if($gender == 'Male') selected @endif>Male</option>
                <option value="Female" @if($gender == 'Female') selected @endif>Female</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="inputPassword3" hidden class="control-label">Allergies</label>
            <input type="hidden" class="form-control" placeholder="Allergies" name="allergies" value="{{ $allergies }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="inputPassword3" hidden class="control-label">Child Number</label>
            <input type="hidden" hidden class="form-control" placeholder="Child Number" name="child_number" value="{{ $child_number }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="inputPassword3" class="control-label">Image</label>
            <input type="file" id="img" class="form-control" placeholder="Image" name="img" value="{{ $img }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="inputPassword3" class="control-label">Relationship</label>
            <input type="text" class="form-control" placeholder="Relationship" name="relationship" value="{{ $relationship }}">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="inputPassword3" class="control-label">Additional Info</label>
            <input type="text" class="form-control" placeholder="Additional Information" name="additional_info" value="{{ $additional_info }}">
        </div>
    </div>
</div>
@endif
