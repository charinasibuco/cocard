<?php

namespace Acme\Common\Template;


class EventListCalendarTemplate
{
    public $id;
    public $organization_id;
    public $name;
    public $description;
    public $capacity;
    public $pending;
    public $fee;
    public $parent_event_id;
    public $modify_recurring_month;
    public $recurring;
    public $no_of_repetition;
    public $recurring_end_date;
    public $start_date;
    public $end_date;
    public $reminder_date;
    public $volunteer_number;
    public $status;
    public $created_at;
    public $updated_at;
    public $original_no_of_repetition;
    public $original_recurring_end_date;
    public $base_end_date;
    public $event_date;

    public function __construct($data)
    {
        $this->id = $data->id;
        $this->organization_id = $data->organization_id;
        $this->name = $data->name;
        $this->description = $data->description;
        $this->capacity = $data->capacity;
        $this->pending = $data->pending;
        $this->fee = $data->fee;
        $this->parent_event_id = $data->parent_event_id; 
        $this->modify_recurring_month = $data->modify_recurring_month;
        $this->recurring = $data->recurring;
        $this->no_of_repetition = $data->no_of_repetition;
        $this->recurring_end_date = $data->recurring_end_date;
        $this->start_date = $data->start_date;
        $this->end_date = $data->end_date;
        $this->reminder_date = $data->reminder_date;
        $this->volunteer_number = $data->volunteer_number;
        $this->status = $data->status;
        $this->created_at = $data->created_at;
        $this->updated_at = $data->updated_at;
        $this->original_no_of_repetition = $data->original_no_of_repetition;
        $this->original_recurring_end_date = $data->original_recurring_end_date;
        $this->base_end_date = $data->base_end_date;

    }
}

?>