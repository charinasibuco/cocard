<?php

namespace Acme\Common\Entity;


class EventListEntity
{
    public $id;
    public $user_id;
    public $event_id;
    public $name;
    public $email;
    public $qty;
    public $start_date;
    public $end_date;
    public $no_of_repetition;
    public $occurence;
    public $created_at;
    public $update_at;
    public $status;
    public $organization_id;
    public $description;
    public $capacity;
    public $pending;
    public $fee;
    public $parent_event_id;
    public $modify_recurring_month;
    public $recurring;
    public $recurring_end_date;
    public $recurring_end_date_carbon;

    public $reminder_date;
    public $volunteer_number;
    public $original_no_of_repitition;
    public $original_recurring_end_date;
    public $events;
    public $android_recurring_end_date;
}

?>