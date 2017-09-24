<?php

namespace Acme\Common\Template;

use Acme\Common\Constants as Constants;
use Acme\Common\Entity\EventEntity as EventEntity;
use Acme\Common\Entity\EventListEntity as EventListEntity;
use Acme\Common\CommonFunction as CommonFunction;

class EventListTemplate
{
    use CommonFunction;

    public function ProcessRow($row)
    {
        $entity = new EventListEntity();
        
        $entity->id= $row->id ;
        $entity->user_id= $row->user_id ;
        $entity->event_id= $row->event_id ;
        $entity->name= $row->name ;
        $entity->email= $row->email ;
        $entity->qty= $row->qty ;
        $entity->start_date= $row->start_date ;
        $entity->end_date= $row->end_date ;
        $entity->no_of_repetition= $row->no_of_repetition ;
        $entity->occurence= $row->occurence ;
        $entity->created_at= $row->created_at ;
        $entity->update_at= $row->update_at ;
        $entity->status= $row->status ;
        $entity->organization_id= $row->organization_id ;
        $entity->description= $row->description ;
        $entity->capacity= $row->capacity ;
        $entity->pending= $row->pending ;
        $entity->fee= $row->fee ;
        $entity->parent_event_id= $row->parent_event_id ;
        $entity->modify_recurring_month= $row->modify_recurring_month ;
        $entity->recurring= $row->recurring ;
        $entity->recurring_end_date= $this->DateFormat($row->recurring_end_date);
        $entity->recurring_end_date_carbon = $this->DateFormatCarbon($row->recurring_end_date);

        $entity->reminder_date= $row->reminder_date ;
        $entity->volunteer_number= $row->volunteer_number ;
        $entity->original_no_of_repitition= $row->original_no_of_repitition ;
        $entity->original_recurring_end_date= $row->original_recurring_end_date ;
        $entity->events= $row->events ;
        

        return $entity;
    }

    function Process($list)
    {
        $formattedData = array();

        if(count($list) != 0)
        foreach($list as $key)
        {
           $row =  $this->ProcessRow($key);

           array_push($formattedData , $row);
        }

        return $formattedData;

    }
}

?>