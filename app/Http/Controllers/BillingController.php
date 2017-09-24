<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\BillingInfoRequest;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\TransactionRepository  as Transaction;
use Acme\Repositories\ParticipantRepository as Participant;
use Acme\Repositories\DonationRepository as Donation;
use Acme\Repositories\Cart\EasyCart as EasyCart;
use Acme\Repositories\QuickBooksRepository as QB;
use Acme\Repositories\QueuedMessageRepository;
use Auth;
use Mail;
use PDF;
use App;
use Dompdf\Dompdf;
use App\Transaction as Transactions;
use App\Donation as Donations;
use Carbon\Carbon;
use App\TransactionDetails as TransactionDetails;

class BillingController extends Controller
{
    public function __construct(Donation $donation,QB $qb,Organization $organization, Request $request, Participant $participant, Transaction $transaction,QueuedMessageRepository $queued_message)
    {
        $this->cart = session('cart');
        $this->organization = $organization->getUrl($request->segment(2));
        $this->participant = $participant;
        $this->transaction = $transaction;
        $this->request = $request;
        $this->qb = $qb;
        $this->donation = $donation;
        $this->auth = Auth::user();
        $this->queued_message = $queued_message;
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Billing view form
    public function index()
    {
        $data['user'] = Auth::user();
        $data['organization'] = $this->organization;
        $data['organization_id'] = $data['organization']->id;
        $data['slug'] = $data['organization']->url;

        $data['step'] = 0;
        if($this->request->server('HTTP_REFERER') && $this->cart->getItemCount()) $data['step'] = 1;
        if($this->request->get('token-id')) $data['step'] = 3;

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        //dd($this->transaction);
        switch ($data['step']) {
            case 1:
                return view('cocard-church.billing.index',$data);

            case 3:
                $xml = $this->process_payment();
                $res = @new \SimpleXMLElement((string)$xml);
                $ret = ['text'=>(string) $res->{'result-text'},'code'=>(string) $res->result];
                //dd($res, $ret);
                if($res->result == 1){
                    /*********QuickBooks Processing*************************************/
                    if($this->qb->findCredentials($data['organization']->id)){
                        $random_number = rand(10,10000);
                        $details = (array) $res->billing;
                        $organization_id = $data['organization']->id;
                        $name = $details['first-name'];
                        $given_name = $details['last-name'];
                        $company_name = $data['organization']->name;
                        $display_name = $name." ".$given_name;
                        $user_id = null;

                        if(Auth::check()){
                            $name = Auth::user()->first_name;
                            $given_name = Auth::user()->last_name;
                            $diplay_name = $name." ".$given_name;
                            $user_id = Auth::user()->id;
                        }
                         $customerObj = [
                            "organization_id" => $organization_id,
                            "name" => $name,
                            "given_name" =>  $given_name,
                            "company_name" =>  $company_name,
                            "display_name" =>  $display_name,
                            "user_id" => $user_id
                        ];

                        $customer_id = $this->qb->post(route('create_customer'),$customerObj);
                        $cart = $this->cart->getItems();
                        foreach($cart as $item){
                            $invoiceObj = [
                                "organization_id" => $data['organization']->id,
                                "id" => $item->id,
                                "doc_number" => rand(1,1000000),
                                "description" => $item->description,
                                "amount" =>$item->amount,
                                "customer_id" => $customer_id
                            ];
                            $this->qb->post(route('create_invoice'),$invoiceObj);
                        }
                    }

                    /****************************************************************/
                    $ret = [
                        'text'=>$res->{'result-text'},
                        'code'=>$res->result,
                        'trans_id'=>$res->{'transaction-id'},
                        'amount'=>$res->amount,
                        'cart'  => $this->cart->getItems()
                    ];
                    //dd($ret);
                    //retrieve items in cart and insert to participants
                    $amount = 0;
                    $cart = $this->cart->getItems();
                    //dd($cart);
                    foreach($cart as $key2 =>$item){
                        if($item !=null){
                            $item->token = \Request::get('token-id');
                            $item->transaction_key =  $res->{'transaction-id'};
                            $item->organization_id = $data['organization_id'];
                            foreach ($cart as $key) {
                               $amount+= $key->amount;
                            }
                            //dd($res->{'first-name'}.' '.$res->{'last-name'});
                            $item->total_amount    = $amount;
                            $item->participant_name = $res->billing->{'first-name'}.' '.$res->billing->{'last-name'};
                            $item->start_date = Carbon::parse($item->start_date)->format('Y-m-d H:i:s');
                            $item->end_date = Carbon::parse($item->end_date)->format('Y-m-d H:i:s');
                            $item->occurence    = $item->occurence + (int)($key2);
                            $this->participant->save($item);
                            $this->transaction->save($item);
                            $item->participant_email = $res->billing->email;
                            if($item->event_id != ''){
                                 $this->queued_message->sentReminderMessage($item, $id=null);
                             }
                            //dd($cart);
                            foreach($cart as $item2){
                                $item2->token = \Request::get('token-id');
                                $item2->transaction_key =  $res->{'transaction-id'};
                                $item2->organization_id = $data['organization_id'];
                                $this->donation->cartItemTransaction($item2);
                            }
                        }
                }
                 // email donation receipt
                    // Mail::send('cocard-church.email.donation', ['res' => $res, 'data' => $data, 'ret' => $ret], function ($m) use ($res) {
                    //     $m->to(trim($res->billing->email), trim($res->billing->{'first-name'}))->subject('Transaction Details');
                    // });
                #dd($ret, $res);
               // dd($this->cart->getItems());
                //$data['cart'] = $this->cart->getItems();
                $this->remove_cart_item();
            }
            // dd($this->cart->getItems());
                $data['email'] = $res->billing->email;
                $data['res'] = $res;
                $data['ret'] = $ret;
                if($ret['code'] == 1){
                    $data['cart'] = $cart;
                }
                if($this->request->type == "json"){
                    if($ret['code'] == 1){
                        return json_encode($ret);
                    }else{
                        return json_encode($ret);
                    }
                }
                return view('cocard-church.billing.results',$data);

            default:
                return redirect('/organization/'.$data['slug'].'/donations');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillingInfoRequest $request)
    {
        // Step 2: Process customer info
        $data['step'] = 2;
        $data['user'] = Auth::user();
        $data['organization'] = $this->organization;
        $data['organization_id'] = $data['organization']->id;
        $data['slug'] = $data['organization']->url;

        $data['total'] = 0.00;
        $data['cart'] = $this->cart->getItems();
        foreach ($data['cart'] as $key) {
            $data['total']  = ($data['total']  + $key->getAmount());
        }



        $form_url = $this->process_userinfo($request,$data['total']);
        $data['form_url'] = $form_url;

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        return view('cocard-church.billing.index',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Helper function demonstrating how to send the xml with curl
     *
     * @param  object  $xmlRequest
     * @param  string  $gatewayURL
     * @return string
     */
    public function sendXMLviaCurl($xmlRequest,$gatewayURL)
    {
        $ch = curl_init(); // Initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $gatewayURL); // Set POST URL

        $headers = array();
        $headers[] = "Content-type: text/xml";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add http headers to let it know we're sending XML
        $xmlString = $xmlRequest->saveXML();
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return into a variable
        curl_setopt($ch, CURLOPT_PORT, 443); // Set the port number
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Times out after 30s
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlString); // Add XML directly in POST

        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // requires ssl cert
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!($data = curl_exec($ch))) {
            print  "curl error =>" .curl_error($ch) ."\n";
            throw New \Exception(" CURL ERROR :" . curl_error($ch));

        }
        curl_close($ch);

        return $data;
    }

    /**
     * Helper function to make building xml dom easier
     *
     * @param object $domDocument
     * @param object $parentNode
     * @param string $name
     * @param string $value
     */
    public function appendXmlNode($domDocument, $parentNode, $name, $value)
    {
        $childNode      = $domDocument->createElement($name);
        $childNodeValue = $domDocument->createTextNode($value);
        $childNode->appendChild($childNodeValue);
        $parentNode->appendChild($childNode);
    }

    public function process_userinfo($request,$total)
    {
        $xmlRequest = new \DOMDocument('1.0','UTF-8');

        $xmlRequest->formatOutput = true;
        $xmlSale = $xmlRequest->createElement('sale');

        // Amount, authentication, and Redirect-URL are typically the bare minimum.
        $this->appendXmlNode($xmlRequest, $xmlSale,'api-key',config('nmi.api_key'));
        $this->appendXmlNode($xmlRequest, $xmlSale,'redirect-url', $request->server('HTTP_REFERER'));
        $this->appendXmlNode($xmlRequest, $xmlSale, 'amount', $total);
        $this->appendXmlNode($xmlRequest, $xmlSale, 'ip-address', $request->server('REMOTE_ADDR'));
        $this->appendXmlNode($xmlRequest, $xmlSale, 'currency', 'USD');

        if($request->get('customer-vault-id')) {
            $this->appendXmlNode($xmlRequest, $xmlSale, 'customer-vault-id', $request->get('customer-vault-id'));
        }
        // else {
        //     $xmlAdd = $xmlRequest->createElement('add-customer');
        //     $this->appendXmlNode($xmlRequest, $xmlAdd, 'customer-vault-id' ,411);
        //     $xmlSale->appendChild($xmlAdd);
        // }

        // Set the Billing and Shipping from what was collected on initial shopping cart form
        $xmlBillingAddress = $xmlRequest->createElement('billing');
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'first-name', $request->get('billing-address-first-name'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'last-name', $request->get('billing-address-last-name'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'address1', $request->get('billing-address-address1'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'city', $request->get('billing-address-city'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'state', $request->get('billing-address-state'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'postal', $request->get('billing-address-zip'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'country', $request->get('billing-address-country'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'email', $request->get('billing-address-email'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'phone', $request->get('billing-address-phone'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'company', $request->get('billing-address-company'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'address2', $request->get('billing-address-address2'));
        $this->appendXmlNode($xmlRequest, $xmlBillingAddress,'fax', $request->get('billing-address-fax'));
        $xmlSale->appendChild($xmlBillingAddress);

        // products/items to process
        $cart = $this->cart->getItems();

        foreach ($cart as $item) {
            $xmlProduct = $xmlRequest->createElement('product');
            $this->appendXmlNode($xmlRequest, $xmlProduct,'product-code' , $item->donation_type.'-'.$item->id);
            $this->appendXmlNode($xmlRequest, $xmlProduct,'description' , $item->donationList_title);
            $this->appendXmlNode($xmlRequest, $xmlProduct,'total-amount' , $item->amount);
            $xmlSale->appendChild($xmlProduct);
        }

        // save product to xml
        $xmlRequest->appendChild($xmlSale);

        // The Payment Gateway will return a variable form-url
        $xml = $this->sendXMLviaCurl($xmlRequest,config('nmi.api_url'));

        $form_url = '';
        $gwResponse = @new \SimpleXMLElement($xml);
        if ( (string)$gwResponse->result == 1 ) {
            $form_url = $gwResponse->{'form-url'};
        }
        else {
            throw New \Exception(print " Error, received " . $xml);
        }

        return $form_url;
    }

    public function process_payment()
    {
        $tokenId = \Request::get('token-id');
        $xmlRequest = new \DOMDocument('1.0','UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
        $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction,'api-key', config('nmi.api_key'));
        $this->appendXmlNode($xmlRequest, $xmlCompleteTransaction,'token-id', $tokenId);
        $xmlRequest->appendChild($xmlCompleteTransaction);

        // process payment transaction
        $res = $this->sendXMLviaCurl($xmlRequest,config('nmi.api_url'));

        return $res;
    }

    public function theme($banner,$scheme)
    {
        $data['banner'] = $banner;
        if(!$data['banner']) $data['banner'] ='background.jpg';

        $data['scheme1'] = '#04191c';
        $data['scheme2'] = '#ffffff';
        $data['scheme3'] = '#222222';
        $data['scheme4'] = '#012732';
        $data['scheme5'] = '#012732';
        $data['scheme6'] = '#222222';
        $data['scheme7'] = '#222222';
        $data['scheme8'] = '#ffffff';
        $data['scheme9'] = '#ffffff';
        $data['scheme10'] = '#ffffff';

        if(is_array($scheme)){
            $data['scheme1'] = explode(',', $scheme)[0];
            $data['scheme2'] = explode(',', $scheme)[1];
            $data['scheme3'] = explode(',', $scheme)[2];
            $data['scheme4'] = explode(',', $scheme)[3];
            $data['scheme5'] = explode(',', $scheme)[4];
            $data['scheme6'] = explode(',', $scheme)[5];
            $data['scheme7'] = explode(',', $scheme)[6];
            $data['scheme8'] = explode(',', $scheme)[7];
            $data['scheme9'] = explode(',', $scheme)[8];
            $data['scheme10'] = explode(',', $scheme)[9];
        }

        return $data;
    }

    public function remove_cart_item()
    {
        $cart = $this->cart->getItems();
        foreach ($cart as $item) {
            $this->cart->removeItem($item->id);
        }
    }

    public function transactionReceiptPDF(Request $request, $slug)
    {
         // dd($request->qty);
        //dd($request->all());
        $transaction_id = Transactions::where('transaction_key', $request->transaction_id)->first();
        $donations = Donations::where('transaction_id', $transaction_id->id)->get();
        $transaction_details = TransactionDetails::where('transaction_id', $transaction_id->id)->get();
        $event = TransactionDetails::where('transaction_id', $transaction_id->id)
                                    ->where('event_id', '!=', '0')
                                    ->get();
        $donation = TransactionDetails::where('transaction_id', $transaction_id->id)
                                    ->where('event_id','0')
                                    ->get();
                                    // dd(count($event));
        #dd($transaction_id,$donations,$transaction_details, count($event), count($donation));
        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Transaction Receipt</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '<div style="text-align:center;"><h4 style="text-transform:uppercase">'. $request->organization_name.'</h4>';
        $html .= '<h5 style="line-height:1px">'. $request->organization_contact_number.' </h5>';
        $html .= '<h5 style="line-height:1px">'. $request->organization_email.'</h5>';
        $html .= '<h5 style="line-height:1px">TRANSACTION ID:'. $request->transaction_id.'</h5>';
        $html .= '<h5 style="text-align:center; text-transform:uppercase; line-height:1px"><b>Date:'. $request->date_now.'</b></h5>';
        $html .= '</div><hr>';
        // foreach($donations as $donation){
        if(count($event) > 0){
            $html .= '<div style="width:100%;"><h3 style="width:50%; display:inline-block; padding-left:190px;">Event</h3>';
            for($x = 0; $x < count($event); $x++){
                $html .= '<div style="background:#ffffff; width:100%; height:auto; padding-left:150px; padding-right:150px;">';
                // if(strpos($donations[$x]->amount, ".") !== false){
                //     $amount = $donations[$x]->amount;
                // }else{
                //     $amount = $donations[$x]->amount.'.00';
                // }
                // $start_date = Carbon::parse($donations[$x]->start_date)->format('m/d/y H:s A');
                // $end_date   = Carbon::parse($donations[$x]->end_date)->format('m/d/y H:s A');
                $event_count= count($event[$x]->Event);
                $html .= '<ul style="list-style-type:none;">';
                // dd($transaction_details[$x]->Event);
                if(isset( $event[$x]->Event)){

                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Authorization Code:</div><div style="width:50%; display:inline-block">'.(isset($request->event_product[$x])?$request->event_product[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Name:</div><div style="width:50%; display:inline-block">'. (isset($request->event_name[$x])?$request->event_name[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Description:</div><div style="width:50%; display:inline-block">'. (isset($request->event_description[$x])?$request->event_description[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Fee:</div><div style="width:50%; display:inline-block">$'. (isset($request->fee[$x])?$request->fee[$x]:'---').'</div> </li>';

                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Qty:</div><div style="width:50%; display:inline-block">'.(isset($request->qty[$x])?$request->qty[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Start Date:</div><div style="width:50%; display:inline-block">'.(isset($request->event_start_date[$x])? $request->event_start_date[$x] : '---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event End Date:</div><div style="width:50%; display:inline-block">'. (isset($request->event_end_date[$x])? $request->event_end_date[$x] : '---').'</div> </li>';
                    if(isset($request->recurring[$x]) > 0){
                        if($request->recurring[$x] == 3){
                            $frequency = 'Yearly';
                        }else if($request->recurring[$x]  == 2){
                            $frequency = 'Monthly';
                        }else{
                            $frequency = 'Weekly';
                        }
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event No. of Repetition:</div><div style="width:50%; display:inline-block">'. (isset($request->no_of_repetition[$x])?$request->no_of_repetition[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Recurring End Date:</div><div style="width:50%; display:inline-block">'. (isset($request->recurring_end_date[$x])?$request->recurring_end_date[$x]:'---') .'</div> </li>';   
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Recurring Event Frequency:</div><div style="width:50%; display:inline-block">'. (isset($frequency)?$frequency:'---').'</div> </li>';
                    }
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Amount:</div><div style="width:50%; display:inline-block"><b>$'. (isset($request->event_total[$x])?$request->event_total[$x]:'---').'</b></div></li>';
                    $html .= '<li style="width:100%;"><div style="width:100%; display:inline-block; text-align:center">---------------------------------------------------------------------------------</div> </li>';
                    
                }
                $html .= '</ul>';
                $html .= '</div>';
            }
        }
        if(count($donation) > 0){
            $html .= '<div style="width:100%;"><h3 style="width:50%; display:inline-block; padding-left:90px;">Donation</h3>';
            for($x = 0; $x < count($donation); $x++){
                $html .= '<div style="background:#ffffff; width:100%; height:auto; padding-left:150px; padding-right:150px;">';
                // if(strpos($donations[$x]->amount, ".") !== false){
                //     $amount = $donations[$x]->amount;
                // }else{
                //     $amount = $donations[$x]->amount.'.00';
                // }
                // $start_date = Carbon::parse($donations[$x]->start_date)->format('n/j/Y');
                // $end_date   = Carbon::parse($donations[$x]->end_date)->format('n/j/Y'); 
                $donations_count = count($donations[$x]->donation_type);
                $html .= '<ul style="list-style-type:none;">';
                if(!isset( $donation[$x]->Event)){
                
                $html .= '<li><div style="width:50%; display:inline-block">Authorization Code:</div><div style="width:50%; display:inline-block">'.(isset($request->donation_product[$x])?$request->donation_product[$x] :'---').'</div> </li>';
                $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Donation Type:</div><div style="width:50%; display:inline-block">'. (isset($request->donation_type[$x])?$request->donation_type[$x]:'').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Fund to Donate:</div><div style="width:50%; display:inline-block">'.(isset($request->donationList_title[$x])?$request->donationList_title[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Details:</div><div style="width:50%; display:inline-block">'. (isset($request->donation_description[$x])?$request->donation_description[$x]:'---').'</div> </li>';
                    if(isset($donations[$x]->donation_type) == 'Recurring'){
                        $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Start Date:</div><div style="width:50%; display:inline-block">'.(isset($request->donation_start_date[$x])? $request->donation_start_date[$x] : '---').'</div> </li>';
                        $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">End Date:</div><div style="width:50%; display:inline-block">'. (isset($request->donation_end_date[$x])? $request->donation_end_date[$x] : '---').'</div> </li>';
                        $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Recurring Donation Frequency:</div><div style="width:50%; display:inline-block">'. (isset($request->frequency_title[$x])?$request->frequency_title[$x]:'---').'</div> </li>';
                    }
                $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Amount:</div><div style="width:50%; display:inline-block"><b>$'. (isset($request->donation_total[$x])?$request->donation_total[$x]:'---').'</b></div></li>';
                $html .= '<li style="width:100%;"><div style="width:100%; display:inline-block; text-align:center">---------------------------------------------------------------------------------</div> </li>';
                }
                
                $html .= '</ul>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        $html .= '<div style="width:50%; display:inline-block; padding-left:190px;"><b>Total Amount:</b></div><div style="margin-left:-175px; display:inline-block"><b>$'. $request->total_amount.'</b></div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Transaction Receipt_'.Carbon::now());
    }

    public function sendEmailTransaction(Request $request){
        $data = $request->all();
        // dd($data);
        Mail::send('cocard-church.email.donation', ['data' => $data, 'request' => $request], function ($m) use ($data) {
            $m->to(trim($data['email']), trim($data['email']))->subject('Transaction Details');
        });
        return back()->with('success','Email Receipt Sent');
    }
    public function transaction(Request $request, $slug){
        
        $data['user'] = Auth::user();
        $data['organization'] = $this->organization;
        $data['organization_id'] = $data['organization']->id;
        $data['slug'] = $data['organization']->url;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        $amount = 0;
        $data['cart'] = $this->cart->getItems();
        $data['organization_name'] = $this->organization->name;
        $data['organization_contact_number'] = $this->organization->contact_number;
        $data['organization_email'] = $this->organization->email;
        $data['email'] = (Auth::user())?Auth::user()->email : '';
        // dd($data['cart']);
        //dd($cart); 
        // dd(md5(uniqid(rand(), true)), md5(uniqid(rand(), true)));
       //dd($cart, $request, $this->organization->name);
        $transaction_token = Transactions::where('token',$request->input('token') )->get();
        if(count($transaction_token) == null)
        {
            foreach($data['cart'] as $item){
                if($item !=null){
                    // $data['name'] = $item->name;
                    // dd($cart);
                    $item->token = $request->input('token');
                    $item->transaction_key =  md5(uniqid(rand(), true));
                    $item->organization_id =  $this->organization->id;
                    foreach ($data['cart'] as $key) {
                       $amount+= $key->amount;
                    }
                    $item->total_amount    = $amount;
                    $data['transaction_token'] = Transactions::where('token',$item->token )->get();
                    // if(count($data['transaction_token']) == null)
                    // {
                        $this->participant->save($item);
                        $this->transaction->save($item);  
                       // dd($item);
                        if($item->event_id != ''){
                            $item->participant_email = $item->email;
                            $this->queued_message->sentReminderMessage($item, $id=null);
                        }
                    // }
                    // dd($data['transaction_key']);
                }
                // dd($item->token, $request->input('token'));
                $data['token'] = Transactions::where('token',$item->token )->first();
                $data['guest_email'] = $item->email;
            }
            $data['transaction_key'] =  $data['token']->transaction_key;
        }
        $this->remove_cart_item();
        // dd($this->cart->getItems());
        return view('cocard-church.billing.result_zero', $data);
    }

    public function transactionReceiptPDFZero(Request $request, $slug)
    {
        $transaction_id = Transactions::where('transaction_key', $request->transaction_id)->first();
        $donations = Donations::where('transaction_id', $transaction_id->id)->get();
        $transaction_details = TransactionDetails::where('transaction_id', $transaction_id->id)->get();
        $event = TransactionDetails::where('transaction_id', $transaction_id->id)
                                    ->where('event_id', '!=', '0')
                                    ->get();
        $donation = TransactionDetails::where('transaction_id', $transaction_id->id)
                                    ->where('event_id','0')
                                    ->get();
        #dd($transaction_id,$donations,$transaction_details, count($event), count($donation));
        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Transaction Receipt</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '<div style="text-align:center;"><h4 style="text-transform:uppercase">'. $request->organization_name.'</h4>';
        $html .= '<h5 style="line-height:1px">'. $request->organization_contact_number.' </h5>';
        $html .= '<h5 style="line-height:1px">'. $request->organization_email.'</h5>';
        $html .= '<h5 style="line-height:1px">TRANSACTION ID:'. $request->transaction_id.'</h5>';
        $html .= '<h5 style="text-align:center; text-transform:uppercase; line-height:1px"><b>Date:'.Carbon::parse()->format('m/d/y') .'</b></h5>';
        $html .= '</div><hr>';
        // foreach($donations as $donation){
        if(count($event) > 0){
            $html .= '<div style="width:100%;"><h3 style="width:50%; display:inline-block; padding-left:190px;">Event</h3>';
            for($x = 0; $x < count($event); $x++){
                $html .= '<div style="background:#ffffff; width:100%; height:auto; padding-left:150px; padding-right:150px;">';
            
                $event_count= count($event[$x]->Event);
                $html .= '<ul style="list-style-type:none;">';
                if(isset( $event[$x]->Event)){

                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Authorization Code:</div><div style="width:50%; display:inline-block">'.(isset($request->event_product[$x])?$request->event_product[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Name:</div><div style="width:50%; display:inline-block">'. (isset($request->event_name[$x])?$request->event_name[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Description:</div><div style="width:50%; display:inline-block">'. (isset($request->event_description[$x])?$request->event_description[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Fee:</div><div style="width:50%; display:inline-block">$'. (isset($request->fee[$x])?$request->fee[$x]:'---').'</div> </li>';

                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Qty:</div><div style="width:50%; display:inline-block">'.(isset($request->qty[$x])?$request->qty[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Start Date:</div><div style="width:50%; display:inline-block">'.(isset($request->event_start_date[$x])? $request->event_start_date[$x] : '---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event End Date:</div><div style="width:50%; display:inline-block">'. (isset($request->event_end_date[$x])? $request->event_end_date[$x] : '---').'</div> </li>';
                    if(isset($request->recurring[$x]) > 0){
                        if($request->recurring[$x] == 3){
                            $frequency = 'Yearly';
                        }else if($request->recurring[$x]  == 2){
                            $frequency = 'Monthly';
                        }else{
                            $frequency = 'Weekly';
                        }
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event No. of Repetition:</div><div style="width:50%; display:inline-block">'. (isset($request->no_of_repetition[$x])?$request->no_of_repetition[$x]:'---').'</div> </li>';
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Event Recurring End Date:</div><div style="width:50%; display:inline-block">'. (isset($request->recurring_end_date[$x])?$request->recurring_end_date[$x]:'---') .'</div> </li>';   
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Recurring Event Frequency:</div><div style="width:50%; display:inline-block">'. (isset($frequency)?$frequency:'---').'</div> </li>';
                    }
                    $html .= '<li style="width:100%;"><div style="width:50%; display:inline-block">Amount:</div><div style="width:50%; display:inline-block"><b>$'. (isset($request->event_total[$x])?$request->event_total[$x]:'---').'</b></div></li>';
                    $html .= '<li style="width:100%;"><div style="width:100%; display:inline-block; text-align:center">---------------------------------------------------------------------------------</div> </li>';
                    
                }
                $html .= '</ul>';
                $html .= '</div>';
            }
        }
        $html .= '</div>';
        $html .= '<div style="width:50%; display:inline-block; padding-left:190px;"><b>Total Amount:</b></div><div style="margin-left:-175px; display:inline-block"><b>$0.00</b></div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Transaction Receipt_'.Carbon::now());
    }
    public function sendEmailTransactionZero(Request $request){
        $data = $request->all();
        Mail::send('cocard-church.email.event_zero', ['data' => $data, 'request' => $request], function ($m) use ($data) {
            $m->to(trim($data['email']), trim($data['email']))->subject('Transaction Details');
        });
        return back()->with('success','Email Receipt Sent');
    }
}
