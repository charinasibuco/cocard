<?php
namespace Acme\Common;

class Constants
{
    #GENERAL
    const LIMIT                 = 20;
    const ALL = 'All';
    const SYMBOL_ALL = '*';
    const JSON = "json";


    #GENERAL ERROR
    const ERROR_AUTHENTICATION = "Authentication Expired.";
    const LOST_CONNECTION = "Connection Failed.";

    #MODULES
    const MODULE = 'module';
    const DONATION_LIST = "donation_list";
    const DONATION_CATEGORY = "donation_category";
    const EVENT_LIST = "event_list";
    const VOLUNTEER_LIST = "volunteer_list";
    const VOLUNTEER_GROUP_LIST = "volunteer_group_list";
    const USER_PROFILE = "user_profile";
    const FAMILY_LIST = "family_list";
    const FAMILY_MEMBER_LIST = "family_member_list";
    const INITIALIZE_FAMILY_MEMBER = "initialize_family_member";
    const INITIALIZE_FAMILY_GROUP = "initialize_family_group";
    const EVENT_CALENDAR = "event_calendar";
    const VOLUNTEER_LIST_GUEST = "volunteer_list_guest";
    const VOLUNTEER_LIST_MEMBER = "volunteer_list_member";

    #Pagination
    const PAGE_INDEX = "PageIndex";
    const PAGE_SIZE = "PageSize";
    const KEYWORD = "search";
    const SORT_ORDER = "SortOrder";
    const SORT_BY = "SortBY";


    #FORMAT
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';
    const LIST_DATE_FORMAT = "m/d/Y";
    const LIST_DATE_TIME_FORMAT = "m/d/Y H:i:s";
    const ANDROID_DATETIME_FORMAT = 'yyyy-MM-dd hh:mm:ss';
    const ANDROID_DATE_FORMAT = 'yyyy-MM-dd';

    #DEFAULT VALUES
    const DEFAULT_SORT_ORDER = "DESC" ;
    const DEFAULT_SORT_BY = "id";


    #LOGIN
    const LOGIN_SUCCESS = "Successfully Logged-In";
    const LOGIN_FAILED = "Invalid Username and Password";
    const SUCCESSFULLY_REGISTERED = 'Successfully registered. You are now logged in';


    #DONATION
    const DONATION_CANCELLED = "Successfully cancelled the donation";


    #FAMILY GROUP
    const SUCCESSFULLY_ADDED_FAMILY_GROUP = "Successfully Added Family Group!";
    const SUCESSSFULLY_DELETED_FAMILY_GROUP = "Successfully Deleted Family Group!";
    const SUCCESSFULLY_UPDATED_FAMILY_GROUP = "Successfully Edited Family Group!";

    #FAMILY MEMBER 
    const SUCCESSFULLY_ADD_FAMILY_MEMBER = 'Successfully Added Family Member/s';
    const SUCCESSFULLY_ADD_MEMBER = 'Successfully Added to Members Directory';
    const SUCCESSFULLY_UPDATE_MEMBER = 'Successfully Edited Family Member';
    const SUCCESSFULLY_DELETED_MEMBER  = "Successfully Deleted Family Member";

    #USER

    const SUCCESSFULLY_UPDATED_USER = "Successfully Edited Profile!";
    

    #VIEWS PAGES
    const ERROR_PAGE = "errors.errorpage";



    #ERROR_CODE
    const ERROR_CODE = "error_code";
    const ERROR_AUTHENTICATION_EXPIRED = 401;
    const ERROR_CODE_FAMILY_MEMBER_VALIDATION = 101;
    const ERROR_CODE_FAMILY_MEMBER_EXIST =  102;
    const ERROR_CODE_FAMILY_MEMBER_USER_VALIDATION = 103;

}

?>