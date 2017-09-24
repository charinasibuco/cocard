<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\ActivityLogRepository;
use Acme\Repositories\OrganizationRepository;
use App\Http\Requests;
use App\ActivityLog;
use Carbon\Carbon;
use Auth;
use Gate;
use App;

class ActivityLogController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog, OrganizationRepository $organization){
		$this->middleware('auth');
		$this->activityLog = $activityLog;
		$this->organization = $organization;
		$this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
	}
    //Superadmin list of activity log
    public function index(Request $request){
    	if(Gate::denies('view_superadmin_log')){
	        return view('errors.errorpage');
	    }
	    else
	    {
	    	$data['activity_log'] = $this->activityLog->getSuperadminLog($request);
	    	$data['search'] = $request->input('search');
	    	return view('cocard-church.superadmin.activitylog',$data);
	    }
    }
    //Per Admin of Organization list of logs
    public function adminlogs(Request $request,$slug){
       # dd(Auth::user()->created_at->timezone);
        $timezone= Auth::user()->created_at->timezone;
        #dd($request);
    	$data['organization'] = $this->organization->getUrl($slug);
    	$data['slug']         = $data['organization']->url;
    	

        if(Gate::denies('view_admin_log')){
            return view('errors.errorpage');
        }else{
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);
            if($auth == true){
                $data['activity_log'] = $this->activityLog->getAdminLog($request, $slug);
                foreach($data['activity_log'] as $q){
                    $data['timezone'] = Carbon::createFromFormat('Y-m-d H:i:s', $q->created_at, 'Asia/Manila');
                    #dd($data['timezone']);
                }
                $data['search'] = $request->input('search');
                return view('cocard-church.church.admin.logs',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }

    public function adminlogspost(Request $request,$slug)
   {
       $data = $request->all(); // This will get all the request data.

       dd($data); // This will dump and die
   }

}
