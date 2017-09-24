<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Acme\Repositories\PendingOrganizationUsersRepository;
use Acme\Repositories\OrganizationRepository as Organization;
use Auth;
use App;
use Gate;


class PendingOrganizationUsersController extends Controller
{
    public function __construct(Organization $organization, PendingOrganizationUsersRepository $pendingOrganizationUser){
       // $this->middleware('auth');
       $this->organization = $organization;
        $this->pendingOrganizationUser = $pendingOrganizationUser;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    public function index(Request $request){
        if(Gate::denies('view_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
          $data['pendingOrganizationUser'] = $this->pendingOrganizationUser->getPendingOrganizationStatus($request);
          $data['search'] = $request->input('search');
          if($request->type =="json"){
            return $data;
          }
          return view('cocard-church.superadmin.pendingorganization',$data);
        }
    }
    public function indexStatus(Request $request,$status){
        if(Gate::denies('view_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
          $data['status'] = $status;
          $data['pendingOrganizationUser'] = $this->pendingOrganizationUser->getPendingOrganizationStatus($request,$status);
          $data['search'] = $request->input('search');
          if($request->type =="json"){
            return $data;
          }
          return view('cocard-church.superadmin.pendingorganization',$data);
        }
    }
    public function save(Request $request){

         $results = $this->pendingOrganizationUser->save($request);
         #dd($results['status']);
         if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            return redirect('/register')->withErrors($results['results'])->withInput();
        }
        return redirect()->route('home_page')->with('message', 'Request successfully sent. Please wait for a confirmation email.');

    }
    public function edit($id)
    {
        if(Gate::denies('edit_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
           $data = $this->pendingOrganizationUser->edit($id);
           return view('views.auth.registration', $data);
        }
    }
    public function reviewPending($id)
    {
        if(Gate::denies('view_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
           $data = $this->pendingOrganizationUser->reviewPending($id);
           return view('cocard-church.superadmin.review', $data);
        }
    }
     public function reviewPendingDeclined($id)
    {
        if(Gate::denies('view_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
           $data = $this->pendingOrganizationUser->reviewPendingDeclined($id);
           return view('cocard-church.superadmin.review', $data);
        }

    }
    public function reviewPendingToActive($id)
    {
        if(Gate::denies('view_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
           $data = $this->pendingOrganizationUser->reviewPending($id);
           return view('cocard-church.superadmin.reviewPending', $data);
        }

    }
    

    public function updateDeclined(Request $request , $id){
        $results = $this->pendingOrganizationUser->saveDeclined($request, $id);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            #return redirect()->route('pending_organization', $id)->withErrors($results['results'])->withInput();
            return redirect('/organizations')->withErrors($results['results'])->withInput();
        }
         #return redirect()->route('pending_organization', $id)->with('message', 'Organization has been Declined.');
         return redirect('/organizations')->with('message', 'Organization has been Declined.');
    }
    public function updateInactive(Request $request , $id){
        $results = $this->pendingOrganizationUser->saveInactive($request, $id);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            #return redirect()->route('pending_organization', $id)->withErrors($results['results'])->withInput();
            return redirect('/organizations')->withErrors($results['results'])->withInput();
        }
         #return redirect()->route('pending_organization', $id)->with('message', 'Successfully Update Page');
         return redirect('/organizations')->with('message', 'Organization has been Deactivated.');
    }
    public function updateApprove(Request $request , $id){

        $results = $this->pendingOrganizationUser->saveApprove($request, $id);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            return back()->withErrors($results['results'])->withInput();
        }
         return redirect('/organizations')->with('message', 'Successfully Approved Organization.');
    }
     public function updateActive(Request $request , $id){
        $results = $this->pendingOrganizationUser->saveActive($request, $id);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            #return redirect()->route('pending_organization', $id)->withErrors($results['results'])->withInput();
            return redirect('/organizations')->withErrors($results['results'])->withInput();
        }
         #return redirect()->route('pending_organization', $id)->with('message', 'Successfully Update Page');
         return redirect('/organizations')->with('message', 'Organization has been Activated.');
    }
     public function updateReview(Request $request , $id){
        $results = $this->pendingOrganizationUser->saveReview($request, $id);
        #dd($request);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            #return redirect()->route('pending_organization', $id)->withErrors($results['results'])->withInput();
            #return redirect('/pending_organization/status/'.$request->status)->withErrors($results['results'])->withInput();
            return redirect('/superadmin/review-organization/'.$id)->withErrors($results['results'])->withInput();
        }
          #return redirect('/pending_organization/status/'.$request->status)->withErrors($results['results'])->withInput();
         #return redirect()->route('pending_organization', $id)->with('message', 'Successfully Update Page');
         return redirect('/organizations')->with('message', 'Successfully Update Page');
    }
    public function destroy($id)
    {
        if(Gate::denies('delete_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $this->pendingOrganizationUser->destroy($id);
            return redirect()->route('pending_organization')->with('status','Pending Organization successfully deleted!');
        }
    }
}
