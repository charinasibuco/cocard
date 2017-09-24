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
use Acme\Repositories\ActivityLogRepository;
use App\DonationCategory;

use Gate;
use Auth;
use App;
class DonationListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct(ActivityLogRepository $activityLog,DonationListRepository $donationList,DonationRepository $donation,DonationCategoryRepository $donationCategory, FrequencyRepository $frequency, OrganizationRepository $organization){
        //  $this->middleware('auth');
        $this->donationCategory = $donationCategory;
        $this->donationList = $donationList;
        $this->donation = $donation;
        $this->cart = session('cart');
        $this->frequency = $frequency;
        $this->activityLog = $activityLog;
        //  $this->auth = Auth::user();
        $this->organization = $organization;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
     }

    public function index(Request $request, $slug)
    {
        $data['organization']               = $this->organization->getUrl($slug);
        $data['slug']                       = $data['organization']->url;
        $auth = $this->activityLog->AuthGate(Gate::allows('view_donation_list'),Auth::user()->organization_id,$data['organization']->id);

        if($auth == false)
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['donationList'] 				= $this->donationList->getDonationList($request,$data['organization']->id);
            #dd($data['organization']->id);
            $data['donationCategory']           = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
            $data['search'] 					= $request->input('search');
            $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
            if($request->type =="json"){
              return $data;
            }
            return view('cocard-church.church.admin.donation',$data);
        }
    }

    public function admin_donationlist(Request $request, $slug)
    {
        $data['organization']               = $this->organization->getUrl($slug);
        $data['slug']                       = $data['organization']->url;

        if(Gate::denies('view_donation_list'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['donationList']               = $this->donationList->getDonationList($request,$data['organization']->id);
                #dd($data['organization']->id);
                $data['donationCategory']           = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                $data['search']                     = $request->input('search');
                $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
                if($request->type =="json"){
                  return $data;
                }
                return view('cocard-church.church.admin.donation.donationlist',$data);
            }else{
                return view('errors.errorpage'); 
            }
            
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $slug)
    {
        $data = $this->donationList->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        

        if(Gate::denies('add_donation_list'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                $data['search'] = $request->input('search');
                $data['name']                  = '';
                $data['description']           = '';
                $data['recurring']             = '';
                if($request->type =="json"){
                  return $data;
                }
                return view('cocard-church.church.admin.donation.list',$data);
            }else{
                return view('errors.errorpage');
            }   
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id=0)
    {
       # dd($id);
        $slug                   = $request->slug;
        $results                = $this->donationList->save($request,$id);
        $data['organization'] = $this->organization->getUrl($slug);
        #dd($results['status'] );
        if($request->type =="json"){
          return $data;
        }
       if($results['status'] == false)
        {
           return redirect('/organization/'.$slug.'/administrator/donation')->withErrors($results['results'])->withInput();
        }
         if($id > 0){
            $this->activityLog->log_activity(Auth::user()->id,'Updated Donation List','Updated Donation List fields', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/donation-list')->with('success', 'Successfully Updated!');
        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Donation List','Added Donation List fields', $data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/donation-list')->with('success', 'Successfully Added!');
        }

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
    public function edit(Request $request,$slug, $id)
    {
        $data = $this->donationList->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('edit_donation_list'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                if($request->type =="json"){
                  return $data;
                }
                 return view('cocard-church.church.admin.donation.list',$data);
            }else{
                return view('errors.errorpage');
            }
             
         }
    }
     public function delete(Request $request,$slug, $id)
    {
        $data = $this->donationList->delete($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationList']                = $this->donationList->getDonationList($request,$data['organization']->id);
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization']->id);

        if(Gate::denies('delete_donation_list'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                if($request->type =="json"){
                  return $data;
                }
                $this->activityLog->log_activity(Auth::user()->id,'Deleted Donation List','Deleted Donation List.', $data['organization']->id);
                return redirect('/organization/'.$slug.'/administrator/donation-list')->with('success', 'Successfully Deleted Donation List!');
            }else{
                return view('errors.errorpage');
            }
            
        }
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
