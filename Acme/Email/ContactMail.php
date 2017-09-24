<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 3/3/2016
 * Time: 3:19 PM
 */

namespace Acme\Mail;

use Illuminate\Support\Facades\Mail;

class ContactMail implements ContactMailInterface, EmailInterface
{
    const TEMPLATE = 'emails.contactMail';
    protected $data;
    protected $subject;
    protected $message;
    protected $fromName;
    protected $from;
    protected $to;
    protected $toName;

    public function __construct($from = '', $to = '', $subject = '', $message = '')
    {
        $this->from     = $from;
        $this->to       = $to;
        $this->subject  = $subject;
        $this->message  = $message;
    }

    public function setFrom($from){
        $this->from = $from;
    }

    public function setFromName($fromName){
        $this->fromName = $fromName;
    }

    public function setTo($to){
        $this->to = $to;
    }

    public function setToName($toName){
        $this->toName = $toName;
    }

    public function setSubject($subject){
        $this->subject = $subject;
    }

    public function setMessage($message){
        $this->message = $message;
    }

    public function getFrom(){
        return $this->from;
    }

    public function getFromName(){
        return $this->fromName;
    }

    public function getTo(){
        return $this->to;
    }

    public function getToName(){
        return $this->to;
    }

    public function getSubject(){
        return $this->subject;
    }

    public function getMessage(){
        return $this->message;
    }

    public function execute(){
        $data = $this;

        Mail::send(self::TEMPLATE, ['data' => $data], function ($m) use ($data) {
            $m->from($data->getFrom(), $data->getFromName());

            $m->to($data->getTo(), $data->getToName())->subject($data->getSubject());
        });
    }
}