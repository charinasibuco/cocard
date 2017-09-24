<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Acme\Repositories\DonationCategoryRepository;
use Acme\Repositories\DonationListRepository;
use Acme\Repositories\DonationRepository;
use Acme\Repositories\FrequencyRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\Cart\DonationItem;
use Acme\Repositories\Cart\EasyCart;
use Acme\Common\DataResult as DataResult;
use Acme\Common\Constants as Constants;
use Auth;
use App\Event;
use App;
class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct(DonationListRepository $donationList,DonationRepository $donation,DonationCategoryRepository $donationCategory, FrequencyRepository $frequency, OrganizationRepository $organization){
        #$this->middleware('auth');
        $this->donationCategory = $donationCategory;
        $this->donation = $donation;
        $this->donationList = $donationList;
        $this->cart = session('cart');
        $this->frequency = $frequency;
        //  $this->auth = Auth::user();
        $this->organization = $organization;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
     }
     public function user_donation(Request $request, $slug)
     {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request);
        $data['frequency'] = $this->frequency->getFrequency($request);

        $data['cart'] = '';
        $data['count'] = $request->count;
        if($request->type =="json"){
          return $data;
      }
      return view('cocard-church.user.donation',$data);
     }

     public function add_user_donation(Request $request, $slug)
    {


        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request);
        $data['frequency'] = $this->frequency->getFrequency($request);
        $data['search'] = $request->input('search');
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.donation.userdonation',$data);
    }

     public function addtocart(Request $request,$id=0){
       #$cart = session('cart');
        #dd($request["item"]);
        $data['frequency']          = $this->frequency->getFrequency($request);
        $data['user']               = $this->auth;
        $slug                       = $request->slug;
        $input                      = $request->except(['_token','slug']);
        if($request->donation_type  == "One-Time"){
            $input                  = $request->except(['_token','slug','frequency_id']);
        }
        $input['id']                = $this->cart->generateTransctionID(15);
        $this->cart->addItem(new DonationItem($input),'donation');
        $data['organization']       = $this->organization->getOrganization($request);
        $cart                       = $this->cart->getItems();
        $data['cart']               =$cart;
        #dd($cart->id);
        $data['count']              = $request->count;
        $data['slug']               = $request->slug;
        if($request->type =="json"){
          return $data;
        }
        return redirect('/organization/'.$slug.'/donations')->with('message', 'You have successfully added an item to your cart!');
    }
    public function removeCartitem(Request $request, $slug, $id){

        $item = $this->cart->findItem($id);
        if(isset($item->type) &&  $item->type== 'event'){
          $qty = $item->qty;
          $event = Event::where('id', $item->event_id)->first();
          $event->pending = $event->pending - $qty;
          $event->save();
        }
        // dd($items->event_id);
        $cart                   = $this->cart->removeItem($id);
        $slug                   = $request->slug;
        $data['organization']   = $this->organization->getOrganization($request);
        $data['autoOpenModal'] = true;
        if($request->type =="json"){
          return $data;
        }
         return back()->with('message', 'Item successfully removed from your cart!')->with('error_code',5);


    }
    public function clearCart(Request $request, $slug){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['autoOpenModal'] = true;
        foreach ($this->cart->getItems() as $item) {
          //$cart                    = $this->cart->removeItem($item->id);
          if(!empty($item->qty)){
            $qty = $item->qty;
            $event = Event::where('id', $item->event_id)->first();
            $event->pending = $event->pending - $qty;
            $event->save();
            //$event->pending = $event->pending
          }
          $cart                    = $this->cart->removeItem($item->id);
          // dd($item);
        }
        if($request->type =="json"){
          return $data;
        }
         return back()->with('message', 'Cart Cleared!')->with('error_code',5);


    }
    public function editCartitem(Request $request, $slug, $id){
        #dd($id);
        $cart                   = $this->cart->findItem($id);
        $slug                   = $request->slug;
        $data['organization']   = $this->organization->getOrganization($request);
        if($request->type =="json"){
          return $data;
        }
        return redirect('/organization/'.$slug.'/donations')->with('message', 'Item successfully removed from your cart!');;

    }
         public function updateCartitem(Request $request, $slug, $id){
        #dd($id);
        $input                   = $request->except(['_token','slug']);
        #dd($input);
        $cart                   = $this->cart->updateItem2($id,$input);
        $slug                   = $request->slug;
        $data['organization']   = $this->organization->getOrganization($request);
        if($request->type =="json"){
          return $data;
        }
        return back()->with('message', 'Item successfully updated from your cart!')->with('error_code',5);

    }
    public function index(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request);
        $data['frequency'] = $this->frequency->getFrequency($request);
        $data['search'] = $request->input('search');
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.church.admin.donation',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request);
        $data['frequency'] = $this->frequency->getFrequency($request);
        $data['search'] = $request->input('search');
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.donation.index',$data);
    }
    public function oneTimeAdd(Request $request,$slug){
        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;
        $data['count']                  = $request->count;
        $data['donationCategory']       = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
        $data['donationLists']          = $this->donationList->getDonationListPerOrg($request,$data['organization']->id);
        $data['token']                 = csrf_token();
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.donation.templates.onetime_item',$data);
    }

    public function cancelDonation($slug,  $id){

        $result = new DataResult();

        if(!empty(Auth::guard('api')->user()))
        {
            $this->donation->cancelDonation($id);
            $result->message = Constants::DONATION_CANCELLED;
            return json_encode($result);
        }
        else
        {
            //$this->donation->cancelDonation($id);
            return back()->with('messages', 'Cancelled Donation Successfully');
        }

    }

    public function editDonation($slug, $id){

        $data                 = $this->donation->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['frequencies'] = $this->frequency->getFrequency();
        $data['donation_lists'] = $this->donationList->getUserDonationList();
        $data['donation_categories'] = $this->donationCategory->getUserDonationCategory();

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
            // dd($id);
        return view('cocard-church.user.editdonation',$data);
    }

    public function updateDonation($slug, $id, $request){

        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $results                 = $this->donation->updateDonation($id);

        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->withInput();
        }else{

            return back()->with('message', 'Updated Donation Successfully');
        }

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
            // dd($id);
        return view('cocard-church.user.editdonation',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

public function theme($banner,$scheme)
{
    $data['banner'] = $banner;
    if(!$data['banner']) {
        $data['banner'] ='background.jpg';
    }
    if($scheme == null){
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
    }else{
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

    public function store(Request $request)
    {
        //
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
}
