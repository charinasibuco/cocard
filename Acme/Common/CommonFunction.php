<?php

use Acme\Common\Constants as Constants;
use Acme\Common\DataResults as DataResults;

namespace Acme\Common;

use Acme\Common\Template\EventListCalendarTemplate as EventListCalendarTemplate;

use Carbon\Carbon;

Trait CommonFunction
{

    public function AuthenticationError ($request)
    {
        if($request->type == Constants::JSON)
        {
            $result = new DataResult();
            $result->error = true;
            $result->tags = Constants::ERROR_AUTHENTICATION_EXPIRED;
            $result->message = Constants::ERROR_AUTHENTICATION;

            return json_encode($result);
        }
        else
        {
            return view(Constants::ERROR_PAGE);
        }
    }

    public function DateFormat($date)
    {
        return date(Constants::LIST_DATE_FORMAT,strtotime($date));
    }

    public function DateTimeFormat($date)
    {
        return date(Constants::LIST_DATE_TIME_FORMAT,strtotime($date));
    }

    public function DateFormatCarbon($date)
    {
        return  Carbon::parse($date);
    }

    public function AddWeek($date)
    {
        $date = strtotime($date);
        $newDate = strtotime("+7 day",$date);
        return date(Constants::LIST_DATE_TIME_FORMAT  ,$newDate);
    }

    public function AddMonth($date)
    {
        $date = strtotime($date);
        $newDate = strtotime("+1 month",$date);
        return date(Constants::LIST_DATE_TIME_FORMAT,$newDate);
    }

     public function AddYear($date)
    {
        $date = strtotime($date);
        $newDate = strtotime("+12 month",$date);
        return date(Constants::LIST_DATE_TIME_FORMAT ,$newDate);
    }

    public function extractRecurringDates($data , $request)
    {
        $list = array();

        foreach($data as $row)
        {
            $template = new EventListCalendarTemplate($row);
            $template->event_date = $this->DateTimeFormat($template->start_date);
            //array_push($list,$template);      

        
            if(
                (strtotime($request->start_date) <= strtotime($template->event_date))
                && (strtotime($request->end_date) >= strtotime($template->event_date))
            ){
                array_push($list,$template);      
            }
            

            if($row->recurring <> 0)
            {
                    $newevent_date = $template->start_date;
                    $recurring_end_date = $template->base_end_date;
                    $i = false;
                    while($i != true)
                    {
                        $subEvents = new EventListCalendarTemplate($template);

                        switch($row->recurring)
                        {
                            case 1 :
                                $newevent_date = $this->AddWeek($newevent_date);
                            break;
                            case 2 :
                                $newevent_date = $this->AddMonth($newevent_date);
                            break;
                            case 3:
                                $newevent_date= $this->AddYear($newevent_date);
                            break;
                        }

                        $subEvents->event_date = $this->DateTimeFormat($newevent_date);
                    
                        $i = (strtotime($newevent_date) >= strtotime($recurring_end_date));
                        if(!$i)
                        {
                            //array_push($list,$subEvents);  

                            if(
                                (strtotime($request->start_date) <= strtotime($newevent_date))
                                && (strtotime($request->end_date) >= strtotime($newevent_date))
                            ){
                                array_push($list,$subEvents);      
                            }    
                        }
                    }

            }
      
        }

        return $list;
    }
}

?>