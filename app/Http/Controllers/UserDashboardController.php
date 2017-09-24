<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\DonationCategoryRepository as DonationCategory;
use Acme\Repositories\DonationListRepository as DonationList;
use Acme\Repositories\FrequencyRepository as Frequency;
use Acme\Repositories\UserRepository as User;
use Acme\Repositories\PageRepository as Page;
use Acme\Repositories\Cart\EasyCart;
use Acme\Repositories\Cart\DonationItem;
use Acme\Repositories\EventRepository as Event;
use Acme\Repositories\ParticipantRepository as Participant;
use Acme\Repositories\VolunteerGroupRepository as VolunteerGroup;
use Acme\Repositories\VolunteerRepository as Volunteer;
use Acme\Repositories\DonationRepository  as Donation;
use Acme\Repositories\ActivityLogRepository as ActivityLog;
#use App\Libraries\QuickBooks;
use Acme\Repositories\TransactionRepository  as Transaction;
use Acme\Common\Constants  as Constants;
use App\Http\Requests;
use App\Role;
use Auth;
use Gate;
use Redirect;
use App;
use Acme\Helper\Api;
use Acme\Common\CommonFunction;

class UserDashboardController extends Controller
{
    use CommonFunction;

    public function __construct(ActivityLog $activityLog, DonationList $donationList,Page $page,Donation $donation,Event $event,User $user,Organization $organization, DonationCategory $donationCategory, Frequency $frequency,Participant $participant, VolunteerGroup $volunteer_group, Volunteer $volunteer, Transaction $transaction){

        $this->donationCategory = $donationCategory;
        $this->donation = $donation;
        $this->donationList = $donationList;
        $this->cart = session('cart');
        $this->event = $event;
        $this->frequency = $frequency;
        $this->user = $user;
        $this->organization = $organization;
        $this->participant = $participant;
        $this->page = $page;
        $this->volunteer_group = $volunteer_group;
        $this->volunteer = $volunteer;
        $this->activityLog = $activityLog;
        #$this->quickbooks = new QuickBooks();
        $this->transaction = $transaction;

        $this->auth = Auth::user();
        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }

    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function donate(Request $request,$slug)
    {
        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;

        if(Gate::denies('view_donation_history')&& empty(Auth::guard('api')->user()))
        {
             return $this->AuthenticationError($request);
        }
        else
        {
            if(empty(Auth::guard('api')->user()))
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::guard('api')->user()->organization_id,$data['organization']->id);
            }

            if($auth == true){
                $data['user']                           = $this->auth;
                $data['organization']                   = $this->organization->getUrl($slug);
                $data['slug']                           = $data['organization']->url;
                //continue the implementation of merging list category
                $data['donationCategory']               = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                $data['donationList']                   = $this->donationList->getDonationList($request,$data['organization']->id,0);
                $data['donation_list']                   = $this->donationList->getDonationList($request,$data['organization']->id);
                #$data['donationListsOnetime']           = $this->donationList->getDonationListPerOrg($request,$data['organization']->id,0);
                #$data['donationListsRecurring']         = $this->donationList->getDonationListPerOrg($request,$data['organization']->id,1);
                $data['frequency']                      = $this->frequency->getFrequency($request);
                $data['search']                         = $request->input('search');
                $data['count']                          = $request->count;
                $cart                                   = $this->cart->getItems();
                $data['cart']                           = $cart;
                $data['total']                          = 0.00;
                $data['token']                 = csrf_token();
                $data[Constants::MODULE] = Constants::DONATION_CATEGORY;

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                foreach ($data['cart'] as $key) {
                    $data['total'] += $key->getAmount();
                    $frequency = $this->frequency->findFrequency($key->getFrequencyId());
                    $key->frequency_title = is_object($frequency)?$frequency->title:"";
                    $key->donationList_title = isset($this->donationList->show($key->getDonationCategoryId())->name)? $this->donationList->show($key->getDonationCategoryId())->name : '' ;
                    $key->donation_type =$key->getDonationType();
                }
                return Api::displayData($data,'cocard-church.user.donate',$request);
            }else{
                 return $this->AuthenticationError($request);
            }
        }
    }
    public function donateonetime(Request $request, $slug = null,$id)
    {
        $data['frequency'] = $this->frequency->getFrequency($request);
        $cart                      = $this->cart->getItems();
        $data['cart']               =$cart;
        $data['total']          = 0.00;
        foreach ($data['cart'] as $key) {
            $data['total']  = ($data['total']  + $key->getAmount());
        }
        $data['organization']       = $this->organization->getUrl($slug);
        $data['donationCategory']   = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
    #$data['donationLists']       = $this->donationList->getDonationListPerOrg($request,$data['organization']->id);
        $data['user']               = $this->auth;
    #dd($data['user']);
        $data['donationList']       = $this->donationList->show($id);
        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        return view('cocard-church.user.donateonetime',$data);
    }
    public function donaterecurring(Request $request, $slug = null,$id)
    {
        #dd($request);
        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;

        if(Gate::denies('view_donation_history'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $cart                      = $this->cart->getItems();
                $data['cart']               =$cart;
                $data['total']              = 0.00;
                $data['note']               = '';
                foreach ($data['cart'] as $key) {
                    $data['total']  = ($data['total']  + $key->getAmount());
                }
                $data['frequency']          = $this->frequency->getFrequency($request);
                $data['organization']       = $this->organization->getUrl($slug);
                $data['donationCategory']   = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                $data['donation_list']       = $this->donationList->getDonationList($request,$data['organization']->id);
                $data['user']               = $this->auth;
                #dd($data['user']);
                $data['donationList']       = $this->donationList->show($id);
                $data['organization']       = $this->organization->getUrl($slug);
                $data['slug']               = $data['organization']->url;
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                return view('cocard-church.user.donaterecurring',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }


    public function addtocartuser(Request $request,$id=0){
        #$cart = session('cart');
        $data['frequency']          = $this->frequency->getFrequency($request);
        $data['user']               = $this->auth;
        $slug                       = $request->slug;
        $input                      = $request->except(['_token','slug']);
        if($request->donation_type  == "One-Time"){
            $input                      = $request->except(['_token','slug','frequency_id']);
        }
        #dd( $request->frequency_id);
        $input['id']                = $this->cart->generateTransctionID(15);
        $this->cart->addItem(new DonationItem($input),'donation');

        $data['organization']       = $this->organization->getOrganization($request);
        $cart                       = $this->cart->getItems();
        $data['cart']               =$cart;
        #dd($cart->id);

        $data['count']                  = $request->count;
        $data['slug']           = $request->slug;
        if($request->json =="true"){
          return $data;
        }
        return redirect('/organization/'.$slug.'/user/donate')->with('message', 'You have successfully added an item to your cart!');
    }

    public function calendar(Request $request,$slug = null){

        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;
        if(Gate::denies('view_event_history') && empty(Auth::guard('api')->user()))
        {
             return $this->AuthenticationError($request);
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Api::getUserByMiddleware()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['organization'] = $this->organization->getUrl($slug);
                $data['needing_volunteers'] = (isset($request->slot_filter))?$request->slot_filter:"unfiltered";
                $data['alt_needing_volunteers'] = ($data['needing_volunteers'] == "unfiltered")?"filtered":"unfiltered";
                $data['events'] = $this->event->getEvent($request,$data['organization']->id);

                $eventList = $this->event->getEventList($request,$data['organization']->id);
                $data['eventsList'] = $this->extractRecurringDates($eventList,$request);

                $data['needing_volunteers'] = (isset($request->slot_filter))?$request->slot_filter:"unfiltered";
                $data['alt_needing_volunteers'] = ($data['needing_volunteers'] == "unfiltered")?"filtered":"unfiltered";
                $data['slug']         = $data['organization']->url;
                $data['volunteer_group_types'] = $this->volunteer_group->getUniqueTypes();
                $data['donation_list']       = $this->donationList->getDonationList($request,$data['organization']->id);
                $data['user']                           = $this->auth;
                $cart                                   = $this->cart->getItems();
                $data['cart']                           = $cart;
                $data['frequency'] = $this->frequency->getFrequency($request);
                $data['total']                          = 0.00;
                foreach ($data['cart'] as $key) {
                    $data['total']  = ($data['total']  + $key->getAmount());

                }
                $data['organization_id']         =     $data['organization']->id;
                $data[Constants::MODULE] = Constants::EVENT_CALENDAR;

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);

                return Api::displayData($data,'cocard-church.user.calendar',$request);

            }else{
                 return $this->AuthenticationError($request);
            }
        }
    }

    public function volunteer(Request $request, $slug){
        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;

        if(Gate::denies('view_volunteer_history') && empty(Auth::guard('api')->user()))
        {
             return $this->AuthenticationError($request);
        }
        else
        {
            if(empty(Auth::guard('api')->user()))
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::guard('api')->user()->organization_id,$data['organization']->id);
            }
            if($auth == true){
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug'] = $this->organization->getUrl($slug)->url;
                $data['page'] = 'page='.$request->page;
                $organization_id = $data['organization']->id;
                $request["user"] = true;
                $this->slug = $data['slug'];
                $id = '';

                // dd($data);
                $events = $this->event->groupByEvent($organization_id,$request);
                foreach($events as $event)
                {
                    $event->VolunteerGroupByType;
                
                    foreach(@$event->VolunteerGroupByType as $vg)
                    {
                        $vg->events_schedule = $event->volunteerGroupsUnderType($vg->type);
                        $vg->required_participants = $this->volunteer_group->allVolunteersApproved($event->id,$vg->type);
                        $vg->total_participants_needed = $this->volunteer_group->allVolunteerGroupsNeeded($event,$vg);
                    }
                }

                $data["events"] = $events;

                //$data['events_table'] = $this->event->filterEventsByRole($this->event->needsVolunteers(),"",TRUE);
                $data['volunteer_role_titles'] = $this->volunteer_group->getUniqueTypes();
                //$data['volunteer_groups'] = $this->volunteer_group->getUserVolunteerGroup($request,$organization_id);
                $data['volunteers'] = $this->volunteer->getvolunteer();
                $data['volunteer_groups'] =$this->volunteer_group->getvolunteer_group();
                $data['volunteer_group_by_field'] = $this->volunteer_group->groupByField('event_id',$organization_id,$request);
                $data['id'] = '';
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                $data[Constants::MODULE] = Constants::VOLUNTEER_GROUP_LIST;
                //return Api::displayData($data,'cocard-church.user.volunteerlist',$request);
                return Api::displayData($data,'cocard-church.user.volunteer_group',$request);
            }else{
                return $this->AuthenticationError($request);
            }
        }
    }
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
}
