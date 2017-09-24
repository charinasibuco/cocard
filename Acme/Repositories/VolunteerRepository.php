<?php

namespace Acme\Repositories;
use Acme\Helper\AesTrait;
use DB;
use Auth;
use Acme\Helper\Api;
use App\User;
use Mail;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class VolunteerRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

    use Pagination;
    protected $listener;

    use AesTrait;

    public function model(){
        return 'App\Volunteer';
    }
/**/
    public function setListener($listener){
        $this->listener = $listener;
    }

    public function findVolunteer($id){
        return $this->model->find($id);
    }
    public function findVolunteerPerGroup($id){
        return $this->model->where('volunteer_group_id',$id)->get();
    }
    public function getVolunteers($request){
        $this->SetPage($request);

        $query = $this->model->where('status','Active');
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }
        $order_by   = $this->SortBy;
        $sort       = $this->SortOrder;

        return $query->select('volunteers.*')
            ->orderBy('volunteers.'.$order_by, $sort)
             ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);

    }
      public function getVolunteersAll($request){
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'event_id';

        return $query->select('volunteers.*')
            ->orderBy('volunteers.'.$order_by, $sort)
            ->paginate();
    }
    public function getPerUserVolunteers($request, $userid){
        $query = $this->model->where('status','Active')->where('user_id', $userid);
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'event_id';

        return $query->select('volunteers.*')
            ->orderBy('volunteers.'.$order_by, $sort)
            ->paginate();
    }

    public function getUserVolunteer($request){

        // $query = $this->model
        //                     ->join('volunteer_groups', 'volunteer_groups.id', '=', 'volunteers.volunteer_group_id')
        //                     ->join('event', 'event.id', '=', 'volunteer_groups.event_id')
        //                     ->where('volunteers.user_id', '=', Api::getUserByMiddleware()->id);

        // if ($request->has(Constants::KEYWORD)) {
        //     $search = trim($request->input('search'));
        //     $query = $query->where(function ($query) use ($search) {
        //     $query->select('event.name')->from('event')->where('event.name', 'LIKE', '%' . $search . '%')
        //             ->orderBy('event.created_at', 'desc')
        //             ->paginate(7);
        //     });
        // }
        // $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'name';
        // $sort       = ($request->input('sort'))? $request->input('sort') : 'name';

        // return $query->select('*')
        //     ->orderBy('volunteers.'.$order_by, $sort)
        //     ->paginate(7);
        $this->SetPage($request);

        $query = $this->model->where('user_id', '=',  Api::getUserByMiddleware()->id)
                ->leftJoin('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
                ->leftJoin('event','event.id','=','volunteer_groups.event_id');
        if ($request->has(Constants::KEYWORD)) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->select('event.name')->from('event')->where('event.name', 'LIKE', '%' . $search . '%')
                    ->orderBy('event.created_at', 'desc')
                    ->paginate(7);
            });
            //->orWhere('participants.qty', 'LIKE', '%' . $search . '%');
        }

        $order_by   =  $this->SortBy;
        $sort       =  $this->SortOrder;
        //dd($query);
        return $query->select('event.name as event_name','volunteer_groups.type as type', 
            'volunteer_groups.start_date as start_date','volunteer_groups.end_date as end_date','volunteers.volunteer_group_status as status')
            ->orderBy('volunteers.created_at', 'desc')
            ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);
    }
    public function getUserVolunteerAll($request){
        $query = $this->model->where('user_id', '=', Api::getUserByMiddleware()->id)
                            ->join('volunteer_groups', 'volunteer_groups.id', '=', 'volunteers.volunteer_group_id')
                            ->join('event', 'event.id', '=', 'volunteer_groups.event_id');
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'name';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'name';

        return $query->select('volunteers.*', 'event.*', 'volunteer_groups.*')
            ->orderBy('volunteers.'.$order_by, $sort)
            ->get();
    }
    /*public function getUsers($request)
    {
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('nickname', 'LIKE', '%' . $search . '%')
                    ->orWhere('gender', 'LIKE', '%' . $search . '%')
                    ->orWhere('position', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('users.*')
            ->orderBy('users.'.$order_by, $sort)
            ->paginate();
    }*/
    /**
     * @param $id -> Event ID
     */
   public function apply($input){
       DB::statement('SET FOREIGN_KEY_CHECKS=0;');
       $this->model->create($input);
       DB::statement('SET FOREIGN_KEY_CHECKS=1;');
   }

    public function cancel($id){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');;
        $this->model->find($id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function create(){

    }
    public function edit($id){

    }
       public function update(array $input,$id)
    {
        #dd($input);
        $this->model->where('id',$id)->update($input);
        //DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
     public function updateStatus($id)
    {
        $volunteer = $this->model->where('id',$id)->first();

        $volunteer->status  = 'InActive';
        $volunteer->save();
    }
      public function updateVolunteerStatus($status,$id,$request)
    {
        #dd($status);
        $volunteer = $this->model->where('id',$id)->update([
                                    'volunteer_group_status' => $status
                                ]);
        $volunteer = $this->model->where('id',$id)->first();
        // $volunteer_group = $this->model->where('id',$id)->first();
        Mail::send('cocard-church.email.approved_volunteers',['volunteer' => $volunteer,'status' => $status, 'request' => $request], function ($m) use ($volunteer,$status) {
                $m->to(trim($volunteer->email), trim($volunteer->email))->subject('Volunteering '. $status .' for '.$volunteer->volunteer_group->type);
        });
        //return back()->with('message', 'Message Successfully Sent!');
    }

    public function checkNumberApproveVolunteers($id){
        $vg = $this->model->where('id',$id)->first();
        $vg_status = $this->model->where('volunteer_group_id',$vg->volunteer_group_id)->where('volunteer_group_status','Approved')->get();
        return $vg_status->count();
    }

    public function checkVolunteerGroupNeeded($id){
        $vg = $this->model->where('id',$id)->first();
        $volunteer_group = $this->model->where('volunteer_group_id',$vg->volunteer_group_id)->first();
        return $volunteer_group->volunteer_group->volunteers_needed;
    }
    public function getVolunteersApprovedCountToDisabled($id){
        $vg = $this->model->where('id',$id)->first()->getVolunteersApprovedCountToDisabledAttribute();
        return $vg;
    }
    public function allVolunteerPending($type){
       return $this->model->allVolunteerPending($type);
    }

    public function getvolunteer(){
        return $this->model;
    }
}
