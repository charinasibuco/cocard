<?php
namespace Acme\Helper;

use Acme\Common\Constants as Constants;
use Acme\Common\Template\EventListTemplate as EventListTemplate;

class ApiDataHandler
{
    private $returnData;
    private $module;
    private $content= array();
    private $data;
    public $settings;

    function __construct($data)
    {
        if(empty($data[Constants::MODULE]))
        {
            $data[Constants::MODULE] = '';
        }

        $this->module = $data[Constants::MODULE];
        $this->data = $data;

        $this->SetData();
        $this->FilterData();
    }

    function Data()
    {
        return $this->returnData;
    }

    function FilterData()
    {
        if(count($this->content)!=0)
        {
            foreach($this->content as $c)
            {
                $this->returnData[$c] = $this->Reformat($this->data[$c], $c);
            }
        }
        else
        {
            $this->returnData = $this->data;
        }
    }

    function SetData()
    {
        $set = array();

        switch($this->module)
        {
            case Constants::DONATION_LIST : 
                $this->content = ['donations'];
            break;
            case Constants::DONATION_CATEGORY :
                $this->content = ['donation_list'];
            break;
            case Constants::EVENT_LIST :
                $this->content = ['participants'];
            break;
            case Constants::VOLUNTEER_LIST :
                $this->content = ['volunteers'];
            break;
            case Constants::VOLUNTEER_GROUP_LIST :
                $this->content = ['events'];
            break;
            case Constants::USER_PROFILE :
                $this->content = ['profile'];
            break;
            case Constants::FAMILY_LIST :
                $this->content = ['family_groups'];
            break;
            case Constants::FAMILY_MEMBER_LIST :
                $this->content = ['family_members','family_groups'];
            break;
            case Constants::INITIALIZE_FAMILY_MEMBER :
                $this->content = ['family_member'];
            break;
            case Constants::INITIALIZE_FAMILY_MEMBER :
                $this->content = ['family_member'];
            break;
            case Constants::EVENT_CALENDAR :
                $this->content = ['eventsList'];
            break;
            case Constants::INITIALIZE_FAMILY_GROUP :
                $this->content = ['family_group'];
            break;
            case Constants::VOLUNTEER_LIST_GUEST :
                $this->content = ['events'];
            break;
            case Constants::VOLUNTEER_LIST_MEMBER :
                $this->content = ["volunteer_groups"];
            break;
            default :
                $this->content = [];
        }
    }

    function Reformat($data, $object)
    {

        switch($object)
        {
            case 'participants' : 
            case 'volunteer_group_by_field':
               $set["DateTimeFormat"] = Constants::ANDROID_DATETIME_FORMAT;
               $set["DateFormat"] = Constants::ANDROID_DATE_FORMAT;
               $this->settings = $set;
            break;
        }
        

        return $data;
    }
}