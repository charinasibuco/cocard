<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 6/14/2016
 * Time: 11:36 AM
 */

namespace Acme\Repositories;


class QuickBooksRepository extends Repository
{
/**/
    protected $listener;

    public function model()
    {
        // TODO: Implement model() method.
        return 'App\QuickBooks';
    }

    public function findCredentials($organization_id){
        return $this->model->where("organization_id",'=',$organization_id)->first();
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

    public function save(Array $input)
    {
        $oauth = $this->model->where('organization_id','=',$input["organization_id"])->first();
        if($oauth){
            $this->model->find($oauth->id)->update($input);
            return $this->model->find($oauth->id);
        }
       return $this->model->create($input);
    }

    public function update(Array $input, $id)
    {
        $this->model()->where("id",$id)->update($input);
    }
    public function getFillable(){
        return $this->model->getFillable();
    }
    public function destroy($id){

    }

    public function post($url, $input){
        foreach($input as $key => $value){
            $input[$key] = urlencode($value);
        }

        $fields_string = "";
        foreach($input as $key=>$value) {
            $fields_string .= $key.'='.$value.'&';
        }
        rtrim($fields_string,'&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($input));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        
        //curl_close($ch);
        return $result;
    }
}