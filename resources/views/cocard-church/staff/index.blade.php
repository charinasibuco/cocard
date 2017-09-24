<div class="col-md-12">
    <table class="table">
        <thead>
            <tr>
                <th>Action</th>
                <th>Status</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $staff)
                <tr>
                    <td>
                        <a title="Edit" href="{{ url('/organization/'.$slug.'/administrator/staff/edit/'.$staff->id) }}" class="btn btn-success">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </a>
                        <a title="Delete" class="delete-link btn btn-danger" href="{{ url('/organization/'.$slug.'/administrator/staff/destroy/'.$staff->id) }}">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td>
                        <a href="javascript:void(0);">
                            <label class="switch">
                                <input type="checkbox" data-staff_id="{{ $staff->id }}" class="staff-status" @if($staff->status == "Active") checked  @endif id="status-{{ $staff->id }}">
                                <div class="slider round"></div>
                            </label>
                        </a>
                        <span class="status-container">{{ $staff->status }}</span>
                    </td>
                    <td>{{ $staff->full_name }}</td>
                    <td>{{ $staff->role->first()->title or ""  }}</td>
                    <td>{{ $staff->email or ""  }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>