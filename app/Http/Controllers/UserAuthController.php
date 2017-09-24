<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\UserRepository;
use Acme\Repositories\ActivityLogRepository;
use Acme\Repositories\OrganizationRepository;
use Acme\Repositories\RoleRepository as Role;
use Acme\Common\DataResult;
use Acme\Common\Constants as constants;
use App\Http\Requests;
use Auth;
use Gate;
use Acme\Helper\Api;
class UserAuthController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog,UserRepository $user, OrganizationRepository $organization, Role $role){
        $this->user = $user;
        $this->role = $role;
		$this->activityLog = $activityLog;
		$this->auth = Auth::user();
		$this->organization = $organization;
	}

	//REGISTER
	public function create(Request $request, $slug = null){
        
    	$data = $this->user->create();
        $data['organization']   = $this->organization->getUrl($slug);
 		return view('auth.user-register', $data);
    }

	public function save(Request $request){
        //dd('asd');
        $result = new DataResult();

        $slug       = $request->slug;
        $id         = $request->id;

        $organization = $this->organization->getUrl($slug);
        $request["organization_id"] = $organization->id;

        // dd($request->all());
    	$results    = $this->user->save($request, $id);
        $email      = trim($request->email);
        $password   = trim($request->password);

		if($results['status'] == true)
		{
            if($request->type == "json"){
                $result->message = constants::SUCCESSFULLY_REGISTERED;
                return json_encode($result);
            }

            if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'Active', 'organization_id' => $request->organization_id]))
            {
                return redirect('organization/'.$slug.'/user/dashboard');
            }

		}else{
            if($request->type == "json"){
                    $result->message = $results['results']->errors()->all();
                    $result->error = true;
                     return json_encode($result);
            }
            return redirect('organization/'.$slug.'/register')->withErrors($results['results'])->withInput();
        }
		// dd($request->all());
	}
    public function save_admin(Request $request){
        #dd($request);
        $slug       = $request->slug;

        $id         = $request->id;
        $results    = $this->user->save($request, $id);

        if($results['status'] == false)
         {
             return back()->withErrors($results['results'])->withInput();

         }else{
                $this->activityLog->log_activity(Auth::user()->id,'New Member','Created new member', $request->organization_id);
             return redirect('organization/'.$slug.'/administrator/members')->with('message', 'Successfully Added Member');
         }
    }
	//LOG-IN
	public function userLogin(Request $request, $slug){
        if(Auth::guest()){
            $data['organization'] 	= $this->organization->getUrl($slug);
            $data['action']			= route('post_user_login');
            $data['slug']           =  $data['organization']->url;
            $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
            $data = array_merge($data,$theme);
            return view('auth.user-login', $data);
        
        }else{
            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/dashboard');
            }
            elseif(Auth::user()->hasRole('superadmin')){
                return redirect('dashboard');
            }
            else{
                return redirect('organization/'.$slug.'/administrator/dashboard');
            }
        }
    }
    //switch account
    public function multipleLogin(Request $request){

        $slug       = $request->slug;
        $id         = $request->id;

        $this->user->switchAccount($request);

            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/dashboard');
            }
            else{
                return redirect('organization/'.$slug.'/administrator/dashboard');
            }
    }

    public function postLogin(Request $request){

        $result = new DataResult();

        $emailphone = trim($request->emailphone);
        $password   = trim($request->password);
        $slug       = $request->slug;
        $id         = $request->id;
        $hasJson = $request->has('json');

        // if (Auth::attempt(['email' => $emailphone, 'password' => $password, 'organization_id' => $id, 'status' => 'Active']) && Auth::user()->hasRole('member') || Auth::attempt(['phone' => $emailphone, 'password' => $password, 'organization_id' => $id, 'status' => 'Active']))
        // {
        if (Auth::attempt(['email' => $emailphone, 'password' => $password, 'organization_id' => $id, 'status' => 'Active']) || Auth::attempt(['phone' => $emailphone, 'password' => $password, 'organization_id' => $id, 'status' => 'Active']))
        {
            $user_id = Api::getUserByMiddleware()->id;
            $this->user->updateToken($user_id);
            $user = Api::getUserToken();

            if($hasJson)
            {
                 $assigned_user_roles = $this->role->getMembersRole($user_id);
                 if(count($assigned_user_roles) != 0)
                 {
                     $result->message = constants::LOGIN_SUCCESS;
                     $result->data['user'] = $this->user->findUser($user_id);
                     $result->data['roles'] = $assigned_user_roles;
                 }
                 else
                 {
                     $result->message = constants::LOGIN_FAILED;
                     $result->error = true;
                 }
            
                 return response()->json($result);
            }

            if(Auth::user()->hasRole('member')){
                return redirect('organization/'.$slug.'/user/dashboard');
            }else{
                return redirect('organization/'.$slug.'/administrator/dashboard');
            }

        }else{
            if($hasJson){
                 $result->message = constants::LOGIN_FAILED;
                 $result->error = true;
                 return response()->json($result);
            }
           return redirect('organization/'.$slug. '/login')->with('message', 'Invalid Username and Password');
        }
    }
    public function superadminLogin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'Active', 'organization_id' => 0])){
            return redirect('/dashboard');
        }else{
             return redirect('/login')->with('error', 'Invalid Username or Password');
        }

    }

    public function logout($slug){
        Auth::logout();
        return redirect('organization/'.$slug. '/');
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
    //sending reset password link in email for organization's member
    public function requestResetLink(Request $request, $slug){
        $data['organization']   = $this->organization->getUrl($slug);
        $data['slug']           =  $data['organization']->url;
        // $data['action']         = route('request-resetpassword-link', $slug);
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        return view('auth.resetpassword.email', $data);

    }
    //reset password link for organization's member
    public function resetPassword(Request $request, $slug, $token){
        $data['organization']   = $this->organization->getUrl($slug);
        $data['slug']           =  $data['organization']->url;
        $data['token']          = $token;
        // $data['action']         = route('request-resetpassword-link', $slug);
        $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
        $data = array_merge($data,$theme);

        return view('auth.resetpassword.reset', $data);

    }
}
