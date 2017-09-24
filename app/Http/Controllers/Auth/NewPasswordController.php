<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;
use Hash;
use Mail;
use Carbon\Carbon;
use App\User;
use App\Organization;
use Acme\Common\DataResult as DataResult;

class NewPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    //get email for sending reset link for superadmin
	public function email (Request $request) {

		// return 'Email LInk form';
		// return view('auth.resetpassword.email');
		return view('auth.resetpassword.superadmin.email');
	}
	 //superadmin reset password link
	public function resetLink (Request $request, $token) {

		// return 'Email LInk form';
		// return view('auth.resetpassword.email');
		$data['token'] = $token;
		return view('auth.resetpassword.superadmin.reset', $data);
	}
	//send email reset link
	public function sendEmail (Request $request) {
		// Send Email for reset link

		// $user = User::where ('email', $request->email)-first();
	 //    if ( !$user ) return redirect()->back()->withErrors(['error' => '404']);
	    $result = new DataResult();

		$messages   = ['required' 	=> 'The :attribute is required',
                        'email' 	=> 'The :attribte entered must be a valid email address.'];

		$validator  = Validator::make($request->toArray(), [
		                'email'             	=> 'required|email'
		            ], $messages);
	
        if($validator->fails()){
        	// dd($validator);
			if($request->has('json'))
			{
				$result->message = $validator->errors()->all();
				$result->error = true;

				return json_encode($result);
			}
			else
			{
				return back()->withErrors($validator)->withInput();
			}
        }
        //user exists
		$user = User::where('email', $request->email)
		            ->where('organization_id', $request->id)
		            ->where('status', 'Active')->first();
		//if user does not exist
		if(!$user){
			if($request->has('json'))
			{
				$result->message = "Invalid User.";
				$result->error = true;

				return json_encode($result);
			}
			else
			{
				return back()->with(['status' => 'Invalid User.']);
			}
		//if user exists
		}else{

			//insert data in password_resets table (includes email, token, org_id, created_at for reset lik reference) 
		    DB::table('password_resets')->insert([
		        'email' => $request->email,
		        'token' => str_random(60), //change 60 to any length you want
		        'created_at' => Carbon::now(),
		        'organization_id' => $user->organization_id,
		    ]);

		   $tokenData = DB::table('password_resets')->where('email', $request->email)->where('organization_id', $request->id)->first();

		   $token = $tokenData->token;
		   $email = $request->email; 

		   //if superadmin
		   if($user->organization_id == 0){
		   		//send reset link to email
		   		Mail::send('auth.resetpassword.superadmin.mail',['token' => $token, 'email'=>$email], function ($m) use ($token, $email) {
	                    $m->to($email)->subject('iSteward reset password link.');
	            });
		   //if user from specific org
		   }else{

			   $organization = DB::table('organizations')->where('url', $request->slug)->first();
			   $slug = $request->slug;
			   //send reset link to email
			   Mail::send('auth.resetpassword.mail',['token' => $token, 'email'=>$email, 'slug' => $slug, 'organization'=>$organization], function ($m) use ($token, $email, $slug, $organization) {
	                    $m->to($email)->subject($organization->name.' reset password link.');
	            });
			}

			if($request->has('json'))
			{
				$result->message = "Reset Password Link Sent.";

				return json_encode($result);
			}
			else
			{
				return back()->with(['status' => 'Reset Password Link Sent.']);
			}
		}	
	}
	public function resetPassword (Request $request, $token) {
		// Update the new password
		// return $request->all();

		 //some validation
		$messages   = ['required' 	=> 'The :attribute is required',
                        'email' 	=> 'The :attribte entered must be a valid email address.',
                        'same'     	=> 'Password Mismatch.'];

		$validator  = Validator::make($request->toArray(), [
		                'email'             	=> 'required|email',
		                'password'          	=> 'required',
		                'password_confirmation' => 'required|same:password',
		            ], $messages);
	
        if($validator->fails()){
        	// dd($validator);
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $slug = $request->slug;
        $organization_id = $request->id;
        $password = $request->password;
	    
	    $tokenData = DB::table('password_resets')->where('token', $token)->first();
	    //if token is deleted means it was already been used.
	    if(!$tokenData){
	    	return back()->with(['status' => 'Token expired.']);
	    }
	    //if there's no email same as request email found in password_resets table
	    if($email != $tokenData->email){
	    	return back()->with(['status' => 'Invalid user email.']);
	    }

	    $user = User::where('email', $tokenData->email)->where('organization_id', $organization_id)->where('status', 'Active')->first();
	    //if user does not exist
	    if(!$user){
	     	return redirect('organization/'.$slug); 
	    }
	    //update user password and delete data in password reset to expire token
	    DB::table('users')->where('id', $user->id)->update([ 'password' => bcrypt($password)]); 
	    DB::table('password_resets')->where('token', $token)->delete();

	    //logs in user
	    Auth::login($user);

	    //redirects if already logged in
	    if(Auth::user()->hasRole('member')){
	    	return redirect('organization/'.$slug.'/user/dashboard');
	    }elseif(Auth::user()->hasRole('superadmin')){
	    	return redirect('/dashboard');
	    }else{
	    	return redirect('organization/'.$slug.'/administrator/dashboard');
	    }
	}

}
