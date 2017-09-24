<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/8/2016
 * Time: 3:07 PM
 */

namespace Acme\Repositories\Cart;


class CartItem
{
    private $id;
    private $image;
    private $name;
    private $description;
    private $price;
    private $qty;

    /**
     * CartItem constructor.
     */
    public function __construct($item = [])
    {
        if(count($item) != 0){
            $this->id               = $item['id'];
            $this->image            = $item['image'];
            $this->name             = $item['name'];
            $this->description      = $item['description'];
            $this->price            = $item['price'];
            $this->qty              = $item['qty'];
        }
    }

    /**
     * Set id method
     * @param $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * Set Image method
     * @param $image
     */
    public function setImage($image){
        $this->image = $image;
    }

    /**
     * Set name method
     * @param $name
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * Set description method
     * @param $description
     */
    public function setDescription($description){
        $this->description = $description;
    }

    /**
     * Set price method
     * @param $price
     */
    public function setPrice($price){
        $this->price = $price;
    }

    /**
     * Set quantity method
     * @param $qty
     */
    public function setQty($qty){
        $this->qty = $qty;
    } 


    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getImage(){
        return $this->image;
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getDescription(){
        return $this->description;
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
    public function getQty(){
        return $this->qty;
    }
}