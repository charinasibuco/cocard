<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\FamilyRepository;
use Acme\Repositories\FamilyMemberRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\UserRepository;
use Acme\Repositories\ActivityLogRepository;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Family;
use App\FamilyMember;
use App\Organization;
use App\User;
use Auth;
use Gate;
use App;
use DB;
use Acme\Helper\Api;
use Acme\Common\Constants as Constants;
use Acme\Common\DataResult as DataResult;
use Acme\Common\CommonFunction;


class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use CommonFunction; 
     
    public function __construct(ActivityLogRepository $activityLog,UserRepository $user,FamilyRepository $family_group, FamilyMemberRepository $family_member, OrganizationRepository $organization){
        #$this->middleware('auth');
        $this->family_group = $family_group;
        $this->family_member = $family_member;
        $this->organization = $organization;
        $this->activityLog = $activityLog;
        $this->user = $user;
        $this->auth = Auth::user();

        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }
    }

    //list of family in each organization (ADMIN DASHBOARD)
    public function family_index(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']  = $slug;

        if(Gate::denies('view_family'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['family_groups']  = $this->family_group->getFamily($request, $slug);
                // dd($data['family_groups']);
                return view('cocard-church.church.admin.family.index',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }

    //list of family assigned to church goer (USER DASHBOARD)
    public function user_family_index(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']  = $slug;

        if(Gate::denies('view_own_family') && empty(Auth::guard('api')->user()))
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
                $data['family_groups'] = $this->family_group->getUserFamily($request);

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data[Constants::MODULE] = Constants::FAMILY_LIST;
                $data = array_merge($data,$theme);

                return Api::displayData($data,'cocard-church.user.family.index',$request);
            }else{
                return $this->AuthenticationError($request);
            }    
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // create family form (ADMIN DASHBOARD)
    public function family_create($slug)
    {
        $data                 = $this->family_group->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('add_family'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.church.admin.family.form',$data);
            }else{
                return view('errors.errorpage');
            }      
        }
    }
    //create family form (USER DASHBOARD)
    public function user_family_create($slug)
    {
        $data                 = $this->family_group->create();
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        if(Gate::denies('view_own_family'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.user.family.form',$data);
            }
            else
            {
                return view('errors.errorpage');
            }
        }
        // if($auth == false)
        // {
        //     return view('errors.errorpage');
        // }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //add/store family
    public function family_store(Request $request)
    {

        $slug                 = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);

        if(!empty(Auth::guard('api')->user()) && $request->type == "json")
        {  
            $results = $this->family_group->user_save($request);

            $result = new DataResult();

            if($results["status"] == true)
            {
                $result->error = false;
                $result->message =  Constants::SUCCESSFULLY_ADDED_FAMILY_GROUP;
                $result->data = $results['input'];
            }
            else
            {
                $result->error = true;
                $result->message = $results['results']->errors()->all();
                $result->data = $results['input'];
            }
            
            return json_encode($result->Iterate());
        }
        else
        {
             return $this->AuthenticationError($request);
        }

        if(Auth::user()->hasRole('member')){
            $results              = $this->family_group->user_save($request);

            if($results['status'] == false)
            {
                return redirect('organization/'.$slug.'/user/family/create')->withErrors($results['results'])->withInput();
            }else{

                if($request->cb_val == 1)
                {
                    return back()->with('message', 'Successfully Added Family Group!');
                }
                else
                {
                    return redirect('/organization/'.$slug.'/user/family')->with('message', 'Successfully Added Family Group!');
                }
            }

        }else{
            $slug                 = $request->slug;
            $data['organization'] = $this->organization->getUrl($slug);
            $results              = $this->family_group->save($request);

            if($results['status'] == false)
            {
                return redirect('organization/'.$slug.'/administrator/family/create')->withErrors($results['results'])->withInput();
            }else{

                if($request->cb_val == 1)
                {
                    return back()->with('message', 'Successfully Added Family Group!');
                }
                else
                {
                    $this->activityLog->log_activity(Auth::user()->id,'Added Family Group','Added New Family Group', $data['organization']->id);
                    return redirect('/organization/'.$slug.'/administrator/family')->with('message', 'Successfully Added Family Group!');
                }
            }
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
    //edit family form (ADMIN DASHBOARD)
    public function family_edit($slug, $id)
    {
        $data                 = $this->family_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;

        if(Gate::denies('edit_family'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.church.admin.family.form',$data);
            }else{
                return view('errors.errorpage');
            } 
        }
    }

    //edit family form (USER DASHBOARD)
    public function user_family_edit(Request $request,$slug, $id)
    {
        
        $data                 = $this->family_group->edit($id);
        $data["family_group"]                 = $this->family_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data[Constants::MODULE] = Constants::INITIALIZE_FAMILY_GROUP;

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        if(Gate::denies('view_own_family') && empty(Auth::guard('api')->user()))
        {
            return $this->AuthenticationError($request);
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Api::getUserByMiddleware()->organization_id,$data['organization']->id);
            if($auth == true){
                return Api::displayData($data,'cocard-church.user.family.form',$request);
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
    //update family
    public function family_update(Request $request, $id)
    {
        $slug                 = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);

        $family_id = $id;
        if($request->has("family_id"))
        {
            $family_id = $request->family_id;
        }

        $results = $this->family_group->save($request, $family_id);

        if(!empty(Auth::guard('api')->user()))
        {  
            $result = new DataResult();

            if($results["status"] == true)
            {
                $result->error = false;
                $result->message =  Constants::SUCCESSFULLY_UPDATED_FAMILY_GROUP;
                $result->data = $results['input'];
                $result->tag = $results['action'];
            }
            else
            {
                $result->error = true;
                $result->message = $results['results'];
                $result->data = $results['input'];
                $result->tag = $results['action'];
            }
            
            return json_encode($result);
        }

        if($results['status'] == false)
        {
            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/family/edit/'.$id)->withErrors($results['results'])->withInput();
            }else{
                return redirect('organization/'.$slug.'/administrator/family/edit/'.$id)->withErrors($results['results'])->withInput();
            }

        }else{
            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/family/')->with('message', 'Successfully Edited Family Group');
            }else{
                $this->activityLog->log_activity(Auth::user()->id,'Update Family','Updated the Family Details', $data['organization']->id);
                return redirect('organization/'.$slug.'/administrator/family/')->with('message', 'Successfully Edited Family Group');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //delete family
    public function family_delete($slug, $id)
    {
        $result = new DataResult();
        $data['organization'] = $this->organization->getUrl($slug);

        if(!empty(Auth::guard('api')->user()))
        {
             $this->family_group->softDelete($id);
             $result->message = Constants::SUCESSSFULLY_DELETED_FAMILY_GROUP;
              
             return json_encode($result);
        }


        if(Auth::user()->hasRole('member')){ 
             $this->family_group->softDelete($id);
             
            return back()->with('message', 'Successfully Deleted Family Group');  
        }

        if(Gate::denies('delete_family'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $this->family_group->softDelete($id);
                $this->activityLog->log_activity(Auth::user()->id,'Delete Family','Deleted the Family.', $data['organization']->id);
                return back()->with('message', 'Successfully Deleted Family Group');
            }else{
                return view('errors.errorpage');
            }
        }
    }
    //view family profile
    public function family_view(Request $request, $slug, $id)
    {
        $data['slug']  = $slug;
        $data['organization'] = $this->organization->getUrl($slug);
        
        if(Gate::denies('view_specific_family_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data = $this->family_group->edit($id);
                $data['id'] = $id;
                $data['family_members'] = $this->family_member->getFamilyMember($request, $id);

                $data['slug']  = $slug;
                $data['organization'] = $this->organization->getUrl($slug);

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);

                return view('cocard-church.church.admin.family.view',$data);
            }else{
                return view('errors.errorpage'); 
            }            
        }
    }

    //list of family members for each family based on id (ADMIN DASHBOARD)
    public function family_member_index(Request $request, $slug, $id)
    {
        #dd($slug);

        $data = $this->family_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['family_id'] = $id;
        $data['slug']  = $slug;
        $data['family_members'] = $this->family_member->getFamilyMember($request, $id);
        
        if(Gate::denies('view_family_members'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                return view('cocard-church.church.admin.familymember.index',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    //list of family members for ech family based on id (USER DASHBOARD)
    public function user_family_member_index(Request $request, $slug, $id)
    {
        $data = $this->family_group->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['family_id'] = $id;
        $data['phone'] = old('phone');
        $data['email'] = old('email');
        $data['password'] = old('password');
        $data['add_member'] = "yes";
        $data['slug']  = $slug;
        $data['family_members'] = $this->family_member->getFamilyMember($request, $id);
        $data['family_groups'] = $this->family_group->findFamily($id);
        
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        // $data["birthdate"] = date("m/d/Y", strtotime($data["birthdate"]));
        $data = array_merge($data,$theme);
        
        if(Gate::denies('view_own_family') && empty(Auth::guard('api')->user()))
        {
            return $this->AuthenticationError($request);
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Api::getUserByMiddleware()->organization_id,$data['organization']->id);
            if($auth == true)
            {
                $data[Constants::MODULE] = Constants::FAMILY_MEMBER_LIST;
                return Api::displayData($data,'cocard-church.user.familymember.index',$request);
            }
            else
            {
                return view('errors.errorpage');
            }  
        }
    }
    //family member create form (ADMIN DASHBOARD)
    public function family_member_create(Request $request,$slug, $id)
    {
        $data                 = $this->family_member->createfm($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['users'] =  $this->user->getUsersMembers($request,$slug,$data['organization']->id);


        $data['family_id'] = $id;

        $data['count'] = 0;
        $data["birthdate"] = date("m/d/Y", strtotime($data["birthdate"]));
        $data['search'] = $request->input('search');
       
        if(Gate::denies('add_user_family_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                return view('cocard-church.church.admin.familymember.form',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    //family member create form (USER DASHBOARD)
    public function user_family_member_create($slug, $id)
    {
        $data                 = $this->family_member->createfm($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['email']        = old('email');
        $data['password']     = old('password');
        $data['phone']        = old('phone');
        $data['cb_num']        = old('cb_num');
        $data['family_id']    = $id;
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);
        
        if(Gate::denies('add_family_members'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                return view('cocard-church.user.familymember.form',$data);
            }else{
                return view('errors.errorpage');
            } 
        }
    }
    //add/store family member to database
    public function family_member_store(Request $request,$id)
    {
        $slug                 = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);
        
        if(!empty(Auth::guard('api')->user()))
        {
            $result = new DataResult();

             if($request->cb_num == 1){
                $results = $this->family_member->saveUser($request);
             }else{    
                $results = $this->family_member->save($request);
             }

             if($results['status'] == false)
             {
                $result->error = true;
                $result->tag = $results[Constants::ERROR_CODE];

                if($results[Constants::ERROR_CODE] == Constants::ERROR_CODE_FAMILY_MEMBER_EXIST)
                {
                    $result->message = $results['results'];
                    $result->data = $results['data'];
                }
                else
                {
                    $result->message = $results['results']->errors()->all();
                }
                
             }
             else
             {
                $result->message  = Constants::SUCCESSFULLY_ADD_FAMILY_MEMBER;
             }

             return json_encode($result->Iterate());
        }

        if(Auth::user()->hasRole('member')){
            if($request->cb_num == 1){
                $results = $this->family_member->saveUser($request);
            }else{    
                $results = $this->family_member->save($request);
            }
        }else{
            $results = $this->family_member->saveArray($request);
        }

           if($results['status'] == false)
            {
                return back()->withErrors($results['results'])->withInput();
            }else{

                if(Auth::user()->hasRole('member')){
                    return redirect('/organization/'.$slug.'/user/family/'.$id)->with('message', 'Successfully Added Family Member/s');
                }else{
                    $this->activityLog->log_activity(Auth::user()->id,'Added Family Member','Added the Family Member/s to a Family', $data['organization']->id);
                    return redirect('/organization/'.$slug.'/administrator/family/'.$id)->with('message', 'Successfully Added Family Member/s');
                }
                
            }
    }
    //edit family member form (ADMIN DASHBOARD)
    public function family_member_edit($slug, $id)
    {
        $data                 = $this->family_member->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['count']        = 0;
        
        if(Gate::denies('edit_user_family_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                return view('cocard-church.church.admin.familymember.form',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    //edit family member form (USER DASHBOARD)
    public function user_family_member_edit(Request $request, $slug, $id)
    {

        $data                 = $this->family_member->edit($id);
        $data['family_member']= $this->family_member->edit($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['email']        = '';
        $data['password']     = '';
        $data['phone']        = '';
        $data['email']        = '';
        $data['cb_num']        = '';
        $data[Constants::MODULE] = Constants::INITIALIZE_FAMILY_MEMBER;

        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        if(Gate::denies('edit_family_members') && empty(Auth::guard('api')->user()))
        {
            return $this->AuthenticationError($request);
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Api::getUserByMiddleware()->organization_id,$data['organization']->id);
            if($auth == true){
                return Api::displayData($data,'cocard-church.user.familymember.form',$request);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    //update family member info
    public function family_member_update(Request $request, $id)
    {
        $result = new DataResult();

        //return json_encode(Api::getUserByMiddleware());

        $slug                 = $request->slug;
        $id                   = $id;
        $data['organization'] = $this->organization->getUrl($slug);


        if(Api::getUserByMiddleware() == null)
        {
             return $this->AuthenticationError($request);
        }

        if($request->has('id'))
        {
            $id = $request->id;
        }

        if($request->add_member == "yes"){
            $results = $this->family_member->saveMemberToUser($request, $id);
        }else{
            $results = $this->family_member->updates($request, $id);
        }
        
        if($results['status'] == false)
        {
            #return back()->withErrors($results['results'])->withInput();
            if($request->has('type')== Constants::JSON){
                $result->tag = $results['status'];
                $result->message = $results['results']->errors()->all();
                $result->error = true;

               return response()->json($result->Iterate());
            }
            else{
                return back()->withErrors($results['results'])->withInput();
            }
        }else{
            if($request->has('type')== Constants::JSON){
                if($request->add_member == "yes")
                {
                    $result->message = Constants::SUCCESSFULLY_ADD_MEMBER;
                }
                else
                {
                    $result->message = Constants::SUCCESSFULLY_UPDATE_MEMBER;
                }
                return json_encode($result);
            }
            else{
                if($request->add_member == "yes"){
                    return back()->with('message', 'Successfully Added to Members Directory');
                }else{
                    if(Auth::user()->hasRole('member')){
                        return redirect('/organization/'.$slug.'/user/family/'.$request->family_id)->with('message', 'Successfully Edited Family Member');
                    }else{
                        $this->activityLog->log_activity(Auth::user()->id,'Update Family Member','Updated the Family Member Details', $data['organization']->id);
                        return redirect('/organization/'.$slug.'/administrator/family/'.$request->family_id)->with('message', 'Successfully Edited Family Member');
                    }
                }
                

            }
        }
    }
    //delete family member in each family
    public function family_member_delete($slug, $id)
    {
        #dd($slug);
        $data['organization'] = $this->organization->getUrl(explode("/",$slug)[0]);
        $this->family_member->softDelete($id);
        
        if(!empty(Auth::guard('api')->user()))
        {
            $result = new DataResult();
            $result->message = Constants::SUCCESSFULLY_DELETED_MEMBER;

            return json_encode($result);
        }

        if(Gate::denies('delete_family_members'))
        {
            return view('errors.errorpage');
        }
        else
        {
            if(Auth::user()->hasRole('member')){
                return back()->with('message', 'Successfully Deleted');
            }else{

                $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

                if($auth == true){
                    $this->activityLog->log_activity(Auth::user()->id,'Deleted Family Member','Deleted the Family Member', $data['organization']->id);
                    return back()->with('message', 'Successfully Deleted');
                }else{
                    return view('errors.errorpage');
                }
                
            }
        }
    }
    //view family member profile
    public function family_member_view(Request $request, $slug, $id)
    {

        $data['slug']  = $slug;
        $data['organization'] = $this->organization->getUrl($slug);
       
        if(Gate::denies('view_specific_family_member'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data = $this->family_member->edit($id);
                $data['id'] = $id;

                $data['slug']  = $slug;
                $data['organization'] = $this->organization->getUrl($slug);

                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);

                return view('cocard-church.church.admin.familymember.view',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
    }
    //assign member to a certain family
    public function assignFamily(Request $request, $id)
    {
        $slug                 = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);
        $results              = $this->family_member->assignFamily($request, $id);

        if($results['status'] == false)
        {
            return back()->with('message', 'Member is already in that Family');
        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Assign Member to Family','Added Member to Family', $data['organization']->id);
            return back()->with('message', 'Successfully Added Member to Family');
        }
    }
    //AJAX search and auto complete
    public function autoComplete(Request $request, $slug){

        $data = $this->family_member->autoComplete($request, $slug);
        return $data;
    }
    //adding multiple family members
    public function addFamilyMember(Request $request, $slug, $id){
        $data                 = $this->family_member->createfm($id);
        $data['organization'] = $this->organization->getUrl($slug);
        $data['slug']         = $data['organization']->url;
        $data['users'] =  $this->user->getUsersMembers($request,$slug,$data['organization']->id);
        $data['family_id']           = $id;
        #dd($request);
        $data['count'] = $request->count + 1;

        $data['search'] = $request->input('search');
        return view('cocard-church.church.admin.familymember.form-template',$data);
    }

    //user dashboard theme
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
