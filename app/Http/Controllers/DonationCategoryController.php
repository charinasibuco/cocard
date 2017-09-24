<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Acme\Repositories\DonationCategoryRepository;
use Acme\Repositories\DonationListRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\FrequencyRepository;
use Acme\Repositories\ActivityLogRepository;
use App;
use Gate;

class DonationCategoryController extends Controller
{
   public function __construct(ActivityLogRepository $activityLog,DonationListRepository $donationList,OrganizationRepository $organization,DonationCategoryRepository $donationCategory, FrequencyRepository $frequency){
    $this->middleware('auth');
    $this->donationList = $donationList;
    $this->donationCategory = $donationCategory;
    $this->organization = $organization;
    $this->frequency = $frequency;
    $this->activityLog = $activityLog;
    $this->auth = Auth::user();
    if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    public function index(Request $request)
    {
        if(Gate::denies('view_donation_category'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['donationCategory'] = $this->donationCategory->getDonationCategory($request);
            $data['frequency'] = $this->frequency->getFrequency($request);
            $data['search'] = $request->input('search');
            $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
            if($request->type =="json"){
              return $data;
            }
            return view('cocard-church.donation.index',$data);
        }
    }

    public function admin_donationcat(Request $request, $slug)
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
                $data['donationList']         = $this->donationList->getDonationList($request,$data['organization']->id);
                #dd($data['organization']->id);
                $data['donationCategory']           = $this->donationCategory->getDonationCategory($request,$data['organization']->id);
                $data['search']           = $request->input('search');
                $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
                
                if($request->type =="json"){
                  return $data;
                }
                return view('cocard-church.church.admin.donation.donationcategory',$data);
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
        $data = $this->donationCategory->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('add_donation_category'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['organization_id']         = $data['organization']->id;
                $data['donationCategory'] = $this->donationCategory->getDonationCategory($request, $data['organization']);
                $data['search'] = $request->input('search');
                $data['name']                  = '';
                $data['description']           = '';
                if($request->type =="json"){
                  return $data;
                }
                return view('cocard-church.church.admin.donation.category',$data);
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
        #dd($request);
        $slug                   = $request->slug;
        $results                = $this->donationCategory->save($request,$id);
        $data['organization'] = $this->organization->getUrl($slug);
        #dd($results['status'] );
        if($request->type =="json"){
          return $data;
        }

       if($results['status'] == false)
        {

           return redirect('/organization/'.$slug.'/administrator/donation-category')->withErrors($results['results'])->withInput();
        }
        if($id > 0){
            $this->activityLog->log_activity(Auth::user()->id,'Updated Donation Category','Donation Category successfully updated!',$data['organization']->id);
            return redirect('/organization/'.$slug.'/administrator/donation-category')->with('success', 'Changes Saved!');
        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Added Donation Category','Donation Category successfully added!',$data['organization']->id);

        }
        if($request->cb_val == 1){
        return redirect('/organization/'.$slug.'/administrator/donation/create-donation-category')->with('message', 'Successfully Done!');
        }
        return redirect('/organization/'.$slug.'/administrator/donation-category')->with('message', 'Donation Category successfully added!');

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
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('edit_donation_category'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data = $this->donationCategory->edit($id);
                $data['organization'] = $this->organization->getUrl($slug);
                $data['slug']         = $data['organization']->url;
                $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization'] ->id);
                $data['organization_id']         = $data['organization']->id;

                if($request->type =="json"){
                  return $data;
                }
                return view('cocard-church.church.admin.donation.category',$data);
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
    public function delete(Request $request,$slug, $id)
    {
        $data = $this->donationCategory->delete($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['donationList']                = $this->donationList->getDonationList($request,$data['organization']->id);
        $data['donationCategory'] = $this->donationCategory->getDonationCategory($request,$data['organization']->id);

        if(Gate::denies('delete_donation_category'))
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
                $this->activityLog->log_activity(Auth::user()->id,'Deleted Donation Category','Deleted Donation Category. Donation Lists under this category are also deleted.', $data['organization']->id);
                return back()->with('success', 'Successfully Deleted Donation Category!');
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
}
