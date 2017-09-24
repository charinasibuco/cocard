@extends('layouts.app')
@include('cocard-church.church.admin.navigation')
@section('content')
<div class="d-content">
    <div class="margin-mob-top">
        <div class="row">
            <div class="col-md-6">
                <h3> Event Details</h3>
            </div>
            <div class="col-md-6">
                <h3>
                    <div class="clearfix">
                        <a href="{{ url('/organization/'.$slug.'/administrator/events')}}" class="btn btn-darkblue float-right">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp;Back
                        </a>
                    </div>
                </h3>
            </div>
        </div>
        <div class="panel panel-primary panel-information">
            <div class="panel-heading personal-information"><i class="fa fa-user" aria-hidden="true"></i> &nbsp;Details</div>
            <div class="panel-body">
                <div class="row event-details-table">
                    <table>
                        <tr>
                            <td>Organization:</td>
                            <td><strong>{{ $organization->url }}</strong></td>
                        </tr>
                        <tr>
                            <td>Event:</td>
                            <td><strong>{{ $event->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td><strong>{{ $event->description }}</strong></td>
                        </tr>
                        <tr>
                            <td>Capacity:</td>
                            <td><strong>{{ $event->capacity }}</strong></td>
                        </tr>
                        <tr>
                            <td>Pending:</td>
                            <td><strong>{{ $event->pending }}</strong></td>
                        </tr>
                        <tr>
                            <td>Fee:</td>
                            <td><strong>{{ $event->fee }}</strong></td>
                        </tr>
                        <tr>
                            <td>Start date:</td>
                            <td><strong>{{ $event->start_date }}</strong></td>
                        </tr>
                        <tr>
                            <td>End Date:</td>
                            <td><strong>{{ $event->end_date }}</strong></td>
                        </tr>
                        <tr>
                            <td>Reminder Date:</td>
                            <td><strong>{{ $event->reminder_date }}</strong></td>
                        </tr>
                        <tr>
                            <td>Volunteers Needed:</td>
                            <td><strong>{{ $event->volunteer_number }}</strong></td>
                        </tr>
                    </table>

                    {{--<h4 style="margin-left:8px;">Volunteer Groups</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type of Volunteer</th>
                                <th>Volunteers Needed</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->VolunteerGroups() as $group)
                            <tr>
                                <td>{{ $group->type  }}</td>
                                <td>{{ $group->volunteers_needed  }}</td>
                                <td>{{ $group->note  }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>--}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
