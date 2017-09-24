@if(Session::has('message'))
	<div class="alert alert-success alert-dismissible" role="alert">
	    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      {{ Session::get('message') }}
	</div>
@endif
@if(count($errors) > 0)
	<div class="alert alert-warning alert-dismissible">Error: Highlighted fields are required! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
	@foreach ($errors->all() as $error)
		@if(strpos($error, 'required') == false)
			<div class="alert alert-warning alert-dismissible">{{ $error }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
		@endif
	@endforeach
@endif

<form class="form-horizontal " method="post" action="{{ $action }}">
	<input type="hidden" class="form-control" name="slug" value="{{ $slug }}">
	<div class="form-group">
		<label class="col-sm-3 control-label" >Role Title</label>
		<div class="col-sm-9">
			<input class="form-control {{ ($errors->has('title')) ? 'highlight_error' : '' }}" name="title" placeholder="Role Title" value="{{ $title }}" required>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" >Role Description</label>
			<div class="col-sm-9">
				<input class="form-control {{ ($errors->has('description')) ? 'highlight_error' : '' }}" name="description" placeholder="Role Description" value="{{ $description }}" style="margin-top:8px;">
				<input type="hidden" name="slug" value="{{ $slug }}" required>
			</div>
	</div>
	<fieldset>
		<div class="well role">
			<ul class="rolelist">
				<li style="display: none;"><input type="checkbox" name="permission[]" id="permission_1" value="1" class="permission" checked="checked" {{ (isset($permission_role)?((in_array('1', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_1">Able to login to admin dashboard</label></li>
				<li class="members"><h4>MEMBERS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_2" value="2" class="permission" {{ (isset($permission_role)?((in_array('2', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_2">Able to view members page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_3" value="3" class="permission" {{ (isset($permission_role)?((in_array('3', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_3">Able to add a member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_4" value="4" class="permission" {{ (isset($permission_role)?((in_array('4', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_4">Able to edit a member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_5" value="5" class="permission" {{ (isset($permission_role)?((in_array('5', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_5">Able to delete a member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_6" value="6" class="permission" {{ (isset($permission_role)?((in_array('6', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_6">Able to view a member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_7" value="7" class="permission" {{ (isset($permission_role)?((in_array('7', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_7">Able to assign a role to a member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_8" value="8" class="permission" {{ (isset($permission_role)?((in_array('8', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_8">Able to assign a member to a family</label></li>
					</ul>
				<li><h4>EVENTS</h4></li>
				<hr class="hr-bottom">

					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_9" value="9" class="permission" {{ (isset($permission_role)?((in_array('9', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_9">Able to view events page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_10" value="10" class="permission" {{ (isset($permission_role)?((in_array('10', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_10">Able to add an event</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_11" value="11" class="permission" {{ (isset($permission_role)?((in_array('11', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_11">Able to edit an event</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_12" value="12" class="permission" {{ (isset($permission_role)?((in_array('12', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_12">Able to delete an event</label></li>
					</ul>
				<li><h4>DONATIONS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_13" value="13" class="permission" {{ (isset($permission_role)?((in_array('13', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_13">Able to view donations page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_14" value="14" class="permission" {{ (isset($permission_role)?((in_array('14', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_14">Able to view donation categories</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_15" value="15" class="permission" {{ (isset($permission_role)?((in_array('15', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_15">Able to add donation category</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_16" value="16" class="permission" {{ (isset($permission_role)?((in_array('16', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_16">Able to edit donation category</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_17" value="17" class="permission" {{ (isset($permission_role)?((in_array('17', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_17">Able to delete donation category</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_18" value="18" class="permission" {{ (isset($permission_role)?((in_array('18', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_18">Able to view donation lists</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_19" value="19" class="permission" {{ (isset($permission_role)?((in_array('19', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_19">Able to add donation list</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_20" value="20" class="permission" {{ (isset($permission_role)?((in_array('20', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_20">Able to edit donation list</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_21" value="21" class="permission" {{ (isset($permission_role)?((in_array('21', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_21">Able to delete donation list</label></li>
					</ul>
				<li><h4>FAMILY</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_22" value="22" class="permission" {{ (isset($permission_role)?((in_array('22', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_22">Able to view family page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_23" value="23" class="permission" {{ (isset($permission_role)?((in_array('23', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_23">Able to add a family</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_24" value="24" class="permission" {{ (isset($permission_role)?((in_array('24', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_24">Able to edit a family</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_25" value="25" class="permission" {{ (isset($permission_role)?((in_array('25', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_25">Able to delete a family</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_26" value="26" class="permission" {{ (isset($permission_role)?((in_array('26', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_26">Able to view family members</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_27" value="27" class="permission" {{ (isset($permission_role)?((in_array('27', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_27">Able to add family member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_28" value="28" class="permission" {{ (isset($permission_role)?((in_array('28', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_28">Able to edit family member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_29" value="29" class="permission" {{ (isset($permission_role)?((in_array('29', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_29">Able to delete family member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_30" value="30" class="permission" {{ (isset($permission_role)?((in_array('30', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_30">Able to view specific family member</label></li>
					</ul>
				<li><h4>VOLUNTEERS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_31" value="31" class="permission" {{ (isset($permission_role)?((in_array('31', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_31">Able to view volunteers page</label></li>
					</ul>
				<li><h4>STAFF</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_32" value="32" class="permission" {{ (isset($permission_role)?((in_array('32', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_32">Able to view staffs page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_33" value="33" class="permission" {{ (isset($permission_role)?((in_array('33', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_33">Able to add a staff</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_34" value="34" class="permission" {{ (isset($permission_role)?((in_array('34', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_34">Able to edit a staff</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_35" value="35" class="permission" {{ (isset($permission_role)?((in_array('35', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_35">Able to delete a staff</label></li>
					</ul>
				<li><h4>ROLE</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_36" value="36" class="permission" {{ (isset($permission_role)?((in_array('36', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_36">Able to view role page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_37" value="37" class="permission" {{ (isset($permission_role)?((in_array('37', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_37">Able to add a role and its permission</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_38" value="38" class="permission" {{ (isset($permission_role)?((in_array('38', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_38">Able to update a role and its permission</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_39" value="39" class="permission" {{ (isset($permission_role)?((in_array('39', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_39">Able to delete a role and its permission</label></li>
					</ul>
				<li><h4>EMAIL GROUP</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_40" value="40" class="permission" {{ (isset($permission_role)?((in_array('40', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_40">Able to view email group page</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_41" value="41" class="permission" {{ (isset($permission_role)?((in_array('41', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_41">Able to add an email group</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_42" value="42" class="permission" {{ (isset($permission_role)?((in_array('42', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_42">Able to edit an email group</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_43" value="43" class="permission" {{ (isset($permission_role)?((in_array('43', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_43">Able to delete an email group</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_44" value="44" class="permission" {{ (isset($permission_role)?((in_array('44', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_44">Able to send message to email group members</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_45" value="45" class="permission" {{ (isset($permission_role)?((in_array('45', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_45">Able to view email group members</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_46" value="46" class="permission" {{ (isset($permission_role)?((in_array('46', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_46">Able to add an email group member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_47" value="47" class="permission" {{ (isset($permission_role)?((in_array('47', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_47">Able to edit an email group member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_48" value="48" class="permission" {{ (isset($permission_role)?((in_array('48', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_48">Able to delete an email group member</label></li>
						<li><input type="checkbox" name="permission[]" id="permission_49" value="49" class="permission" {{ (isset($permission_role)?((in_array('49', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_49">Able to send message to an email group member</label></li>
					</ul>
				<li><h4>BACKUP</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_50" value="50" class="permission" {{ (isset($permission_role)?((in_array('50', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_50">Able to backup database</label></li>
					</ul>
				<li><h4>REPORTS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_51" value="51" class="permission" {{ (isset($permission_role)?((in_array('51', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_51">Able to generate report</label></li>
					</ul>
				<li><h4>QUICKBOOKS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_100" value="100" class="permission" {{ (isset($permission_role)?((in_array('100', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_100">Able to access the quickbooks</label></li>
					</ul>
				<li><h4>LOGS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_52" value="52" class="permission" {{ (isset($permission_role)?((in_array('52', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_52">Able to view admin logs</label></li>
					</ul>
				<li><h4>SETTINGS</h4></li>
				<hr class="hr-bottom">
					<ul style="list-style: none;">
						<li><input type="checkbox" name="permission[]" id="permission_53" value="53" class="permission" {{ (isset($permission_role)?((in_array('53', $permission_role) )? 'checked="checked"' : ''):'') }}> <label for="permission_53">Able to edit admin settings</label></li>
					</ul>
			</ul>
		</div>
	</fieldset>
	{!! csrf_field() !!}
	<div class="clearfix">
		<div class="pull-right">
			<button type="submit" class="btn btn-darkblue">
				Submit
			</button>
			<a href="{{ url('organization/'.$slug.'/administrator/role/') }}" class="btn btn-red" class="btn btn-darkblue btn-lg float-right">
				Cancel
			</a>
		</div>
	</div>
</form>
