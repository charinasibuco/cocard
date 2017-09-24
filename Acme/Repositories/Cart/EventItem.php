<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/8/2016
 * Time: 3:07 PM
 */

namespace Acme\Repositories\Cart;
use Illuminate\Http\Request;
use App\Event;
class EventItem
{

    public $name;
    public $description;
    public $price;
    public $id;
    public $frequency_id;
    public $donation_category_id;
    public $amount;
    public $start_date;
    public $end_date;
    public $type;
    public $donation_type;
    public $event_id;
    public $email;
    public $donationList_title;
    public $qty;
    public $user_id;
    public $total;
    public $token;
    public $transaction_key;
    public $note;
    public $recurring;
    public $recurring_type;
    public $fee;
    public $no_of_repetition;
    public $occurence;
    public $recurring_end_date;
    public $no_of_payments;
    public $participant_name;

    /**
     * CartItem constructor.
     */
    public function __construct($item = [])
    {
        // dd($item);
        if(count($item) != 0){
            $this->id                               = $item['id'];
            $this->name                             = (isset($item['name'])? $item['name'] : '');
            $this->description                      = (isset($item['description'])? $item['description'] : '');
            $this->price                            = (isset($item['price'])? $item['price'] : '');
            $this->qty                              = (isset($item['qty'])? $item['qty'] : '');
            $this->frequency_id                     = (isset($item['frequency_id'])? $item['frequency_id'] : '');
            $this->donation_category_id             = (isset($item['donation_category_id'])? $item['donation_category_id'] : '');
            $this->amount                           = (isset($item['total'])? $item['total'] : '');
            $this->start_date                       = (isset($item['start_date_timezone'])? $item['start_date_timezone'] : '');
            $this->end_date                         = (isset($item['end_date_timezone'])? $item['end_date_timezone'] : '');
            $this->type                             = 'event';
            $this->donation_type                    = (isset($item['donation_type'])? $item['donation_type'] : '');
            $this->event_id                         = (isset($item['event_id'])? $item['event_id'] : '');
            $this->email                            = (isset($item['email'])? $item['email'] : '');
            $this->qty                              = (isset($item['qty'])? $item['qty'] : '');
            $this->user_id                          = (isset($item['user_id'])? $item['user_id'] : '');
            $this->fee                              = (isset($item['fee'])? $item['fee'] : '');
            $this->total                            = $this->amount;
            $this->note                             = (isset($item['note'])? $item['note'] : ' ');
            $this->recurring                        = (isset($item['recurring'])? $item['recurring'] : '');
            $this->no_of_repetition                 = (isset($item['no_of_repetition'])? $item['no_of_repetition'] : '');
            $this->occurence                        = (isset($item['occurence'])? $item['occurence'] : '');
            $this->recurring_end_date               = (isset($item['recurring_end_date'])? $item['recurring_end_date'] : '');
            $this->no_of_payments                   = (isset($item['no_of_payments'])? $item['no_of_payments'] : '');
            $this->recurring_type                   = (isset($item['recurring_type'])? $item['recurring_type'] : '');
            $this->participant_name                  = (isset($item['participant_name'])? $item['participant_name'] : '');
        }
    }
    /**
     * Set frequency method
     * @param $id
     */
    public function setID($id){
        $this->id = $id;
    }  /**
     * Set frequency method
     * @param $frequency
     */
    public function setFrequencyId($frequency_id){
        $this->frequency_id = $frequency_id;
    }

    /**
     * Set donation category method
     * @param $donation_category_id
     */
    public function setDonationCategoryId($donation_category_id){
        $this->donation_category_id = $donation_category_id;
    }
    /**
     * Set donation category method
     * @param $donation_category_id
     */
    public function setName($name){
        $this->name = $name;
    }
    /**
     * Set donation category method
     * @param $donation_category_id
     */
     public function setDescription($description){
        $this->description = $description;
    }
    /**
     * Set amount method
     * @param $amount
     */
    public function setPrice($price){
        $this->price = $price;
    }
    public function setAmount($amount){
        $this->amount = $amount;
    }

    public function setQty($qty){
        $this->qty = $qty;
    }

    /**
     * Set start date method
     * @param $start_date
     */
    public function setStartDate($start_date){
        $this->start_date = $start_date;
    }
     public function setOccurence($occurence){
        $this->occurence = $occurence;
    }
/**
     * Set start date method
     * @param $start_date
     */
    public function setDonationType($donation_type){
        $this->donation_type = $donation_type;
    }
    /**
     * Set end date method
     * @param $end_date
     */
    public function setEndDate($end_date){
        $this->end_date = $end_date;
    }

    public function setEventName($event_id){
        $event_id = $this->event_id;
        $event =  Event::select('name')->where('id', $event_id)->first();
        return $event->name;
    }

    /**
     * @return mixed
     */
    public function getFrequencyId(){
        return $this->frequency_id;
    }

    /**
     * @return mixed
     */
    public function getDonationCategoryId(){
        return $this->donation_category_id;
    }

    /**
     * @return mixed
     */
    public function getPrice(){
        return $this->price;
    }
    public function getQty(){
        return $this->qty;
    }
     public function getOccurence(){
        return $this->occurence;
    }
    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }/**
     * @return mixed
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getStartDate(){
        return $this->start_date;
    }

    /**
     * @return mixed
     */
    public function getEndDate(){
        return $this->end_date;
    }
    /**
     * @return mixed
     */
    public function getType(){
        return $this->type;
    }/**
     * @return mixed
     */
     public function getDonationType(){
        return $this->donation_type;
    }/**
     * @return mixed
     */
    public function getAmount(){
        return $this->amount;
    }
     /**
     * @return mixed
     */
    public function getID(){
        return $this->id;
    }

    public function getEventID(){
        return $this->event_id;
    }

    public function getEventName(){
        $event_id = $this->event_id;
        return Event::select('name')->where('id', $event_id)->first();
    }

    public function getEventFrequency(){
         $event_id = $this->event_id;
         $event_recurring = Event::select('recurring')->where('id', $event_id)->first();
         if($event_recurring->recurring == 1){
            return 'Weekly';
         }elseif($event_recurring->recurring == 2){
            return 'Monthly';
         }elseif ($event_recurring->recurring == 3) {
            return 'Yearly';
         }else{
            return '---';
         }
    }
}
