<?php
/**
 * Created by PhpStorm.
 * User: Jaime Handayan
 * Date: 4/5/2016
 * Time: 3:44 PM
 */

namespace Acme\Repositories\Cart;
use App\Event;
class EasyCart
{
    private $items = [];
    protected $transaction_id;
    protected $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    /**
     * MedBoxCart constructor.
     */
    public function __construct(){
        $this->transaction_id = $this->generateTransctionID();
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param null $type
     * @return array
     */
    public function getItems($type = null){
        if($type != null){
            $list = [];
            foreach($this->items as $item){
                if($item->getType() == $type){
                    $list[] = $item;
                }
            }
            return $list;
        }
        return $this->items;
    }

    /**
     * @param $id
     * @return bool
     */
    public function SearchItem( $id ){
        $list		= array();
        foreach( $this->items as $itemKey => $itemValue ){
            $list[]	= $this->items[$itemKey];
        }
        if(in_array( $id, $list ))
            return true;
        else
            false;
    }

    /**
     * @param $id
     * @return null
     */
    public function findItem($id){
        $result = null;
        foreach($this->items as $item){
            if($item->getID() == $id){
                $result = $item;
                break;
            }
        }

        return $result;
    }

    public function updateItem($id, $item){
        $result         = $this->findItem($id);
        $return         = false;
        if($result != null){
            $result->setName($item['name']);
            $result->setImage($item['image']);
            $result->setDescription($item['description']);
            $result->setPrice($item['price']);
//            $return->setQty($item['qty']);// uncomment if need to update cart item quantity
        }

        return $return;
    }
    public function updateItem2($id, $item){
        #dd($item);
        $result         = $this->findItem($id);
        #dd($result);
        $return         = false;
        if($result != null){
            $result->setFrequencyId((isset($item['frequency_id'])? $item['frequency_id'] : '0'));
            #$result->setDonationCategoryId($item['donation_category_id']);
            $result->setDonationCategoryId((isset($item['donation_category_id'])? $item['donation_category_id'] : '0'));
            #dd($result);
            if($result->type == 'event'){
                //dd($item['prev_qty'], $item['qty']);
                $event = Event::where('id', $result->event_id)->first();
                $event->pending = ($event->pending - $item['prev_qty']) + $item['qty'];
                $event->save();
                $raw    =   ($item['amount'] / $item['prev_qty']);
                #dd($item['amount'] .'---'.$raw. '---'.$item['qty']);
                $result->setAmount($raw * $item['qty']);
            }else{
                #dd($result);
                $result->setAmount($item['amount']);
            }
            $result->setQty((isset($item['qty'])? $item['qty'] : '0'));
            #$result->setEventName((isset($item['event_name'])? $item['event_name'] : '0'));
            $result->setStartDate((isset($item['start_date'])? $item['start_date'] : '00-00-0000'));
            $result->setEndDate((isset($item['end_date'])? $item['end_date'] : '00-00-0000'));
//            $return->setQty($item['qty']);// uncomment if need to update cart item quantity
        }

        return $return;
    }
    /**
     * @param $item
     */
    public function addItem($item, $type){
        //dd($item);
        $q              = false;
        $return         = false;
        $msg            = 'Item is already in the cart';

        //if item already in cart update the quantity
        foreach( $this->items as $itemKey => $itemValue ){
            if( $this->items[$itemKey]->getID() == $item->getID() ){
//                $this->items[$itemKey]->setQTY( $this->items[$itemKey]->getQty() + $item->getQTY() );
                $q		= true;
                break;
            }
        }

        //if item not in cart add the item to cart
        if(!$q) {
            $this->items[] = $item;
            $msg            = 'Add to cart successful!';
            $return         = true;
        }
        return ['result' => $return, 'message' => $msg];
    }

    /**
     * @param $itemID
     */
    public function removeItem( $itemID ){
        $return             = false;
        foreach( $this->items as $itemKey => $itemValue ){
            if( $this->items[$itemKey]->getID() == $itemID ){
                unset($this->items[$itemKey]);
                $return     = true;
                break;
            }
        }

        return $return;
    }

    /**
     * @param $itemID
     */
    public function addQty( $itemID ){
        $return         = false;
        foreach($this->items as $itemKey => $itemValue){
            if( $this->items[$itemKey]->getID() == $itemID ){
                $this->items[$itemKey]->setQTY($this->items[$itemKey]->getQTY() + 1);
                $return = true;
                break;
            }
        }

        return $return;
    }

    /**
     * @param $itemID
     */
    public function minusQty( $itemID ){
        $return         = false;
        foreach( $this->items as $itemKey => $itemValue ){
            if( $this->items[$itemKey]->getID() == $itemID ){
                $this->items[$itemKey]->setQTY( $this->items[$itemKey]->getQTY() - 1 );
                if( $this->items[$itemKey]->getQTY() <= 0 ) {
                    $this->removeItem($itemID);
                    $return = true;
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function ItemQtyEach( $id ){
        foreach( $this->items as $itemKey => $itemValue ){
            if( $this->items[$itemKey]->getID() == $id ){
                return $this->items[$itemKey]->getQTY();
            }
        }
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateTransctionID($length = 8)
    {
        // TODO: Implement generateRandomString() method.
        $charactersLength = strlen($this->characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $this->characters[rand(0, $charactersLength - 1)];
        }

        return time().$randomString;
    }


    public function getItemCount()
    {
        $items = $this->getItems();
        if(is_array($items)){
            return count($items);
        }
        return 0;
    }

}