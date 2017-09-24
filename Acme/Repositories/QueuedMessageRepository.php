<?php

namespace Acme\Repositories;

use Acme\Repositories\Repository;
use App\VolunteerGroup;
use App\Volunteer;
use App\QueuedMessageModel;
use App\Event;
use Carbon\Carbon;
class QueuedMessageRepository extends Repository
{
/**/
    const LIMIT = 50;

    protected $listener;

    public function model()
    {
        // TODO: Implement model() method.
        return 'App\QueuedMessageModel';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function create(){

    }

    public function edit($id)
    {
        // TODO: Implement edit() method.
    }

    public function destroy($id){

    }

    public function sentReminderMessage($request, $id){
        //dd($request->all());

        $volunteer_group_id = isset($request->volunteer_group_id)?$request->volunteer_group_id:'';
        $type = isset($request->type)?$request->type:'';
        $event = Event::where('id',$request->event_id)->first();
        $start_date = Carbon::parse($request->start_date); 
        $end_date = Carbon::parse($request->end_date); 
        $reminder_date =isset($request->reminder_date) ? Carbon::parse($request->reminder_date) :''; 

        $queued_message = new $this->model;
        $queued_message->event_id = $request->event_id;
        $queued_message->volunteer_group_id = isset($request->volunteer_group_id)?$request->volunteer_group_id : 0;
        $queued_message->subject = isset($request->reminder_subject)?$request->reminder_subject: $this->event_subject($event);
        $queued_message->message = isset($request->reminder_message)?$request->reminder_message : $this->event_message($event,$request);
        $queued_message->email   = isset($request->participant_email)?$request->participant_email:$this->getEmailApplicants($volunteer_group_id);
        $queued_message->event_date = isset($request->event_date) ? $request->event_date: $start_date;
        $queued_message->reminder_date = isset($request->reminder_date)?$reminder_date:$event->reminder_date;
        $queued_message->start_date = isset($request->start_date)?$start_date:$event->start_date;
        $queued_message->end_date = isset($request->end_date)?$end_date:$event->end_date;
        $queued_message->participant_name = isset($request->participant_name)?$request->participant_name:'';
        $queued_message->save();
    }

    // public function checkDateToSendReminder(){
    //     return $this->model->where('status', 'queued')->get();
    // }
    public function getDate($date){
        return $date;
    }
    public function getQueuedMessage(){
       return $this->model->where('status', 'queued')->get();
    }

    public function findQueuedMessage($attribute,$value){
        $this->model->where($attribute, $value)->first();
    }

    public function event_message($event,$request){
        return 'Event '.$event->name.' will start on '.$request->start_date.' and will end on '.$request->end_date;
    }
    public function event_subject($event){
        return 'Reminder for '.$event->name;
    }

    public function getEmailApplicants($volunteer_group_id){
        $volunteer =Volunteer::where('volunteer_group_id',$volunteer_group_id)->get();
        $volunteers =[];
        foreach ($volunteer as $key => $value) {
            $volunteers[$key]= $value->email;
        }
        $email = implode (",", $volunteers);
        return $email;
    }
    // public function event_message($event,$request,$count,$recurring){
    //     $start_date = $this->datePerOccurrence->($count,$request->start_date,$recurring);
    //     $end_date = $this->datePerOccurrence->($count,$request->end_date,$recurring);
    //     return 'Event '.$event->name.' will start on '.$request->start_date.' and will end on '.$request->end_date;
    // }
    public function datePerOccurrence($count,$date,$recurring){
        //Recurring Legend
        //1 == Weekly
        //2 == Monthly
        //3 == Yearly
        if($recurring == 1){
           $date_per_occurrence = $date->addWeek($count);
           return $date_per_occurrence;
        }elseif ($recurring == 2) {
           $date_per_occurrence = $date->addMonth($count);
           return $date_per_occurrence;
        }elseif($recurring == 3){
            $date_per_occurrence =  $date->addYear($count);
           return $date_per_occurrence;
        }
    }
}