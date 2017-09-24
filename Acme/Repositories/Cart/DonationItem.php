<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/8/2016
 * Time: 3:07 PM
 */

namespace Acme\Repositories\Cart;
use App\Event;

class DonationItem
{

    public $name;
    public $description;
    public $price;
    public $id;
    public $frequency_id;
    public $donation_category_id;
    public $amount;
    public $note;
    public $start_date;
    public $end_date;
    public $type;
    public $donation_type;
    public $event_id;
    public $email;
    public $qty;
    public $user_id;
    public $total;
    public $token;
    public $recurring_type;
    public $no_of_payments;
    public $transaction_key;
    public $no_of_repetition;
    public $recurring_end_date;
    public $fee;
    public $recurring;
    /**
     * CartItem constructor.
     */
    public function __construct($item = [])
    {
        if(count($item) != 0){
            $this->id                               = $item['id'];
            $this->description                      = (isset($item['description'])? $item['description'] : '');
            $this->price                            = (isset($item['price'])? $item['price'] : '');
            $this->frequency_id                     = (isset($item['frequency_id'])? $item['frequency_id'] : '');
            $this->donation_category_id             = (isset($item['donation_category_id'])? $item['donation_category_id'] : '');
            $this->amount                           = (isset($item['amount'])? $item['amount'] : '');
            $this->note                             = (isset($item['note'])? $item['note'] : '');
            $this->start_date                       = (isset($item['start_date'])? $item['start_date'] : '');
            $this->end_date                         = (isset($item['end_date'])? $item['end_date'] : '');
            $this->qty                              = (isset($item['qty'])? $item['qty'] : '');
            $this->type                             = 'donation';
            $this->donation_type                    = (isset($item['donation_type'])? $item['donation_type'] : '');
            $this->event_id                         = (isset($item['event_id'])? $item['event_id'] : '');
            $this->email                            = (isset($item['email'])? $item['email'] : '');
            $this->recurring_type                   = (isset($item['recurring_type'])? $item['recurring_type'] : '');
            $this->no_of_payments                   = (isset($item['no_of_payments'])? $item['no_of_payments'] : '');
            $this->qty                              = (isset($item['qty'])? $item['qty'] : '');
            $this->user_id                          = (isset($item['user_id'])? $item['user_id'] : 0);
            $this->total                            = $this->amount;
            $this->no_of_repetition                 = (isset($item['no_of_repetition'])? $item['no_of_repetition'] : '');
            $this->recurring_end_date               = (isset($item['recurring_end_date'])? $item['recurring_end_date'] : '');
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
    public function setNote($note){
        $this->note = $note;
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
    public function setRecurringType(){
         $this->recurring_type =$recurring_type;
    }
    public function setNoOfPayments(){
         $this->no_of_payments = $no_of_payments;
    }
    /**
     * @return mixed
     */
    public function getFrequencyId(){
        return $this->frequency_id;
    }
    
    public function getQty(){
        return $this->qty;
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

    public function getNote(){
        return $this->note;
    }
     /**
     * @return mixed
     */
    public function getID(){
        return $this->id;
    }

     public function getEventName(){
        $event_id = $this->event_id;
    }
    public function getRecurringType(){
        return $this->recurring_type;
    }
    public function getNoOfPayments(){
        return $this->no_of_payments;
    }

}
