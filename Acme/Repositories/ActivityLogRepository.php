<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Acme\Helper\AesTrait;
use App\ActivityLog;
use App\Organization;
use Illuminate\Support\Facades\Validator;

class ActivityLogRepository extends Repository{

    const LIMIT                 = 8;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener;

    use AesTrait;

    public function model(){
        return 'App\ActivityLog';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function setDate($date){
        return date('Y-m-d', strtotime($date));
    }

    public function getActivityLog($request,$id)
    {
        #dd($id);
        $query = $this->model->where('user_id',$id);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('activity', 'LIKE', '%' . $search . '%')
                           ->orWhere('details', 'LIKE', '%' . $search . '%')
                           ->paginate(self::LIMIT);
                    });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('activity_log.*')
                     ->orderBy('activity_log.'.$order_by, $sort)
                     ->paginate(self::LIMIT);
    }

    public function getSuperadminLog($request)
    {
        #dd($id);
        $query = $this->model->where('org_id', '0');
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('activity', 'LIKE', '%' . $search . '%')
                           ->orWhere('details', 'LIKE', '%' . $search . '%')
                           ->paginate(self::LIMIT);
                    });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('activity_log.*')
                     ->orderBy('activity_log.'.$order_by, $sort)
                     ->paginate(self::LIMIT);
    }

    public function getAdminLog($request, $slug)
    {
        $organization = Organization::where('url', $slug)->first();
        $query = $this->model->where('org_id', $organization->id);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                     $query->where('activity', 'LIKE', '%' . $search . '%')
                           ->orWhere('details', 'LIKE', '%' . $search . '%')
                           ->paginate(self::LIMIT);
                    });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('activity_log.*')
                     ->orderBy('activity_log.'.$order_by, $sort)
                     ->paginate(self::LIMIT);
    }

    public function create(){

        
    }

    public function superadmin_log_activity($id,$title,$details, $org_id){
            $activity = new ActivityLog;
            $activity->user_id                              = $id;
            $activity->activity                             = $title;
            $activity->details                              = $details;
            $activity->org_id                               = $org_id;
            $activity->save();
        
    }

    public function log_activity($id,$title,$details, $org_id){
            $activity = new ActivityLog;
            $activity->user_id                              = $id;
            $activity->activity                             = $title;
            $activity->details                              = $details;
            $activity->org_id                               = $org_id;
            $activity->save();
        
    }

    public function edit($id){
        
    }
    public function AuthGate($userorg,$org){
        if($userorg == $org || $userorg == 0){            
           return true;
        }
        else
        {
            return false;   
        }
    }
}