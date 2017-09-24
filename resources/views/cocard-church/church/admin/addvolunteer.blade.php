@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3>Add Volunteer</h3>
            </div>
        </div>
        <div class="well">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="first-name">First Name:</label>
                        <input required class="form-control required" name="first_name" placeholder="First Name" class="required prefill" value="{{ $first_name or "" }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="middle-name">Middle Name:</label>
                        <input required class="form-control required" name="middle_name" placeholder="Middle Name" class="required prefill" value="{{ $middle_name or "" }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="last-name">Last Name:</label>
                        <input required class="form-control required" name="last_name" placeholder="Last Name" class="required prefill" value="{{ $last_name or "" }}">
                    </div>
                </div>

                <div class="col-sm-6">
                    <label>Start date </label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-sm-6">
                    <label>End date </label>
                    <input type="date" class="form-control">
                </div>
                <div class="col-sm-12">
                    <label style="margin-top:15px;">Event </label>
                    <select class="form-control" >
                        <option selected disabled>--Select an event--</option>
                        <option id="edit">Sample 1</option>
                        <option id="delete">Sample 2</option>
                        <option id="delete">Sample 3</option>
                        <option id="delete">Sample 4</option>
                    </select>
                </div>
                <div class="col-sm-12">
                    <label style="margin-top:15px;">Role </label>
                    <select class="form-control" >
                        <option selected disabled>--Select a role--</option>
                        <option id="edit">Sample 1</option>
                        <option id="delete">Sample 2</option>
                        <option id="delete">Sample 3</option>
                        <option id="delete">Sample 4</option>
                    </select>
                </div>
            </div>
            &nbsp;
            <div class="row">
                <div class="admin-buttons">
                    <div class="col-sm-12">
                        <input type="submit" class="btn btn-darkblue" value="Submit">
                        <a href="" class="btn btn-primary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
