<?php

namespace Acme\Helper;
use Auth;
use App\Http\Requests;
use Acme\Common\DataResult;
use Acme\Helper\ApiDataHandler as ApiDataHandler;

Trait Api
{
    public static function displayData($data,$bladeView = null, $request){
        /*
            Displays the data depending on which middleware the user is comming from
            i.e: Web or api
            
            Essay: if user is from api then blade view is not needed.
        */

        if(!empty($request))
        {
            if($request->type == 'json'){
                $result = new DataResult();

                $data['user']         = Auth::user();
                $handler = new ApiDataHandler($data);
                $result->data = $handler->Data(); 
                $result->settings = $handler->settings;

                return json_encode($result); 
             }
        }
        
        if(Auth::guard('api')->user()){
            $data['user'] =  Auth::guard('api')->user();
            return $data;
        }
        else{
            $data['user']         = Auth::user();
            return view($bladeView,$data);
        }
    }

    public static function getUserToken()
    {
        $user = Auth::user();

        $data = array();
        $data["id"] = $user->id;
        $data["organization_id"] = $user->organization_id;
        $data["api_token"] = $user->api_token;
        $data["roles"] = $user->roles;
    
        return $data;
    }

    public static function getUserByMiddleware(){
        if(Auth::guard('api')->user()){
            return Auth::guard('api')->user();
        }else{
            return Auth::user();
        } 
    }

    public static function getTimzone($dates){
        $tz= (isset($_GET["tz"])) ? $_GET["tz"] : 'UTC';
        $date = new \DateTime($dates, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($tz));
    }

}