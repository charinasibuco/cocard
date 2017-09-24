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
use Acme\Repositories\QuickBooksRepository as QB;
use Acme\Repositories\RoleRepository as Roles;
use Acme\Repositories\ActivityLogRepository as ActivityLog;
//use App\Libraries\QuickBooks;
use Acme\Repositories\TransactionRepository  as Transaction;
//use App\Event;
use App\Http\Requests;
//use App\Organization;
//use App\Page;
use App\Role;
use Auth;
use Gate;
use Redirect;
use App;
use Acme\Helper\Api;
use App\VolunteerGroup as VolunteerGroups;
use Acme\Common\Constants as Constants;
use Acme\Common\CommonFunction as CommonFunction;


class ChurchController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    use CommonFunction;

    public function __construct(QB $quickbooks,DonationList $donationList,Page $page,Donation $donation,Event $event,User $user,Organization $organization, DonationCategory $donationCategory, Frequency $frequency,Participant $participant, VolunteerGroup $volunteer_group, Volunteer $volunteer, Transaction $transaction, Roles $role, ActivityLog $activityLog){

        #$this->middleware('auth');
        #$this->boot();
        $this->donationCategory = $donationCategory;
        $this->donation = $donation;
        $this->donationList = $donationList;
        $this->cart = session('cart');
        $this->event = $event;
        $this->frequency = $frequency;
        $this->user = $user;
        $this->role = $role;
        $this->organization = $organization;
        $this->participant = $participant;
        $this->page = $page;
        $this->volunteer_group = $volunteer_group;
        $this->volunteer = $volunteer;
        $this->quickbooks = $quickbooks;
        $this->transaction = $transaction;
        $this->activityLog = $activityLog;
//
        $this->auth = Auth::user();
        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }

    }
    //Display the admin dashboard
    public function index($slug)
    {
        if(Auth::guest()){
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
            $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
            $data = array_merge($data,$theme);
            ///to do: put Gate()
            #dd($data['organization'] );
            if(isset($data['organization']->status) && $data['organization']->status == 'Active'){
                //default to login
                return view('auth.user-login', $data);
            }
            else if(isset($data['organization']->status) && $data['organization']->status == 'InActive'){
                $data['error_message'] = 'Organization has been Deactivated!';
                return view('errors.organizationDeactivate', $data);
            }
            else{
                return 'Organization Not Found';
            }
        }else{
            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/dashboard');
            }
            elseif(Auth::user()->hasRole('superadmin')){
                return redirect('dashboard');
            }
            else{
                return redirect('organization/'.$slug.'/administrator/dashboard');
                 // return 'lol';
            }
        }
    }

    public function indexHome($slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        ///to do: put Gate()
        #dd($data['organization'] );
        if(isset($data['organization']->status) && $data['organization']->status == 'Active'){
            return view('cocard-church.church.index', $data);
            //default to login
            #return view('cocard-church.church.login', $data);
        }
        elseif(isset($data['organization']->status) && $data['organization']->status == 'InActive'){
            $data['error_message'] = 'Organization has been Deactivated!';
            return view('errors.organizationDeactivate', $data);
        }
        else{
            return 'Organization Not Found';
        }
    }


    public function quickbooks($slug){

        $data['organization']       = $this->organization->getUrl($slug);
        $data['slug']               = $data['organization']->url;

        if(Gate::denies('view_quickbooks'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['user']               = $this->auth;
                $credentials = $this->quickbooks->findCredentials($data['user']->organization_id);
                $qb_oauth = is_null($credentials)?"":$credentials->toArray();
                foreach($this->quickbooks->getFillable() as $field){
                    $data[$field] = isset($qb_oauth[$field])?$qb_oauth[$field]:"";
                }

                $data['action'] = route('qb_save');
                return view("cocard-church.church.admin.quickbooks",$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }




    public function donate(Request $request,$slug)
    {
        $data['user']                           = $this->auth;
        $data['organization']                   = $this->organization->getUrl($slug);
        $data['slug']                           = $data['organization']->url;
        $data['donationCategory']               = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
        $data['donationList']                   = $this->donationList->getDonationList($request,$data['organization']->id);
        $data['donation_list']                  = $this->donationList->getDonationList($request,$data['organization']->id);
        $data['donationListsRecurring']         = $this->donationList->getDonationListPerOrg($request,$data['organization']->id,1);
        $data['frequency']                      = $this->frequency->getFrequency($request);
        $data['search']                         = $request->input('search');
        $data['count']                          = $request->count;
        $cart                                   = $this->cart->getItems();
        $data['cart']                           = $cart;
        $data['total']                          = 0.00;
        $data['token']                 = csrf_token();
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        $data[Constants::MODULE] = Constants::DONATION_CATEGORY;

        foreach ($data['cart'] as $key) {
            $data['total'] += $key->getAmount();
            $frequency = $this->frequency->findFrequency($key->getFrequencyId());
            $key->frequency_title = is_object($frequency)?$frequency->title:"";
            #$key->donationCategory_title = $this->donationCategory->findDonationCategory($key->getDonationCategoryId())->name;
            #$key->donationList_title = $this->donationList->show($key->getDonationCategoryId())->name;
            $key->donationList_title = isset($this->donationList->show($key->getDonationCategoryId())->name)? $this->donationList->show($key->getDonationCategoryId())->name : '' ;
            $key->donation_type =$key->getDonationType();
        }
        return Api::displayData($data,'cocard-church.church.donation',$request);
        #return view('cocard-church.church.donation',$data);
    }
    public function donatelist(Request $request,$slug = null)
    {

        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
        $data['donationLists']       = $this->donationList->getDonationListPerOrg($request);

        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['search'] = $request->input('search');
        return view('cocard-church.church.donationlist',$data);
    }
    public function register(Request $request){
        $data = $this->user->create();
        $data['organization'] = $this->organization->getOrganization($request);
        return view('cocard-church.church.register', $data);
    }
    public function dashboard($slug){
        if(Gate::denies('login_admin_dashboard'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                return view('cocard-church.church.admin.dashboard',$data); 
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    public function multipleUserRole(Request $request,$slug){

        if(Gate::denies('login_admin_dashboard'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;

            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['user_roles'] = $this->role->getLoginUserRole();
                $assigned_role = App\UserRole::where('user_id', Auth::user()->id)->first();
                $data['role_id'] = (is_null(old('role_id'))? $assigned_role->role_id : old('role_id'));
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);
                return view('cocard-church.user.multiple_role',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    public function guest_dashboard($slug){
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
            $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
            $data = array_merge($data,$theme);
            return view('cocard-church.user.guest.dashboard',$data);
    }
    public function calendar(Request $request,$slug = null){

        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['needing_volunteers'] = (isset($request->slot_filter))?$request->slot_filter:"unfiltered";
        $data['organization'] = $this->organization->getUrl($slug);
        $data['events'] = ($request->slot_filter == "filtered")?$data['organization']->events_needing_volunteers:$data['organization']->events;


        $eventList = $this->event->getEventList($request,$data['organization']->id);
        $data['eventsList'] = $this->extractRecurringDates($eventList,$request);

        $data['alt_needing_volunteers'] = ($data['needing_volunteers'] == "unfiltered")?"filtered":"unfiltered";
       // $data['events'] = $this->event->getEvent($request,$data['organization']->id);
        $data['frequency'] = $this->frequency->getFrequency($request);
        $data['slug']         = $data['organization']->url;
        $data['volunteer_group_types'] = $this->volunteer_group->getUniqueTypes();
        $data['user']                           = $this->auth;
        $cart                                   = $this->cart->getItems();
        $data['cart']                           = $cart;
        $data['total']                          = 0.00;
        foreach ($data['cart'] as $key) {
            $data['total']  = ($data['total']  + $key->getAmount());

        }
        $data['organization_id']         =     $data['organization']->id;
        $data['donation_list']       = $this->donationList->getDonationList($request,$data['organization']->id);
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        $data[Constants::MODULE] = Constants::EVENT_CALENDAR;

        if(Auth::Guest()){
            return Api::displayData($data,'cocard-church.church.event',$request);
        }else{
            if(Gate::denies('view_event_history') && empty(Auth::guard('api')->user()))
            {
                return view('errors.errorpage');
            }
            else
            {
                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
                if($auth == true){
                    return Api::displayData($data,'cocard-church.church.event',$request);
                    #return view('cocard-church.church.event',$data);
                }else{
                    return view('errors.errorpage');
                }
            }
        } 
    }

    public function eventList(Request $request,$slug = null){
        $data['organization'] = $this->organization->getUrl($slug);
        $data['events'] = $this->event->getEvent($request, $data['organization']->id);
        $data['slug']         = $data['organization']->url;
        $data['user']                           = $this->auth;
        $cart                                   = $this->cart->getItems();
        $data['cart']                           = $cart;
        $data['total']                          = 0.00;
        foreach ($data['cart'] as $key) {
            $data['total']  = ($data['total']  + $key->getAmount());

        }
        $data['organizationid']         =     $data['organization'];
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        return view('cocard-church.church.event-list',$data);
    }

    public function insertcreditcard(Request $request,$slug = null){
        $data['user']         = $this->auth;
        #dd($data['user']);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['organization_id']            = $data['organization']->id;
        $data['slug']         = $data['organization']->url;
        $cart                   = $this->cart->getItems();
        $data['cart']           =$cart;
        $data['total']          = 0.00;
        foreach ($data['cart'] as $key) {
            $data['total']  = ($data['total']  + $key->getAmount());
        }
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        return view('cocard-church.donation.donatecreditinfo',$data);
    }
    public function proceedtopayment(Request $request,$slug = null){

        $data['organization']               = $this->organization->getUrl($slug);
        $data['donationCategory']           = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
        $data['frequency']                  = $this->frequency->getFrequency($request);
        $data['slug']                       = $data['organization']->url;
        $data['total']                      = '0.00';
        $cart                               = $this->cart->getItems();
        $data['cart']                       = $cart;
        #dd( $request);
         //$invoice_id = file_get_contents(route('create_invoice',$customer_id));
        $data['proceedpayment']  = $this->donation->cartTransaction($request);
         $random_number = rand(10,99);
         if($this->quickbooks->findCredentials($data['organization']->id)){
             $customerObj = [
                 "organization_id" => $data['organization']->id,
                 "name" => Auth::user()?Auth::user()->first_name:"Customer",
                 "given_name" => Auth::user()?Auth::user()->last_name:"#".$random_number.date("Ymdhi"),
                 "company_name" => Auth::user()?$data['organization']->name:"Company#".$random_number.date("Ymdhi"),
                 "display_name" => Auth::user()?Auth::user()->full_name:"Customer#".$random_number.date("Ymdhi")
             ];

             $customer_id = $this->quickbooks->post(route('create_customer'),$customerObj);

             foreach($cart as $item){
                 $invoiceObj = [
                     "organization_id" => $data['organization']->id,
                     "id" => $item->id,
                     "doc_number" => rand(1,1000000),
                     "description" => $item->description,
                     "amount" =>$item->price,
                     "customer_id" => $customer_id
                 ];
                 $this->post(route('create_invoice'),$invoiceObj);
             }
         }


        $data['cartitempayment'] = $this->donation->cartItemTransaction($request,$cart);

        foreach ($data['cart'] as $item) {
            $cart                    = $this->cart->removeItem($item->id);
        }
        return redirect('/organization/'.$slug.'/donations')->with('message', 'Thank you so much. Your donation went through.');

    }
    public function postJoin(Request $request){
        $participant = $this->participant->save($request);
        $transaction = $this->transaction->save($request);
        $slug = $request->slug;
        if($participant['status'] == true){
            //to do church
            return redirect('organization/'.$slug.'/events')->with('message',$participant['results']);
        }
        elseif($participant['status'] == false){
            //to do church
            return redirect('organization/'.$slug.'/events')->with('error',$participant['results']);
        }
    }
    public function volunteer(Request $request,$slug = null)
    {
        $data['events'] = $this->event->getEvent($request, '1');
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug'] = $data['organization']->url;
        $data['organizationid'] = $data['organization'];
        return view('cocard-church.church.volunteer', $data);
    }
    public function eventModalDetails(Request $request){
        $data['event'] = $this->event->findEvent($request->id);
        $data['slug'] = $request->slug;
        $data["check_applied_user"] = Auth::user()?$this->event->checkAppliedUser($request->id,Auth::user()->id):0;
         // dd($data);
        return view("cocard-church.church.templates.event_modal_content",$data);

    }
    public function volunteerGroupModalDetails(Request $request){


        $data['event'] = $this->event->findEvent($request->id);
        // if(Auth::guest()){
        //     $data['volunteer_group'] = VolunteerGroups::where('event_id', $request->id)->first(); 
        // }else{
        //     $data['volunteer_group'] = $this->volunteer_group->findVolunteerGroup($request->id);
        // }
        $data['volunteer_group'] = $this->volunteer_group->findVolunteerGroup($request->id);
        //dd($data['volunteer_group']);
        $data['event_id'] = $data['volunteer_group']->event->id;
        $data['slug'] = $request->slug;
        $data["check_applied_user"] = Auth::user()?$this->event->checkAppliedUser($request->id,Auth::user()->id):0;
         // dd($data);
        return view("cocard-church.church.templates.volunteer_group_modal_content",$data);

    }
    /*public function objectInArray($value,$property,Array $array){
    $count = -1;
    foreach($array as $item){
    if($item[$property] == $value){
    $count++;
}
}
return $count;
}*/

    public function filterEvents(Request $request,$display_table = false,$slug = null){
        $input = $request->role_title;
        $data['organization'] = $this->organization->getUrl($slug);
        dd('--------sdsdsd---'.$data['organization']);
        $organization_id= $data['organization']->id;
        $events = ($request->slot_filter == "filtered")?$this->event->needsVolunteers($organization_id):$this->event->allEvents();
        if($slug){
            $organization = $this->organization->getUrl($slug);
            $events = ($request->slot_filter == "filtered")?$organization->events_needing_volunteers:$organization->event;
         // dd($organization->events_needing_volunteers);
        }
        $data = $this->event->filterEventsByGroup($events,$input,$display_table);
         //dd($organization->events_needing_volunteers, $this->event->needsVolunteers());
        // if($display_table == "true"){
        //     $data_["events"] = $data;
        //     $data = view("cocard-church.volunteer.templates.events_table",$data_);
        // }
        return $data;
    }

    public function checkUniqueVolunteerEmail(Request $request,$id){
        $input = $request->email;
        return $this->event->findEvent($id)->checkUnique($input,"email");
    }
    public function login(Request $request, $slug = null)
    {
        if(Auth::guest()){
            $data['organization'] = $this->organization->getUrl($slug);
            $data['slug']         = $data['organization']->url;
            if($data['organization']->banner_image == null){
                $data['banner'] ='background.jpg';
            }else{
                $data['banner'] = $data['organization']->banner_image;
            }
            if($data['organization']->scheme == null){
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
                $data['scheme1'] = explode(',', $data['organization']->scheme)[0];
                $data['scheme2'] = explode(',', $data['organization']->scheme)[1];
                $data['scheme3'] = explode(',', $data['organization']->scheme)[2];
                $data['scheme4'] = explode(',', $data['organization']->scheme)[3];
                $data['scheme5'] = explode(',', $data['organization']->scheme)[4];
                $data['scheme6'] = explode(',', $data['organization']->scheme)[5];
                $data['scheme7'] = explode(',', $data['organization']->scheme)[6];
                $data['scheme8'] = explode(',', $data['organization']->scheme)[7];
                $data['scheme9'] = explode(',', $data['organization']->scheme)[8];
                $data['scheme10'] = explode(',', $data['organization']->scheme)[9];
            }
            if(isset($data['organization']->status) && $data['organization']->status == 'Active'){
                return view('cocard-church.church.login', $data);
            }
            elseif(isset($data['organization']->status) && $data['organization']->status == 'InActive'){
                $data['error_message'] = 'Organization has been Deactivated!';
                return view('errors.organizationDeactivate', $data);
            }
            else{
                return view('errors.errorpage');
            }
        }else{
                if(Auth::user()->hasRole('member')){
                    return redirect('organization/'.$slug.'/user/dashboard');
                }
                elseif(Auth::user()->hasRole('superadmin')){
                    return redirect('dashboard');
                }
                else{
                    return redirect('organization/'.$slug.'/administrator/dashboard');
                     // return 'lol';
                }
            }
    }

    public function getVolunteerForm(Request $request){
        $data = [];
        $data['count'] = $request->count;
        $data['event_id'] = $request->event_id;
        $data['event'] = $this->event->findEvent($data['event_id']);
        return view("cocard-church.church.templates.volunteer_add",$data);
    }

    public function postlogin(Request $request){
        $email      = trim($request->email);
        $password   = trim($request->password);
        $slug       = $request->slug;
        $organization = $this->organization->getUrl($slug);
        $id = $request->id;

        if (Auth::attempt(['email' => $email, 'password' => $password, 'organization_id' => $id, 'status' => 'Active']) || Auth::attempt(['email' => $email, 'password' => $password, 'organization_id' => '0', 'status' => 'Active'])){
            if(Auth::user()->hasRole('member')){
                Auth::Logout();
                return redirect('/organization/'.$slug.'/administrator')->with('message', 'Invalid Username and Password');
            }else{
                if(isset($organization->status) && $organization->status == 'Active'){
                    $user_roles = $this->role->getLoginUserRole();
                    if(count($user_roles) > 1){
                        return redirect('organization/'.$slug.'/administrator/login-as');
                    }else{
                        return redirect('organization/'.$slug.'/administrator/dashboard');
                    }
                }elseif(isset($organization->status) && $organization->status == 'InActive'){
                    $data['error_message'] = 'Organization has been Deactivated!';
                    return view('errors.organizationDeactivate', $data);
                }else{
                    return view('errors.errorpage');
                }
            }  
        }else{
            return redirect('/organization/'.$slug.'/administrator')->with('message', 'Invalid Username and Password');
        }
    }
    public function logout($slug){
        Auth::logout();
        return redirect('/organization/'.$slug.'/');
    }
    public function userlogin(Request $request, $slug = null)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        return view('auth.user-login', $data);

    }

public function donationnew(Request $request, $slug = null)
{
    $data['user']               = $this->auth;
    #dd($data['user']);
    $data['organization']       = $this->organization->getUrl($slug);
    $data['slug']               = $data['organization']->url;
    $data['donationCategory']   = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
    $data['donationLists']       = $this->donationList->getDonationListPerOrg($request,$data['organization']->id,0);
    $data['donationListsRecurring']       = $this->donationList->getDonationListPerOrg($request,$data['organization']->id,1);
    # dd($data['donationList']);
    $data['frequency']          = $this->frequency->getFrequency($request);
    $data['search']             = $request->input('search');
    $data['count']              = $request->count;
    $cart                       = $this->cart->getItems();
    $data['cart']               = $cart;
    $data['total']              = 0.00;
    #dd($cart);
    $data['token']                 = csrf_token();
    foreach ($data['cart'] as $key) {
        #dd($key->getFrequencyId());
        $data['total'] += $key->getAmount();
        $frequency = $this->frequency->findFrequency($key->getFrequencyId());
        $key->frequency_title = is_object($frequency)?$frequency->title:"";
        #$key->donationCategory_title = $this->donationCategory->findDonationCategory($key->getDonationCategoryId())->name;
        #$key->donationList_title = $this->donationList->show($key->getDonationCategoryId())->name;
        $key->donationList_title = isset($this->donationList->show($key->getDonationCategoryId())->name)? $this->donationList->show($key->getDonationCategoryId())->name : '' ;
        $key->donation_type =$key->getDonationType();
        #dd( $data['frequency_title'] );
    }
    return view('cocard-church.church.donationnew',$data);

}
    public function onetimedonation(Request $request, $slug = null,$id)
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
        return view('cocard-church.donation.donateonetime',$data);
    }
    public function recurringdonation(Request $request, $slug,$id)
    {
        #dd($slug);
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
        $data['donationList']       = $this->donationList->show($id);
        $data['donation_list']       = $this->donationList->getDonationList($request,$data['organization']->id);
        $data['user']               = $this->auth;
        $data['slug']               = $data['organization']->url;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        return view('cocard-church.donation.donaterecurring',$data);
    }
    public function cart($slug){
        $cart                                   = $this->cart->getItems();
        $data['cart']                           = $cart;
        return json_encode($data['cart']);
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
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
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
