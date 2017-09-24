<?php
use Acme\Common\Constants as Constants;

namespace Acme\Common;

Trait Pagination
{
    public $PageSize = 7;
    public $PageIndex = 1;
    public $SortBy = Constants::DEFAULT_SORT_BY;
    public $SortOrder = Constants::DEFAULT_SORT_ORDER;
    public $Keyword = "";

    function SetPaginationSetting($request)
    {
        if(isset($request[Constants::PAGE_SIZE])&&!empty($request[Constants::PAGE_SIZE]))  
            {$this->PageSize = $request[Constants::PAGE_SIZE];}
        if(isset($request[Constants::PAGE_INDEX])&&!empty($request[Constants::PAGE_INDEX])) 
            { $this->PageIndex = $request[Constants::PAGE_INDEX];}
        if(isset($request[Constants::SORT_BY])&&!empty($request[Constants::SORT_BY])) 
            {$this->SortBy = $request[Constants::SORT_BY];}
        if(isset($request[Constants::SORT_ORDER])&&!empty($request[Constants::SORT_ORDER]))
            {$this->SortBy = $request[Constants::SORT_ORDER];}
        if(isset($request[Constants::KEYWORD])&&!empty($request[Constants::KEYWORD]))
            {$this->Keyword = $request[Constants::KEYWORD];}

    }

    function SetPage($request)
    {
        if(isset($request[Constants::PAGE_SIZE])&&!empty($request[Constants::PAGE_SIZE]))  
            {$this->PageSize = $request[Constants::PAGE_SIZE];}
        if(isset($request[Constants::PAGE_INDEX])&&!empty($request[Constants::PAGE_INDEX])) 
            { $this->PageIndex = $request[Constants::PAGE_INDEX];}
        if(isset($request[Constants::SORT_BY])&&!empty($request[Constants::SORT_BY])) 
            {$this->SortBy = $request[Constants::SORT_BY];}
        if(isset($request[Constants::SORT_ORDER])&&!empty($request[Constants::SORT_ORDER]))
            {$this->SortBy = $request[Constants::SORT_ORDER];}
        if(isset($request[Constants::KEYWORD])&&!empty($request[Constants::KEYWORD]))
            {$this->Keyword = $request[Constants::KEYWORD];}

    }
}

?>