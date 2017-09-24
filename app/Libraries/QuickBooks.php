<?php

//namespace App\Libraries;
define('OAUTH_CONSUMER_KEY',env('OAUTH_CONSUMER_KEY'));
define('OAUTH_CONSUMER_SECRET', env('OAUTH_CONSUMER_SECRET'));
define('QB_ACCESS_TOKEN',env('QB_ACCESS_TOKEN'));
define('QB_ACCESS_TOKEN_SECRET',env('QB_ACCESS_TOKEN_SECRET'));
define('PATH_SDK_ROOT', 'v3-php-sdk-2.6.0/');
define('POPO_CLASS_PATH', PATH_SDK_ROOT . 'Data' . DIRECTORY_SEPARATOR);
require_once('v3-php-sdk-2.6.0/config.php');
require_once(PATH_SDK_ROOT . 'Core/ServiceContext.php');
require_once(PATH_SDK_ROOT . 'DataService/DataService.php');
require_once(PATH_SDK_ROOT . 'PlatformService/PlatformService.php');
require_once(PATH_SDK_ROOT . 'Utility/Configuration/ConfigurationManager.php');
require_once(PATH_SDK_ROOT . 'Core/OperationControlList.php');
require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Php2Xml.php');
require_once(PATH_SDK_ROOT . 'XSD2PHP/src/com/mikebevz/xsd2php/Bind.php');

class QuickBooks extends Library{
    public function _construct(){

        $this->serviceType = IntuitServicesType::QBO;


        //$this->invoice = new Invoice();


       // $this->requestValidator = new OAuthRequestValidator(QB_ACCESS_TOKEN,QB_ACCESS_TOKEN_SECRET,OAUTH_CONSUMER_KEY,OAUTH_CONSUMER_SECRET);
        $this->requestValidator = new OAuthRequestValidator(ConfigurationManager::AppSettings('AccessToken'),
            ConfigurationManager::AppSettings('AccessTokenSecret'),
            ConfigurationManager::AppSettings('ConsumerKey'),
            ConfigurationManager::AppSettings('ConsumerSecret'));
        $this->serviceContext = new ServiceContext($this->realmId, $this->serviceType, $this->requestValidator);
        $this->dataService = new DataService($this->serviceContext);

    }


    public function createCustomer(){
        $customerObj = new IPPCustomer();
        $customerObj->Name = "Name" . rand();
        $customerObj->CompanyName = "CompanyName" . rand();
        $customerObj->GivenName = "GivenName" . rand();
        $customerObj->DisplayName = "DisplayName" . rand();
        $resultingCustomerObj = $this->dataService->Add($customerObj);
        return "Created Customer Id={$resultingCustomerObj->Id}. Reconstructed response body:\n\n";
    }

}