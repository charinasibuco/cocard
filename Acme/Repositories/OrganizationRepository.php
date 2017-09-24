<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Organization;
use App\User;
use App\UserRole;
use App\AssignedUserRole;
use App\Event;
use App\Family;
use App\Donation;
use App\Transaction;
use App\Participant;
use App\Volunteer;
use App\ActivityLog;
use App\PendingOrganizationUser;
use DB;
use Mail;
use Auth;
use Excel;
use PDF;
use App;
use Dompdf\Dompdf;
class OrganizationRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';
    /**/

    protected $listener;

    public function model(){
        return 'App\Organization';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }
    public function getUrl($slug){
        return $this->model->where('url', $slug)->first();
    }

    public function getOrganizationId($organization_id){
        return $this->model->where('id', $organization_id)->first();
    }

    public function findId($id){
        return $this->model->where('id',$id)->first();
    }

    public function findOrganization($id){
        return $this->model->find($id);
    }
    public function getOrganization($request)
    {
        #$query = $this->model->where('status', 'Active');
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('contact_person', 'LIKE', '%' . $search . '%')
                ->orWhere('position', 'LIKE', '%' . $search . '%')
                ->orWhere('contact_number', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('url', 'LIKE', '%' . $search . '%')
                ->orWhere('language', 'LIKE', '%' . $search . '%')
                ->orWhere('status', 'LIKE', '%' . $search . '%')
                ->paginate();
            });
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('organizations.*')
        ->orderBy('organizations.'.$order_by, $sort)
        ->paginate();
    }
    public function getOrgAdmins($id)
    {
        #dd($id);
        $user = new User;
         return $user->where('id',$id)->get();
    }


    public function create(){

        $data['action']                = route('organization_store');
        $data['action_name']           = 'Add';
        $data['name']                  = old('name');
        $data['contact_person']        = old('contact_person');
        $data['position']              = old('position');
        $data['url']                   = old('url');
        $data['contact_number']        = old('contact_number');
        $data['email']                 = old('email');
        $data['logo']                  = old('logo');
        $data['password']              = old('password');
        $data['scheme']                = old('scheme');
        $data['status']                = old('status');
        return $data;
    }

    public function save($request, $id = 0){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $action     = ($id == 0) ? 'organization_store' : 'organization_update';

        $input      = $request->except(['_token','confirm']);

        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'name'              => 'required',
            'contact_person'    => 'required',
            'position'          => 'required',
            'url'               => 'required',
            'contact_number'    => 'required',
            'email'             => 'required',
            'scheme'             => 'required',
            'img'               => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        if($id == 0){
            $this->model->create($input);
            $activity = new ActivityLog;
            $activity->user_id                              = Auth::id();
            $activity->activity                             = "Added Organization";
            $activity->details                              = "Added ".$input['name'];
            $activity->org_id                               = 0;
            $activity->save();
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $this->model->where('id',$id)->update($input);
            $activity = new ActivityLog;
            $activity->user_id                              = Auth::id();
            $activity->activity                             = "Updated Organization";
            $activity->details                              = "Updated ".$input['name'];
            $activity->org_id                               = 0;
            $activity->save();
            #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];

    }

    public function saveOrg($request, $id){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $action     = ($id == 0) ? 'pending_organization_store' : 'pending_organization_update';

        $input      = $request->except(['_token','confirm']);

        $input['url'] = lcfirst(preg_replace('/\s+/', '-',(strtolower($input['url']))));
       # dd($request->password);
        $messages   = [
            'required' => 'The :attribute is required',
            'same'     => 'Password Mismatch!',
            'unique'     => 'The :attribute is already taken. Please use another.'
        ];
        $validator  = Validator::make($input, [
            'name'                  => 'required',
            'contact_person'        => 'required',
            'position'              => 'required',
            'url'                   => 'required|unique:pending_organization_user,url',
            'password'              => 'required',
            'password_confirmation' => 'required|same:password',
            'contact_number'        => 'required|unique:pending_organization_user,contact_number',
            'email'                 => 'required|unique:pending_organization_user,email',
            'logo'                  => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        if($validator->fails()){
            return ['status' => false, 'results' => $validator];
        }
        $pending = new PendingOrganizationUser;

        if($id == 0){
            if($request->hasFile('logo'))
            {
                $imageName = time().'.'.$request->logo->getClientOriginalExtension();
                $request->logo->move(public_path('images'), $imageName);
                $scheme  = implode(",", $input->get('scheme'));
                $pending->create([
                    'name'              => $input['name'],
                    'contact_person'    => $input['contact_person'],
                    'position'          => $input['position'],
                    'contact_number'    => $input['contact_number'],
                    'email'             => $input['email'],
                    'url'               => $input['url'],
                    'scheme'            => $scheme,
                    'logo'              => $imageName,
                    'status'            => 'Active',
                    'password'          => bcrypt($input['password'])
                ]);
            }else{
                $pending->create([
                    'name'              => $input['name'],
                    'contact_person'    => $input['contact_person'],
                    'position'          => $input['position'],
                    'contact_number'    => $input['contact_number'],
                    'email'             => $input['email'],
                    'url'               => $input['url'],
                    'status'            => 'Active',
                    'password'          => bcrypt($input['password'])
                ]);
            }
            $activity = new ActivityLog;
            $activity->user_id                              = Auth::id();
            $activity->activity                             = "Added Organization";
            $activity->details                              = "Added ".$input['name'];
            $activity->org_id                               = 0;
            $activity->save();

        }else{
            if($request->hasFile('logo'))
            {
                $imageName = time().'.'.$request->logo->getClientOriginalExtension();
                $request->logo->move(public_path('images'), $imageName);
                $scheme  = implode(",", $input->get('scheme'));
                $pending->create([
                    'name'              => $input['name'],
                    'contact_person'    => $input['contact_person'],
                    'position'          => $input['position'],
                    'contact_number'    => $input['contact_number'],
                    'email'             => $input['email'],
                    'url'               => $input['url'],
                    'scheme'            => $scheme,
                    'logo'              => $imageName,
                    'status'            => 'Active',
                    'password'          => bcrypt($input['password'])
                ]);
            }else{
                $pending->create([
                    'name'              => $input['name'],
                    'contact_person'    => $input['contact_person'],
                    'position'          => $input['position'],
                    'contact_number'    => $input['contact_number'],
                    'email'             => $input['email'],
                    'url'               => $input['url'],
                    'scheme'            => $scheme,
                    'status'            => 'Active',
                    'password'          => bcrypt($input['password'])
                ]);
            }

            $pending->where('id',$id)->update($input);
            $activity = new ActivityLog;
            $activity->user_id                              = Auth::id();
            $activity->activity                             = "Updated Organization";
            $activity->details                              = "Updated ".$input['name'];
            $activity->org_id                               = 0;
            $activity->save();
        }

        $getPending = $pending->orderBy('id','email')->first();
        $getPending->status  = 'Active';
        $getPending->save();
        #dd($getPending->password);
        $org = new Organization;
        $org->name                                      = $getPending->name;
        $org->contact_person                            = $getPending->contact_person;
        $org->position                                  = $getPending->position;
        $org->url                                       = $getPending->url;
        $org->scheme                                    = $getPending->scheme;
        $org->logo                                      = $getPending->logo;
        $org->contact_number                            = $getPending->contact_number;
        $org->email                                     = $getPending->email;
        $org->pending_organization_user_id              = $getPending->id;
        $org->password                                  = $getPending->password;
        $org->status                                    = $getPending->status;
        $org->save();

        $getOrgID = $org->orderBy('id','email')->first();
        #dd($getOrgID);
        $user = new User;

        $user->organization_id                           = $getOrgID->id;
        $user->first_name                                = $getPending->contact_person;
        $user->email                                     = $getPending->email;
        $user->phone                                     = $getPending->contact_number;
        $user->status                                    = $getPending->status;
        $user->api_token                                 = str_random(60);
        $user->password                                  = $getPending->password;
        $user->save();

        $getUserID = $user->orderBy('id','email')->first();
        #dd($getUserID);
        $updateapi = $user->where('id',$getUserID);
        $updateapi->update(['api_token' => $getUserID->id.'_'.str_random(60)]);

        $assigned_user_role = new AssignedUserRole;
        $assigned_user_role->role_id                    = 2;
        $assigned_user_role->user_id                    = $user->id;
        $assigned_user_role->save();

        $userRole = new UserRole;
        $userRole->role_id                               = '2';
        $userRole->user_id                               = $getUserID->id;
        $userRole->status                                = 'Active';
        $userRole->original_user_id                      = $getUserID->id;
        $userRole->save();

        $activity = new ActivityLog;
        $activity->user_id                              = Auth::id();
        $activity->activity                             = "Approved Pending Organization";
        $activity->details                              = "Approved ".$getPending->name;
        $activity->org_id                               = 0;
        $activity->save();

        return ['status' => true, 'results' => 'Success', 'id' => $org->id ];
    }

    public function edit($id){
        $data['action']         = route('frequency_update', $id);
        $data['action_name']    = 'Edit';
        $data['frequency']      = $this->model->find($id);

        $data['title']          = (is_null(old('title'))?$data['frequency']->title:old('title'));
        $data['description']    = (is_null(old('description'))?$data['frequency']->description:old('description'));
        $data['status']         = (is_null(old('status'))?$data['frequency']->status:old('status'));

        return $data;
    }

    public function settings($id){
        $model                                   = $this->model->find($id);
        $data['action']                          = '';
        $data['action_name']                    = 'Settings';
        $data['organization']                    = $this->model->find($id);
        $data['name']                           = (is_null(old('name'))?$data['organization']->name:old('name'));
        $data['id']                             = $data['organization']->id;
        $data['status']                         = (is_null(old('status'))?$data['organization']->status:old('status'));
        $scheme = [];
        $data['scheme']                         = $scheme;
        $data['logo']                         = (is_null(old('logo'))?$data['organization']->logo:old('logo'));
        $data['banner_image']                 = (is_null(old('banner_image'))?$data['organization']->banner_image:old('banner_image'));
        return $data;
    }

    public function settingsUpdate($request, $id)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $action     = ($id == 0) ? 'settings_update' : 'settings_update';
        $input      = $request->except(['_token','confirm']);
        $messages   = ['image|mimes' => 'should be jpeg,png,jpg,gif,svg!'];

        $validator  = Validator::make($input, [
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ], $messages);

        if($validator->fails()){
            return ['status' => false, 'results' => $validator];
        }

        $org = new Organization;
        $scheme  = implode(",", $request->scheme);

        if($request->hasFile('logo')){

            $imageName = time().'.'.$request->logo->getClientOriginalExtension();
            $request->logo->move(public_path('images'), $imageName);

            $org->where('id',$id)->update([
                'logo' => $imageName,
            ]);
        }

        if($request->hasFile('banner_image')){

            $imageNameBanner = time().'.'.$request->banner_image->getClientOriginalExtension();
            $request->banner_image->move(public_path('images'), $imageNameBanner);

            $org->where('id',$id)->update([
                'banner_image' => $imageNameBanner,
            ]);
        }

        $org->where('id',$id)->update([
            'scheme' => $scheme,
            'status' => 'Active',
            'nmi_user' => $request->nmi_user,
            'nmi_pass' => $request->nmi_pass,
        ]);

        #dd($getOrgID);

        return ['status' => true, 'results' => 'Success', 'id' => $org->id ];
    }

    public function show($id){
        return $this->model->find($id);
    }

    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }

    public function sendEmailNotification($request, $id)
    {
        $organization = $this->model->where('id',$id)->first();
        // dd($organization);
        Mail::send('cocard-church.email.notification',['organization' => $organization], function ($m) use ($organization) {
            // $m->to($organization->email, $organization->contact_person)->subject('Successfully Registered!');
            $m->to('charina10181990@gmail.com', 'charina10181990')->subject('Successfully Registered!');
        });
    }

    public function restoreDefault($id){
        $org = new Organization;

        $org->where('id',$id)->update([
            'logo'              => '',
            'scheme'            => '',
            'banner_image'      => '',
            'status'            => 'Active',
        ]);

        return ['status' => true, 'results' => 'Success', 'id' => $org->id ];
    }
    public function getActive($id)
    {

       # return $this->model->where('status','Active')->get();
        return $this->model->where('pending_organization_user_id',$id)->get();
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('organizations.*')
        ->orderBy('organizations.'.$order_by, $sort)
        ->paginate();
    }

    ////////EXCEL EXPORTS
    public function getMemberExcelExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        if($end_date =='1970-01-01'){
            $users =  \DB::table('users')
            ->where('organization_id', $orgid)
            ->where('created_at','>=',$start_date)
            ->where('status','Active')
            ->orderBy('first_name','ASC')
            ->get();
        }elseif($start_date =='1970-01-01'){
             $users =  \DB::table('users')
            ->where('organization_id', $orgid)
            ->where('status','Active')
            ->orderBy('first_name','ASC')
            ->get();
        }else{
            $end_date = $end_date.' 23:59:59.999';
            $users =  \DB::table('users')
            ->where('organization_id', $orgid)
            ->where('created_at','>=',$start_date)
            ->where('created_at','<=',$end_date)
            ->where('status','Active')
            ->orderBy('first_name','ASC')
            ->get();
        }
        if($users == null){
            return [];
        }
        Excel::create('List of Members', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$users)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('List of Members');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('List of Members for Church ');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$users){


                #dd($start_date);
                foreach($users as $list) {
                    $data[] = array(
                        $list->first_name.' '.$list->middle_name.' '.$list->last_name,
                        $list->email,
                        $list->phone,
                        Carbon::parse($list->birthdate)->format('n/j/Y'),
                        $list->gender,
                        $list->address.' '.$list->city.' ' .$list->state,
                    );
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Name', 'Email', 'Phone','Birthdate','Gender','Address');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);

    }
    public function getSummaryOfEventsExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        if($end_date =='1970-01-01'){
             $event = \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->where('event.status','Active')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }elseif($start_date =='1970-01-01'){
             $event = \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->where('event.status','Active')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $event= \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->where('event.status','Active')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }
        if($event == null){
            return [];
        }
        Excel::create('Summary of Events', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$event)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Summary of Events');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('Summary of Events ');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$event){

                 
                #dd($start_date);
                foreach($event as $list) {
                    if($list->pending == 0 ){
                        $list->pending = '0';
                    }
                    if($list->recurring == 1){
                        $list->recurring = 'R';
                    }else{
                        $list->recurring = '';
                    }
                    $list->start_date = Carbon::parse($list->start_date)->format('n/j/y');
                    $data[] = array(
                        $list->start_date,
                        $list->recurring,
                        $list->event_name,
                        $list->capacity,
                        $list->pending,
                        
                    );
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Start Date','Recurring', 'Event Name','Event Capacity', '# Signed Up');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);

    }
    
    public function getSummaryOfVolunteersExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        if($end_date =='1970-01-01'){
             $volunteers = \DB::table('volunteers')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }elseif($start_date =='1970-01-01'){
             $volunteers = \DB::table('volunteers')
           ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $volunteers= \DB::table('volunteers')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }
        if($volunteers == null){
            return [];
        }
        Excel::create('Summary of Event Volunteers', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$volunteers)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Summary of Event Volunteers');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('Summary of Event Volunteers ');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$volunteers){

                 
                #dd($start_date);
                foreach($volunteers as $list) {
                    if($list->pending == 0 ){
                        $list->pending = '0';
                    }
                    $list->start_date = Carbon::parse($list->start_date)->format('m/j/y');
                    $data[] = array(
                        $list->start_date,
                        $list->event_name,
                        $list->volunteer_group_name,
                        $list->volunteers_needed,
                        $list->total,
                    );
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Start Date', 'Event Name', 'Volunteer Group','No. of Volunteers Needed','No of Volunters Signed up');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);

    }
    public function getEventParticipantsListExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        $paricipants = new Participant;
        if($end_date ==''){
            $participants =  \DB::table('participants')
            ->join('event','event.id','=','participants.event_id')
            ->where('event.organization_id','=', $orgid)
            ->where('participants.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
            ->get();
        }else{
            $end_date = $end_date.' 23:59:59.999';
            $participants =  \DB::table('participants')
            ->join('event','event.id','=','participants.event_id')
            ->where('event.organization_id','=', $orgid)
            ->where('participants.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
            ->get();
        }
        if($participants == null){
            return [];
        }

        Excel::create('Event Participants Transaction', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$participants)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Event Participants Transaction');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('List of Event Participants for Church ');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$participants){


                #dd($start_date);
                foreach($participants as $list) {
                    if($list->recurring == 1){
                        $list->recurring = 'R';
                    }else{
                        $list->recurring = '';
                    }
                    $amt = $list->qty*$list->fee;
                    $data[] = array(                       
                        Carbon::parse($list->start_date)->format('n/j/Y'),                       
                        $list->recurring,                       
                        $list->event_name,                       
                        $list->participant_name,                       
                        $list->qty,
                        number_format($amt,2,'.',','),
                    );
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Event Date','Recurring','Event Name','Attendee','# of Tickets','Total');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);
    }

    public function getVolunteersByFamilyExport($orgid, $start_date, $end_date,$_format,$v)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        $paricipants = new Participant;
        $title_ = 'Volunteers by Family Group';
        if($v == 4){
            $title_ = 'Volunteers by Event';
        }
        if($end_date ==''){
            if($v ==5){
                $volunteers =  \DB::table('volunteers')
                ->join('users','users.id','=','volunteers.user_id')
                ->join('family_members','family_members.user_id','=','users.id')
                ->join('family','family_members.family_id','=','family.id')
                ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('users.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)
                ->select('family.name as family_name',
                'event.name as event_name',
                'volunteer_groups.type as volunteer_group_name',
                'event.start_date as event_start_date')
                ->get();
            }else{
                $volunteers =  \DB::table('participants')
                 ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)         
                ->where('volunteers.created_at','<=',$end_date)     

                ->select('event.name as event_name',
                    'volunteers.name as vol_name',
                'event.start_date as event_start_date',
                'event.start_date as event_start_date',
                'event.end_date as event_end_date'
                )
                ->get();
            }

        }else{
            $end_date = $end_date.' 23:59:59.999';
            if($v ==5){
                $volunteers =  \DB::table('volunteers')
                ->join('users','users.id','=','volunteers.user_id')
                ->join('family_members','family_members.user_id','=','users.id')
                ->join('family','family_members.family_id','=','family.id')
                ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('users.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)
                ->where('volunteers.created_at','<=',$end_date)
                ->select('family.name as family_name',
                'event.name as event_name',
                'volunteer_groups.type as volunteer_group_name',
                'event.start_date as event_start_date')
                ->get();
            }else{
                $volunteers =  \DB::table('volunteers')
                 ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)         
                ->where('volunteers.created_at','<=',$end_date)     

                ->select('event.name as event_name',
                    'volunteers.name as vol_name',
                'event.start_date as event_start_date',
                'event.start_date as event_start_date',
                'volunteer_groups.type as volunteer_group_name',
                'event.end_date as event_end_date'
                )
                ->get();
            }
        }
        if($volunteers == null){
            return [];
        }
        Excel::create($title_, function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$v,$volunteers)
        {

            // Set the spreadsheet title, creator, and description

            if($v == 5){
                $excel->setTitle('Volunteers by Family Group');
            }else{
                $excel->setTitle('Volunteers by Event');
            }
            $excel->setCreator('CoCard')->setCompany('iSteward');
            if($v == 5){
                $excel->setDescription('List of Volunteers by Family Group for Church ');
            }else{
                $excel->setDescription('List of Volunteers by Event for Church ');
            }
            $start_date = \Request::get('input_start_date_timezone');
            $end_date = \Request::get('input_end_date_timezone');
            // dd($start_date, $end_date);
            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$v,$volunteers)
            {
                $x = 0;
                if($v == 5){
                    foreach($volunteers as $list) {
                        $data[] = array(
                            $start_date[$x],
                            $list->event_name,                            
                            $list->volunteer_group_name,
                            $list->family_name,
                            
                        );
                        $x++;
                    }
                }else{
                    #dd($volunteers);

                    foreach($volunteers as $list) { 
                        $data[] = array(
                            $start_date[$x],
                            $list->event_name,
                            $list->vol_name,                              
                            $list->volunteer_group_name,                              
                        );
                        $x++;
                    }
                }
                $sheet->fromArray($data, null, 'A1', false, false);
                if($v == 5){
                    $headings = array('Event Date','Event name',  'Volunteer Group','Name of the Family');
                }else{
                    $headings = array('Event Date','Event Name','Name of Volunteers','Volunteer Group' );
                }
                $sheet->prependRow(1, $headings);   
            });
        })->export($_format);
    }
    public function getDonationExport($orgid, $start_date, $end_date,$_format,$v)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        $paricipants = new Participant;
        $title_ = 'Donation by Family/Individual';
        if($v == 2){
            $title_ = 'Donation by Fund';
        }
        if($end_date ==''){
            $transaction =  \DB::table('transaction')
            ->join('users','users.id','=','transaction.user_id')
            ->join('donation','donation.transaction_id','=','transaction.id')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->where('users.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('users.last_name as last_name',
            'users.first_name as first_name',
            'users.middle_name as middle_name',
            'donation_list.name as dl_name',
            'transaction.transaction_key as transaction_key',
            'donation.created_at as created_at',
            'donation.amount as amount')
            ->get();

        }else{
            $end_date = $end_date.' 23:59:59.999';
            $transaction =  \DB::table('transaction')
            ->join('users','users.id','=','transaction.user_id')
            ->join('donation','donation.transaction_id','=','transaction.id')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->where('users.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('users.last_name as last_name',
            'users.first_name as first_name',
            'users.middle_name as middle_name',
            'donation_list.name as dl_name',
            'transaction.transaction_key as transaction_key',
            'donation.created_at as created_at',
            'donation.amount as amount')
            ->get();
        }
        if($transaction == null){
            return [];
        }
        Excel::create($title_, function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$v,$transaction){

            // Set the spreadsheet title, creator, and description

            if($v == 1){
                $excel->setTitle('Donation by Family Group');
            }else{
                $excel->setTitle('Donation by Event');
            }
            $excel->setCreator('CoCard')->setCompany('iSteward');
            if($v == 1){
                $excel->setDescription('List of Donation by Family/Individual for Church ');
            }else{
                $excel->setDescription('List of Donation by Fund for Church ');
            }

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$v,$transaction){

                #dd($start_date);

                if($v == 1){
                    foreach($transaction as $list) {
                        $data[] = array(
                            $list->first_name.' '.$list->middle_name.' '.$list->last_name,
                            $list->created_at,
                            $list->dl_name,
                            number_format($list->amount,2,'.',','),
                        );
                    }
                }else{
                    #dd($volunteers);
                    foreach($transaction  as $list) {
                        $data[] = array(
                            $list->created_at,
                            $list->dl_name,
                            number_format($list->amount,2,'.',','),
                        );
                    }
                }


                $sheet->fromArray($data, null, 'A1', false, false);
                if($v == 1){
                    $headings = array('Name', 'Date of Donation', 'Fund to Donate To','Amount Donated');
                }else{
                    $headings = array( 'Date of Donation', 'Fund to Donate To','Amount Donated');
                }
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);
    }
    public function getSummaryOfDonationCategoryExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        if($end_date =='1970-01-01'){
             $donation  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }elseif($start_date =='1970-01-01'){
             $donation = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $donation  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }
        if($donation == null){
            return [];
        }
        Excel::create('Summary of Donation by Category', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$donation)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Summary of Donation by Category');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('Summary of Donation by Category');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$donation){

                 
                #dd($start_date);
                $net_amount = 0;
                foreach($donation as $list) {
                    $amount = number_format($list->total, 2, '.', ',');
                    $data[] = array(
                        $list->donation_category_name,
                        $list->dl_count,
                        $amount,
                    );
                      $net_amount  += $list->total;
                }
                $count = 0;
                foreach($donation as $list) {
                    
                    if($count < 1){
                       $data[] = array(
                        'Total: ',
                        '-',
                        number_format($net_amount, 2, '.', ','),
                        );
                    }
                   $count++;
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Category', 'Number of Donations','Total Amount Donated');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);

    }
     public function getSummaryOfDonationFundExport($orgid, $start_date, $end_date,$_format)
    {
        $data['organization']   = $this->getOrganizationId($orgid);
        $slug           = $data['organization']->url;
        if($end_date =='1970-01-01'){
             $donation  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }elseif($start_date =='1970-01-01'){
             $donation = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $donation  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }
        if($donation == null){
            return [];
        }
        Excel::create('Summary of Donation by Fund', function($excel)  use ($slug, $orgid, $start_date, $end_date,$_format,$donation)
        {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Summary of Donation by Fund');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('Summary of Donation by Fund');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($slug, $orgid, $start_date, $end_date,$_format,$donation){

                 
                #dd($start_date);
                $net_amount = 0;
                foreach($donation as $list) {
                    $amount = number_format($list->total, 2, '.', ',');
                    $data[] = array(
                        $list->donation_category_name,
                        $list->dl_count,
                        $amount,
                    );
                      $net_amount  += $list->total;
                }
                $count = 0;
                foreach($donation as $list) {
                    
                    if($count < 1){
                       $data[] = array(
                        'Total: ',
                        '-',
                        number_format($net_amount, 2, '.', ','),
                        );
                    }
                   $count++;
                }

                $sheet->fromArray($data, null, 'A1', false, false);
                $headings = array('Category', 'Number of Donations','Total Amount Donated');
                $sheet->prependRow(1, $headings);

            });

        })->export($_format);

    }
    ////////PDF EXPORTS

    public function exportMemberListPDF($request, $slug)
    {
        $request->start_date= date("Y-m-d", strtotime($request->start_date));
        $request->end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;

        $user = new User;
        if($request->end_date =='1970-01-01'){
            #$end_date = $request->end_date.' 23:59:59.999';
            $data['users'] =  \DB::table('users')
            ->where('organization_id', $orgid)
            ->where('created_at','>=',$request->start_date)
            ->where('status','Active')
            ->orderBy('first_name','ASC')
            ->get();
        }else{
            #dd($request->end_date);\
            $end_date = $request->end_date.' 23:59:59.999';
            $data['users'] =  \DB::table('users')
            ->where('organization_id', $orgid)
            ->where('status','Active')
            ->orderBy('first_name','ASC')
            ->get();
        }
        if($data['users'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');

        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>List of Members</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '  <div style="text-align:center;"><h3>List of Members for '.$data['organization']->name.'</h3>';
        $html .= '      <h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '      <h5>Exported: '.$dateNow[0].'</h5>';
        $html .= '  </div><hr>';
        $html .= '  <div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        $html .= '              <td><b>Name</b></td>';
        $html .= '              <td><b>Email</b></td>';
        $html .= '              <td><b>Phone</b></td>';
        $html .= '              <td><b>Birthdate</b></td>';
        $html .= '              <td><b>Gender</b></td>';
        $html .= '              <td><b>Address</b></td>';
        $html .= '          </tr>';
        
        foreach($data['users'] as $u){
            $html .= '      <tr>';
            $html .= '          <td>'.$u->first_name.' '. $u->middle_name. ' '.$u->last_name.'</td>';
            $html .= '          <td>'.$u->email.'</td>';
            $html .= '          <td>'.$u->phone.'</td>';
            $html .= '          <td>'.Carbon::parse($u->birthdate)->format('n/j/Y').'</td>';
            $html .= '          <td>'.$u->gender.'</td>';
            $html .= '          <td>'.$u->address.' '.$u->city.' '.$u->state.'</td>';
            $html .= '      </tr>';
        }
        $html .= '      </table>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('List of Members');
    }
    public function exportEventParticipantsListPDF($request, $slug)
    {
        $start_date= date("Y-m-d", strtotime($request->start_date));
        $end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;
        $dateNow = \Request::get('input_now_date_timezone');
        if($request->end_date ==''){
            $end_date = $request->end_date.' 23:59:59.999';
            $participants =  \DB::table('participants')
            ->join('event','event.id','=','participants.event_id')
            ->where('event.organization_id','=', $orgid)
            ->where('participants.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
            ->get();
        }else{
            $end_date = $request->end_date.' 23:59:59.999';
            $data['participants'] =  \DB::table('participants')
            ->join('event','event.id','=','participants.event_id')
            ->where('event.organization_id','=', $orgid)
            ->where('participants.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
            ->get();
        }
        if($data['participants'] == null){
            return [];
        }
        #dd($data['users'] );
        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>List of Event Participants</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '<div style="text-align:center;"><h3>List of Event Participants for '.$data['organization']->name.'</h3>';
        $html .= '<h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '<h5>Exported: '.$dateNow[0].'</h5></div><hr>';
        $html .= '<div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '  <table width="100%">';
        $html .= '      <tr>';
        $html .= '          <td><b>Event Date</b></td>';    
        $html .= '          <td><b>Recurring</b></td>';    
        $html .= '          <td><b>Event Name</b></td>';    
        $html .= '          <td><b>Attendee</b></td>';    
        $html .= '          <td><b># of Tickets</b></td>';
        $html .= '          <td><b>Total</b></td>';
        $html .= '      </tr>';
        foreach($data['participants'] as $u){
             if($u->recurring == 1){
                        $u->recurring = 'R';
                    }else{
                        $u->recurring = '';
                    }
            $html .= '  <tr>';
            $html .= '      <td>'.Carbon::parse($u->start_date)->format('n/j/Y').'</td>';
            $html .= '      <td>'.$u->recurring.'</td>';
            $html .= '      <td>'.$u->event_name.'</td>';
            $html .= '      <td>'.$u->participant_name.'</td>';
            $html .= '      <td>'.$u->qty.'</td>';
            $html .= '      <td>$'.number_format(($u->qty*$u->fee),2,'.',',').'</td>';                 
            $html .= '  </tr>';
        }
        $html .= '  </table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('List of Event Participants');
    }
    public function exportVolunteersPDF($request, $slug,$t_)
    {
        $request->start_date= date("Y-m-d", strtotime($request->start_date));
        $request->end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;
        $dateNow = \Request::get('input_now_date_timezone');
        if($request->end_date ==''){
            $end_date = $request->end_date.' 23:59:59.999';
            if($t_ ==5){
                $data['volunteers'] =  \DB::table('volunteers')
                ->join('users','users.id','=','volunteers.user_id')
                ->join('family_members','family_members.user_id','=','users.id')
                ->join('family','family_members.family_id','=','family.id')
                ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('users.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=', $request->start_date)
                ->select('family.name as family_name',
                'event.name as event_name',
                'volunteer_groups.type as volunteer_group_name',
                'event.start_date as event_start_date')
                ->get();
            }else{
                $data['volunteers'] =  \DB::table('volunteers')
                ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)         
                ->where('volunteers.created_at','<=',$end_date)     

                ->select('event.name as event_name',
                    'volunteers.name as vol_name',
                'event.start_date as event_start_date',
                'volunteer_groups.type as volunteer_group_name',
                'event.end_date as event_end_date'
                )
                ->get();
            }
        }else{
            $end_date = $request->end_date.' 23:59:59.999';
            if($t_ == 5){
                  $data['volunteers'] =\DB::table('volunteers')
                ->join('users','users.id','=','volunteers.user_id')
                ->join('family_members','family_members.user_id','=','users.id')
                ->join('family','family_members.family_id','=','family.id')
                ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('users.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=', $request->start_date)
                ->where('volunteers.created_at','<=', $end_date)
                ->select('family.name as family_name',
                'event.name as event_name',
                'volunteers.name as vol_name',
                'volunteer_groups.type as volunteer_group_name',
                'event.start_date as event_start_date')
                ->get();
            }else{
                $data['volunteers'] =  \DB::table('volunteers')
                ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$request->start_date)         
                ->where('volunteers.created_at','<=',$end_date)  
                ->select('event.name as event_name',
                    'volunteers.name as vol_name',
                    'event.start_date as event_start_date',
                    'volunteer_groups.type as volunteer_group_name',
                    'event.end_date as event_end_date'
                )
                ->get();
            }
        }
        if($data['volunteers'] == null){
            return [];
        }
        #dd($data['users'] );
        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>List of Volunteers by Family</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        if($t_ == 5){
            $html .= '<div style="text-align:center;"><h3>List of Volunteers by Family for '.$data['organization']->name.'</h3>';
        }else{
            $html .= '<div style="text-align:center;"><h3>List of Volunteers by Event for '.$data['organization']->name.'</h3>';
        }
        $html .= '<h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '<h5>Exported: '.$dateNow[0].'</h5></div><hr>';
        $html .= '<div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '  <table width="100%">';
        $html .= '      <tr>';
        if($t_ == 5){
            $html .= '      <td><b>Event Date</b></td>';
            $html .= '      <td><b>Event Name</b></td>';
            $html .= '      <td><b>Volunteer Group</b></td>';
            $html .= '      <td><b>Name of the Family</b></td>';
           
            
        }else{
            $html .= '      <td><b>Event Date</b></td>';
            $html .= '      <td><b>Event Name</b></td>';   
            $html .= '      <td><b>Name of Volunteers</b></td>';
            $html .= '      <td><b>Volunteer Group</b></td>';
                     
           
        }
        $html .= '      </tr>';
        $start_date = \Request::get('input_start_date_timezone');
        $end_date = \Request::get('input_end_date_timezone');
        $x = 0;
        foreach($data['volunteers'] as $u){
            $html .= '  <tr>';
            if($t_ == 5){
                $html .= '<td>'.$start_date[$x].'</td>';
                $html .= '<td>'.$u->event_name.'</td>';
                $html .= '<td>'.$u->volunteer_group_name.'</td>';
                $html .= '<td>'.$u->family_name.'</td>';
                
                
            }else{
                //$html .= '<td>'.$u->first_name.' '.$u->middle_name.' '.$u->last_name.'</td>';
                $html .= '<td>'.$start_date[$x].'</td>';
                $html .= '<td>'.$u->event_name.'</td>';  
                $html .= '<td>'.$u->vol_name.'</td>';
                $html .= '<td>'.$u->volunteer_group_name.'</td>';
                              
                
            }
            $html .= '</tr>';
            $x++;
        }
            $html .= '</table>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        if($t_ == 5){
            $dompdf->stream('List of Volunteers by Family');
        }else{
            $dompdf->stream('List of Volunteers by Event');

        }
    }
    public function exportDonationPDF($request, $slug,$t_)
    {
        $request->start_date= date("Y-m-d", strtotime($request->start_date));
        $request->end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;
        $dateNow = \Request::get('input_now_date_timezone');
        if($request->end_date ==''){
            $end_date = $request->end_date.' 23:59:59.999';
            $data['transactions'] =  \DB::table('transaction')
            ->join('users','users.id','=','transaction.user_id')
            ->join('donation','donation.transaction_id','=','transaction.id')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->where('users.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$request->start_date)
            ->select('users.last_name as last_name',
            'users.first_name as first_name',
            'users.middle_name as middle_name',
            'donation_list.name as dl_name',
            'transaction.transaction_key as transaction_key',
            'donation.created_at as created_at',
            'donation.amount as amount')
            ->get();
        }else{
            $end_date = $request->end_date.' 23:59:59.999';
            $data['transactions'] =  \DB::table('transaction')
            ->join('users','users.id','=','transaction.user_id')
            ->join('donation','donation.transaction_id','=','transaction.id')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->where('users.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$request->start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('users.last_name as last_name',
            'users.first_name as first_name',
            'users.middle_name as middle_name',
            'donation_list.name as dl_name',
            'transaction.transaction_key as transaction_key',
            'donation.created_at as created_at',
            'donation.amount as amount')
            ->get();
        }
        if($data['transactions'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');
        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>List of Volunteers by Family</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        if($t_ == 1){
            $html .= '<div style="text-align:center;"><h3>List of Donation by Family/Individual for '.$data['organization']->name.'</h3>';
        }else{
            $html .= '<div style="text-align:center;"><h3>List of Donation by Fund for '.$data['organization']->name.'</h3>';
        }
        $html .= '<h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '<h5>Exported: '.$dateNow[0].'</h5></div><hr>';
        $html .= '<div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        if($t_ == 1){
            $html .= '          <td><b>Name</b></td>';
        }
        $html .= '              <td><b>Donation Date</b></td>';
        $html .= '              <td><b>Fund to Donate to</b></td>';
        $html .= '              <td><b>Amount</b></td>';
        $html .= '          </tr>';
        foreach( $data['transactions'] as $u){
            $html .= '      <tr>';
            if($t_ == 1){
                $html .= '      <td>'.$u->first_name.' '.$u->middle_name.' '.$u->last_name.'</td>';
                $html .= '      <td>'.Carbon::parse($u->created_at)->format('n/d/Y').'</td>';
                $html .= '      <td>'.$u->dl_name.'</td>';
                $html .= '      <td>'.number_format($u->amount,2,'.',',').'</td>';
            }else{
                $html .= '      <td>'.Carbon::parse($u->created_at)->format('n/d/Y').'</td>';
                $html .= '      <td>'.$u->dl_name.'</td>';
                $html .= '      <td>'.number_format($u->amount,2,'.',',').'</td>';
            }
            $html .= '      </tr>';           
        } 
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('Legal', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        if($t_ == 1){
            $dompdf->stream('List of Donation by Family Individual');
        }else{
            $dompdf->stream('List of Donation by Fund');

        }
    }
    public function exportSummaryOfEventsPDF($request, $slug)
    {
        $start_date= date("Y-m-d", strtotime($request->start_date));
        $end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;

        if($end_date =='1970-01-01'){
             $data['event']  = \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.status','Active')
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }elseif($start_date =='1970-01-01'){
             $data['event']  = \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->where('event.status','Active')
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
         $data['event'] = \DB::table('event')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->where('event.status','Active')
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        }
        if($data['event'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');

        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Summary of Events</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '  <div style="text-align:center;"><h3>Summary of Events for '.$data['organization']->name.'</h3>';
        $html .= '      <h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '      <h5>Exported: '.$dateNow[0].'</h5>';
        $html .= '  </div><hr>';
        $html .= '  <div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        $html .= '              <td><b>Start Date</b></td>';
        $html .= '              <td><b>Event Name</b></td>';
        $html .= '              <td><b>Recurring</b></td>';        
        $html .= '              <td><b>Event Capacity</b></td>';
        $html .= '              <td><b># Signed Up</b></td>';
        
        $html .= '          </tr>';
        
        foreach($data['event'] as $e){
            if($e->recurring == 1){
                        $e->recurring = 'R';
                    }else{
                        $e->recurring = '';
                    }
            $html .= '      <tr>';
            $html .= '          <td>'.Carbon::parse($e->start_date)->format('n/j/Y').'</td>';
            $html .= '          <td>'.$e->event_name.'</td>';
            $html .= '          <td>'.$e->recurring.'</td>';
            $html .= '          <td>'.$e->capacity.'</td>';
            $html .= '          <td>'.$e->pending.'</td>';
            
            $html .= '      </tr>';
        }
        $html .= '      </table>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Summary of Events');
    }
    public function exportSummaryOfDonationFundPDF($request, $slug)
    {
        $start_date= date("Y-m-d", strtotime($request->start_date));
        $end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;

        if($end_date =='1970-01-01'){
             $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }elseif($start_date =='1970-01-01'){
             $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        }
        if($data['donation'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');

        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Summary of Donation by Fund</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '  <div style="text-align:center;"><h3>Summary of Donation by Fund for '.$data['organization']->name.'</h3>';
        $html .= '      <h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '      <h5>Exported: '.$dateNow[0].'</h5>';
        $html .= '  </div><hr>';
        $html .= '  <div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        $html .= '              <td><b>Category</b></td>';
        $html .= '              <td><b>Donation List</b></td>';
        $html .= '              <td><b>Number of Donations</b></td>';
        $html .= '              <td><b>Total Amount Donated</b></td>';
        $html .= '          </tr>';
        
        foreach($data['donation'] as $e){
            $html .= '      <tr>';
            $html .= '          <td>'.$e->donation_category_name.'</td>';
            $html .= '          <td>'.$e->donation_list_name.'</td>';
            $html .= '          <td>'.$e->dl_count.'</td>';
            $html .= '          <td>$'.number_format($e->total,2,'.',',').'</td>';
            $html .= '      </tr>';
        }
         $data['count_amount'] = 0;
        foreach($data['donation'] as $row){
            $data['count_amount']  += $row->total;
        }
        $html .= '      <tr>';
        $html .= '          <td><b>TOTAL:</br></td>';
        $html .= '          <td>-</td>';
        $html .= '          <td>-</td>';
        $html .= '          <td>$'. number_format($data['count_amount'],2,'.',',').'</td>';
        $html .= '      </tr>';
        $html .= '      </table>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Summary of Donation by Fund');
    }
    public function exportSummaryOfDonationCategoryPDF($request, $slug)
    {
        $start_date= date("Y-m-d", strtotime($request->start_date));
        $end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;

        if($end_date =='1970-01-01'){
             $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }elseif($start_date =='1970-01-01'){
             $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
          $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        }
        if($data['donation'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');

        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Summary of Donation by Category</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '  <div style="text-align:center;"><h3>Summary of Donation by Category for '.$data['organization']->name.'</h3>';
        $html .= '      <h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '      <h5>Exported: '.$dateNow[0].'</h5>';
        $html .= '  </div><hr>';
        $html .= '  <div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        $html .= '              <td><b>Category</b></td>';
        $html .= '              <td><b>Number of Donations</b></td>';
        $html .= '              <td><b>Total Amount Donated</b></td>';
        $html .= '          </tr>';
        
        foreach($data['donation'] as $e){
            $html .= '      <tr>';
            $html .= '          <td>'.$e->donation_category_name.'</td>';
            $html .= '          <td>'.$e->dl_count.'</td>';
            $html .= '          <td>$'.number_format($e->total,2,'.',',').'</td>';
            $html .= '      </tr>';
        }
         $data['count_amount'] = 0;
        foreach($data['donation'] as $row){
            $data['count_amount']  += $row->total;
        }
        $html .= '      <tr>';
        $html .= '          <td><b>TOTAL:</br></td>';
        $html .= '          <td>-</td>';
        $html .= '          <td>$'. number_format($data['count_amount'],2,'.',',').'</td>';
        $html .= '      </tr>';
        $html .= '      </table>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Summary of Donation by Category');
    }
     public function exportSummaryOfVolunteersPDF($request, $slug)
    {
        $start_date= date("Y-m-d", strtotime($request->start_date));
        $end_date= date("Y-m-d", strtotime($request->end_date));
        $data['organization']   = $this->getUrl($slug);
        $orgid = $data['organization']->id;

        if($end_date =='1970-01-01'){
             $data['volunteers']  = \DB::table('volunteers')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }elseif($start_date =='1970-01-01'){
            $data['volunteers']  = \DB::table('volunteers')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }else{
          $end_date = $end_date.' 23:59:59.999';
         $data['volunteers']  = \DB::table('volunteers')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        }
        if($data['volunteers'] == null){
            return [];
        }
        #dd($data['users'] );
        $dateNow = \Request::get('input_now_date_timezone');

        $html  = '<html>';
        $html .= '<head>';
        $html .= '   <title>Summary of Event Volunteers</title>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div style="background:#ffffff; width:100%; height:100%;">';
        $html .= '  <div style="text-align:center;"><h3>Summary of Event Volunteers for '.$data['organization']->name.'</h3>';
        $html .= '      <h5>From '.$request->start_date.' To '.$request->end_date.'</h5>';
        $html .= '      <h5>Exported: '.$dateNow[0].'</h5>';
        $html .= '  </div><hr>';
        $html .= '  <div style="background:#ffffff; width:100%; height:5%;">';
        $html .= '      <table width="100%">';
        $html .= '          <tr>';
        $html .= '              <td><b>Event Date</b></td>';
        $html .= '              <td><b>Event Name</b></td>';
        $html .= '              <td><b>Volunteer Group</b></td>';
        $html .= '              <td><b>No. Volunteers Needed</b></td>';
        $html .= '              <td><b>No. of Volunters Signed up</b></td>';
        $html .= '          </tr>';
        
        foreach($data['volunteers'] as $e){
            $html .= '      <tr>';
            $html .= '          <td>'.Carbon::parse($e->start_date)->format('m/j/Y').'</td>';
            $html .= '          <td>'.$e->event_name.'</td>';
            $html .= '          <td>'.$e->volunteer_group_name.'</td>';
            $html .= '          <td>'.$e->volunteers_needed.'</td>';
            $html .= '          <td>'.$e->total.'</td>';
            $html .= '      </tr>';
        }
        $html .= '      </table>';
        $html .= '  </div>';
        $html .= '</div>';
        $html .= '</body>';
        $html .= '</html>';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('Summary of Event Volunteers');
    }
    ///////LIST VIEW
    public function getMemberList($orgid,$start_date,$end_date)
    {
        #dd($orgid);
        $user = new User;

        //dd($start_date);
        if($end_date =='1970-01-01'){
            return $user->where('organization_id','=', $orgid)
            ->where('created_at','>=',$start_date)
            ->where('status','Active')
            ->orderBy('first_name', 'ASC')
            ->get();
        }
        if($start_date =='1970-01-01'){
            return $user->where('organization_id','=', $orgid)
            ->orderBy('first_name', 'ASC')
            ->where('status','Active')
            ->get();
        }
        $end_date = $end_date.' 23:59:59.999';
        return $user->where('organization_id','=', $orgid)        
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$end_date)
        ->where('status','Active')
        ->orderBy('first_name', 'ASC')
        ->get();
    }
    public function getEventParticipantList($orgid,$start_date,$end_date)
    {
        #dd($orgid);
        $paricipants = new Participant;

        #dd($end_date);
        if($end_date ==''){
            return $paricipants
            ->join('event','event.id','=','participants.event_id')
            ->where('event.organization_id','=', $orgid)
            ->where('participants.created_at','>=',$start_date)
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
            ->get();
        }
        $end_date = $end_date.' 23:59:59.999';
        return $paricipants

        ->join('event','event.id','=','participants.event_id')
        ->where('event.organization_id','=', $orgid)
        ->where('participants.created_at','>=',$start_date)
        ->where('participants.created_at','<=',$end_date)
        ->select('event.name as event_name',
            'event.start_date as start_date',
            'participants.name as participant_name',
            'participants.qty as qty',
            'event.fee as fee',
            'event.recurring as recurring')
        ->get();
    }
    public function getVolunteersByFamily($orgid,$start_date,$end_date)
    {
        $volunteers = new Volunteer;
        if($end_date ==''){
            return  $volunteers
            ->join('users','users.id','=','volunteers.user_id')
            ->join('family_members','family_members.user_id','=','users.id')
            ->join('family','family_members.family_id','=','family.id')
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('users.organization_id','=',$orgid)
            ->where('volunteers.created_at','>=',$start_date)
            ->select('family.name as family_name',
            'event.name as event_name',
            'volunteer_groups.type as volunteer_group_name',
            'event.start_date as event_start_date')
            ->get();

        }
        $end_date = $end_date.' 23:59:59.999';
        return $volunteers
        ->join('users','users.id','=','volunteers.user_id')
        ->join('family_members','family_members.user_id','=','users.id')
        ->join('family','family_members.family_id','=','family.id')
        ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
        ->join('event','event.id','=','volunteer_groups.event_id')
        ->where('users.organization_id','=',$orgid)
        ->where('volunteers.created_at','>=',$start_date)
        ->where('volunteers.created_at','<=',$end_date)
        ->select('family.name as family_name',
        'event.name as event_name',
        'volunteer_groups.type as volunteer_group_name',
        'event.start_date as event_start_date')
        ->get();
    }

    public function getVolunteersByEvent($orgid,$start_date,$end_date)
    {
        $volunteer = new Volunteer;
        #dd($end_date);
        if($end_date ==''){
            $data['volunteer'] = $volunteer
                ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)              
                ->where('volunteers.created_at','>=',$start_date)              
                ->select(
                'event.name as event_name',
                'volunteers.name as vol_name',
                'event.start_date as event_start_date',
                'event.start_date as event_start_date',
                'volunteer_groups.type as volunteer_group_name',
                'event.end_date as event_end_date'
                )
                ->get();
            #dd($data);
            return $data['volunteer'];
        }
        $end_date = $end_date.' 23:59:59.999';
         $data['volunteer'] = $volunteer
                ->join('volunteer_groups','volunteers.volunteer_group_id','=','volunteer_groups.id')
                ->join('event','event.id','=','volunteer_groups.event_id')
                ->where('event.organization_id','=',$orgid)
                ->where('volunteers.created_at','>=',$start_date)         
                ->where('volunteers.created_at','<=',$end_date)  
                ->select('event.name as event_name',
                    'volunteers.name as vol_name',
                    'event.start_date as event_start_date',
                    'event.start_date as event_start_date',
                    'volunteer_groups.type as volunteer_group_name',
                    'event.end_date as event_end_date'
                )
        ->get();
       //dd($data['volunteer']);
        return $data['volunteer'];
    }
    public function getDonationByFamilyandFund($orgid,$start_date,$end_date)
    {
        #dd($orgid);
        $transaction = new Transaction;

        #dd($end_date);
        if($end_date ==''){
            $data['transactions'] = $transaction
            ->join('users','users.id','=','transaction.user_id')
            ->join('donation','donation.transaction_id','=','transaction.id')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->where('users.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('users.last_name as last_name',
            'users.first_name as first_name',
            'users.middle_name as middle_name',
            'donation_list.name as dl_name',
            'transaction.transaction_key as transaction_key',
            'donation.created_at as created_at',
            'donation.amount as amount')
            ->get();
            #dd($data);
            return $data['transactions'];
        }
        $end_date = $end_date.' 23:59:59.999';
        $data['transactions'] = $transaction
        ->join('users','users.id','=','transaction.user_id')
        ->join('donation','donation.transaction_id','=','transaction.id')
        ->join('donation_list','donation.donation_list_id','=','donation_list.id')
        ->where('users.organization_id','=',$orgid)
        ->where('donation.created_at','>=',$start_date)
        ->where('donation.created_at','<=',$end_date)
        ->select('users.last_name as last_name',
        'users.first_name as first_name',
        'users.middle_name as middle_name',
        'donation_list.name as dl_name',
        'transaction.transaction_key as transaction_key',
        'donation.created_at as created_at',
        'donation.amount as amount')
        ->get();
        return $data['transactions'];
    }
    public function getSummaryOfDonationFundReportList($orgid,$start_date,$end_date)
    {
        #dd($orgid);

        #dd($end_date);
        if($end_date ==''){
            $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
              DB::raw('sum(donation.amount) as total'),
              DB::raw('count(donation.donation_list_id) as dl_count')
              )
            ->groupBy('donation_list.id')->get();
            #dd($data);
            return $data['donation'];
        }
        $end_date = $end_date.' 23:59:59.999';
         $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_list.id')->get();
        return $data['donation'];
    }
    public function getSummaryOfDonationCategoryReportList($orgid,$start_date,$end_date)
    {
        #dd($orgid);

        #dd($end_date);
        if($end_date ==''){
            $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
              DB::raw('sum(donation.amount) as total'),
              DB::raw('count(donation.donation_list_id) as dl_count')
              )
            ->groupBy('donation_category.id')->get();
            #dd($data);
            return $data['donation'];
        }
        $end_date = $end_date.' 23:59:59.999';
         $data['donation']  = \DB::table('donation')
            ->join('donation_list','donation.donation_list_id','=','donation_list.id')
            ->join('donation_category','donation_category.id','=','donation_list.donation_category_id')
            ->where('donation_category.organization_id','=',$orgid)
            ->where('donation.created_at','>=',$start_date)
            ->where('donation.created_at','<=',$end_date)
            ->select('donation_category.name as donation_category_name',
            'donation.created_at as created_at',
            'donation_list.name as donation_list_name',
            DB::raw('sum(donation.amount) as total'),
            DB::raw('count(donation.donation_list_id) as dl_count')
            )
            ->groupBy('donation_category.id')->get();
        return $data['donation'];
    }
    public function getSummaryOfEventsList($orgid,$start_date,$end_date)
    {
        #dd($orgid);
        $event = new Event;

        #dd($end_date);
        if($end_date ==''){
            $data['event'] = $event
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.status','Active')
            >select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
            #dd($data);
            return $data['event'];
        }
        $end_date = $end_date.' 23:59:59.999';
         $data['event'] = $event
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->where('event.status','Active')
            ->select('event.name as event_name',
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'event.recurring as recurring',
            'event.pending as pending')
            ->orderBy('event.start_date', 'ASC')
            ->groupBy('event.id')->get();
        return $data['event'];
    }
    public function getSummaryOfVolunteersList($orgid,$start_date,$end_date)
    {
        #dd($orgid);
        $volunteer = new Volunteer;

        #dd($end_date);
        if($end_date ==''){
            $data['volunteer'] = $volunteer
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
            #dd($data);
            return $data['volunteer'];
        }
        $end_date = $end_date.' 23:59:59.999';
         $data['volunteer'] = $volunteer
            ->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')
            ->join('event','event.id','=','volunteer_groups.event_id')
            ->where('event.organization_id','=',$orgid)
            ->where('event.created_at','>=',$start_date)
            ->where('event.created_at','<=',$end_date)
            ->select('event.name as event_name',
                DB::raw('count(volunteer_groups.id) as total'),
            'event.start_date as start_date',
            'event.end_date as end_date',
            'event.capacity as capacity',
            'volunteer_groups.volunteers_needed as volunteers_needed',
            'volunteer_groups.type as volunteer_group_name',
            'event.pending as pending')
            ->groupBy('volunteer_groups.id')->get();
        return $data['volunteer'];
    }
    public function displayList($request)
    {
        $sdate= date("Y-m-d", strtotime($request->start_date));
        $edate= date("Y-m-d", strtotime($request->end_date));
        switch($request->report){
            case 1:
            #dd($request->organization);
            $data['reports'] = $this->getDonationByFamilyandFund($request->organization,$sdate,$edate);
            break;
            case 2:
            #dd($request->organization);
            $data['reports'] = $this->getDonationByFamilyandFund($request->organization,$sdate,$edate);
            break;
            case 3:
            #dd($request->organization);
            $data['reports'] = $this->getEventParticipantList($request->organization,$sdate,$edate);
            break;
            case 4:
            #dd($request->organization);
            $data['reports'] = $this->getVolunteersByEvent($request->organization,$sdate,$edate);
            break;
            case 5:
            #dd($request->organization);
            $data['reports'] = $this->getVolunteersByFamily($request->organization,$sdate,$edate);
            break;
            case 6:
            #dd($request->organization);
            $data['reports'] = $this->getMemberList($request->organization,$sdate,$edate);
            break;
            case 7:
            #dd($request->organization);
            $data['reports'] = $this->getSummaryOfDonationFundReportList($request->organization,$sdate,$edate);
            break;
            case 8:
            #dd($request->organization);
            $data['reports'] = $this->getSummaryOfEventsList($request->organization,$sdate,$edate);
            break;
            case 9:
            #dd($request->organization);
            $data['reports'] = $this->getSummaryOfVolunteersList($request->organization,$sdate,$edate);
            break;
            case 10:
            #dd($request->organization);
            $data['reports'] = $this->getSummaryOfDonationCategoryReportList($request->organization,$sdate,$edate);
            break;
            case 7:
            #dd($request->organization);
            $data['reports'] = $this->getSummaryOfDonationFundReportList($request->organization,$sdate,$edate);
            break;

        }
        return $data['reports'];

    }
    public function generateExcel($request,$slug,$_format)
    {
        //dd($request['input_start_date_timezone']);
        $data['organization']   = $this->getUrl($slug);
        $orgid           = $data['organization']->id;
        $sdate= date("Y-m-d", strtotime($request->start_date));
        $edate= date("Y-m-d", strtotime($request->end_date));
        switch($request->report){
            case 1:
            #dd($request->organization);
            $data['reports'] = $this->getDonationExport($request->organization,$sdate,$edate,$_format,1);
            break;
            case 2:
            #dd($request->organization);
            $data['reports'] = $this->getDonationExport($request->organization,$sdate,$edate,$_format,2);
            break;
            case 3:
            #dd($request->organization);
            $data['reports'] = $this->getEventParticipantsListExport($request->organization,$sdate,$edate,$_format);
            break;
            case 4:
            #dd($request->organization);
            $data['reports'] = $this->getVolunteersByFamilyExport($request->organization,$sdate,$edate,$_format,4);
            break;
            case 5:
            #dd($request->organization);
            $data['reports'] = $this->getVolunteersByFamilyExport($request->organization,$sdate,$edate,$_format,5);
            break;
            case 6:
            $data['reports'] = $this->getMemberExcelExport($request->organization,$sdate,$edate,$_format);
            break;
            case 7:
            $data['reports'] = $this->getSummaryOfDonationFundExport($request->organization,$sdate,$edate,$_format);
            break;
            case 8:
            $data['reports'] = $this->getSummaryOfEventsExport($request->organization,$sdate,$edate,$_format);
            break;
            case 9:
            $data['reports'] = $this->getSummaryOfVolunteersExport($request->organization,$sdate,$edate,$_format);
            break;
             case 10:
            $data['reports'] = $this->getSummaryOfDonationCategoryExport($request->organization,$sdate,$edate,$_format);
            break;
        }
        return $data['reports'];
    }

}
