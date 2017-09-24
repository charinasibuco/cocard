<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Mail;
use Carbon\Carbon;
use App\QueuedMessageModel;
use Acme\Repositories\QueuedMessageRepository;

class QueuedMessageController extends Controller
{
   	public function checkDateToSendReminder($queued_message){
        $now = Carbon::now()->format('d-m-Y');
        $messages = $queued_message->getQueuedMessage();
        foreach ($messages as $message) {
            $start_date = $this->parse($message->start_date);
            $end_date = $this->parse($message->end_date);
            $recurring = $message->event->recurring;
            
            $recurring_end_date = $this->parse($message->event->recurring_end_date)->format('d-m-Y');
            $event_name = $message->event->name;
            if($message->volunteer_group_id == 0){//if Reminder message is for Event Participants
                $type = 'Event';
                $reminder_date = $this->parse($message->event->reminder_date);
                $body_message =  $this->getMessage($event_name,$type);
                $custom_message = '';
                $name = $message->participant_name;
            }elseif ($message->volunteer_group_id > 0) { //if Reminder message is for Volunteer Group Volunteers
               $volunteer_group_name = $message->VolunteerGroup->type;
               $type = 'Volunteer Group';
               $reminder_date = $this->parse($message->reminder_date);
               $custom_message = $message->message;
               $body_message =  $this->getMessage($volunteer_group_name,$type);
               $name = $message->VolunteerGroup->type.' Group';
            }
            // dd($message->Participant);
            $get_email     = $message->email;
            $emailStrip     = (explode(',', $get_email));
            $email_count     = count($emailStrip);

            if($email_count > 1){
                $email =$emailStrip;
            }else if($email_count == 1){
                $email = $message->email;
            }
           
            //if Reminder Message is for Recurring Event
            if($recurring >0){
                $total_no_of_occurrence = $message->total_no_of_occurrence;
                for ($i=0; $i < $total_no_of_occurrence ; $i++) { 
                    $recurring_reminder_date = $this->datePerOccurrence($i,$reminder_date,$recurring)->format('d-m-Y');
                    $start_date_recurring = $this->datePerOccurrence($i,$start_date,$recurring)->format('n/j/Y h:i A');
                    $end_date_recurring = $this->datePerOccurrence($i,$end_date,$recurring)->format('n/j/Y h:i A');
                    if($recurring_reminder_date  == $now){
                        Mail::send('cocard-church.email.message',['email' => $message->email,'name'=>$name,'event_name' => $event_name, 'reminder' => $reminder_date, 'start_date' => $start_date_recurring, 'end_date' => $end_date_recurring,'custom_message' =>$custom_message,'request' => ['subject' => $message->subject , 'message' => $body_message]], function ($m) use ($message,$recurring_reminder_date,$email) {
                            $m->to($email)->subject($message->subject);
                        });
                    }
                    //if Recurring End Date is equal to Date now,
                    //status will update to save.
                    if($type == 'Event'){
                        if($recurring_end_date == $now){
                            $message->status = 'sent';
                            $message->save();
                        }
                    }elseif($type == 'Volunteer Group'){
                        $message->status = 'sent';
                        $message->save();
                    }
                        
                }

            }else{
                 $start_date = $start_date->format('n/j/Y h:i A');
                 $end_date = $end_date->format('n/j/Y h:i A');
                //if Reminder Message is for Single Event or a Volunteer Group
                if($reminder_date->format('d-m-Y')  == $now){
                    Mail::send('cocard-church.email.message',['email' => $message->email,'event_name' => $event_name,'group_name '=>$volunteer_group_name,'reminder' => $reminder_date, 'start_date' => $start_date, 'end_date' => $end_date, 'custom_message' =>$custom_message, 'request' => ['subject' => $message->subject , 'message' => $body_message]], function ($m) use ($message,$email) {
                        $m->to($email)->subject($message->subject);
                    });
                    //after emailing, status will update to sent.
                    $message->status = 'sent';
                    $message->save();
                }
            }
        }
    }

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

    public function parse($date){
        $parse_date = Carbon::parse($date);
        return $parse_date;
    }

    public function getMessage($name,$type){
        return 'We are sending you this message to remind you about the "'.$name.' '.$type.'" with the following details:';
    }
}

