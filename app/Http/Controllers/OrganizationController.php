<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Http\Requests;
use Acme\Repositories\OrganizationRepository as Organization;
use Acme\Repositories\PendingOrganizationUsersRepository as Pending;
use Acme\Repositories\UserRepository as User;
use Acme\Repositories\RoleRepository as Role;
use Acme\Repositories\BackupRepository as Backup;
use Acme\Repositories\ActivityLogRepository;
use App\ActivityLog;
use Auth;
use DB;
use App;
use Mail;
use Gate;
class OrganizationController extends Controller
{
    public function __construct(ActivityLogRepository $activityLog,Backup $backup,Pending $pending, Organization $organization, User $user,Role $role){
        #$this->middleware('auth');
        $this->organization = $organization;
        $this->pending = $pending;
        $this->auth = Auth::user();
        $this->user = $user;
        $this->backup = $backup;
        $this->activityLog = $activityLog;
        $this->role = $role;
        if($this->auth != null){
            App::setLocale($this->auth->locale);
        }
    }
    public function index(Request $request)
    {
        if(Gate::denies('view_organization') && $request->type !='json')
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
            #$data['organization'] = $this->organization->getOrganization($request);
            $data['organization'] = $this->pending->getAllOrganizationUser($request);
            $data['search'] = $request->input('search');
            if($request->type =="json"){
                return $data;
            }
            return view('cocard-church.superadmin.organizations',$data);
        }
    }
    public function dashboard()
    {
        if(Auth::user()->organization_id == 0){
            return view('cocard-church.superadmin.dashboard');
        }
        else{
            Auth::logout();
            return redirect('/login');
        }
        return view('cocard-church.superadmin.dashboard');
    }
    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        if(Gate::denies('add_organization'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data = $this->organization->create();
            #dd($data);
            return view('cocard-church.superadmin.createorg', $data);
        }
    }
    public function save(Request $request, $id = 0){
        $results = $this->organization->save($request, $id);
        if($request->type =="json"){
            return $data;
        }
        if($results['status'] == false)
        {
            return redirect()->route('organization_create')->withErrors($results['results'])->withInput();
        }
        return redirect()->route('organization_create')->with('message', 'Successfully Added Organization');
    }

    public function saveOrganization(Request $request, $id = 0){
        // dd($request);
        $results = $this->organization->saveOrg($request, $id);

        if($request->type =="json"){
            return $data;
        }
        if($results['status'] == true)
        {
            $id = $results['id'];
            #$this->organization->sendEmailNotification($request, $id);
            return redirect('/organizations')->with('message', 'Successfully Added Organization');
            #for email
            return redirect()->route('send', $id);
        }
        else
        {
            return redirect()->route('organization_create')->withErrors($results['results'])->withInput();
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
    public function edit($id)
    {
        //
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
    public function sendEmailNotification(Request $request, $id)
    {
        $organization = $this->organization->findId($id);
        Mail::send('cocard-church.email.notification',['organization' => $organization], function ($m) use ($organization) {
            $m->to($organization->email, $organization->contact_person)->subject('Successfully Registered!');
        });
        if($request->type =="json"){
            return $data;
        }
        return redirect()->route('organization_create')->with('message', 'Successfully Added Organization');
    }


    public function fetchEvents($id,$needing_volunteers = null){
        $organization = $this->organization->findOrganization($id);
        $events = ($needing_volunteers == "filtered")?$organization->events_needing_volunteers:$organization->events;
        return response(json_encode($events))->header('Content-Type','application/json');
    }

    public function reports(Request $request, $slug)
    {
        if(Gate::denies('generate_report'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['organization']   = $this->organization->getUrl($slug);
            $data['slug']           = $data['organization']->url;
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id); 

            if($auth == true){
                $data['start_date']     = '';
                $data['end_date']       = '';
                $data['report']         = '';
                $data['eformat']         = '';
                $data['reports']        = [];
                $data['message']=' ';
                if($request->type =="json"){
                    return $data;
                }
                return view('cocard-church.church.admin.reports',$data);

            }else{
                return view('errors.errorpage');
            }
            #dd($request);   
        }
    }
    public function sort_member_list(Request $request,$slug){
        dd($request);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $data['organization']   = $this->organization->getUrl($slug);
        $data['slug']           = $data['organization']->url;
        $data['sort'] = ($request->sort == 'asc')? 'desc' : 'asc';
        $data['reports'] = $this->organization->displayList_member($request);
        return view('cocard-church.church.admin.reports',$data);
    }
    public function generateReport(Request $request, $slug){

      
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $data['sort'] = '';
        $data['organization']   = $this->organization->getUrl($slug);
        $data['slug']           = $data['organization']->url;
        $data['start_date']     = $request->start_date;
        $data['end_date']       = $request->end_date;
        $data['converted_start_date']     = $request->converted_start_date;
        $data['converted_end_date']       = $request->converted_end_date;
        $data['report']         = $request->report;
        $data['eformat']        = $request->eformat;
        $data['message']=' ';
        $auth = $this->activityLog->AuthGate(Gate::allows('generate_report'),Auth::user()->organization_id,$data['organization']->id);

        if($auth == false)
        {
            return view('errors.errorpage');
        }
        else
        {
            $this->activityLog->log_activity(Auth::user()->id,'Reports','Generate Reports', $data['organization']->id);
            if (isset($_POST['view'])) {
                 
                 //dd($request);
                $data['reports'] = $this->organization->displayList($request);
                if($request->report == 7 || $request->report == 10){
                    $data['count_amount'] = 0;
                    foreach($data['reports'] as $row){
                        $data['count_amount']  += $row->total;
                    }
                }
            } elseif(isset($_POST['generate'])) {
                switch($request->eformat){
                    case 'xls'  :
                    $data['reports'] = $this->organization->generateExcel($request,$slug,'xls');
                    break;
                    case 'xlsx' :
                    $data['reports'] = $this->organization->generateExcel($request,$slug,'xlsx');
                    break;
                    case 'csv'  :
                    $data['reports'] = $this->organization->generateExcel($request,$slug,'csv');
                    break;
                    case 'pdf'  :
                    switch ($data['report']) {
                        case 1:
                        $data['reports'] = $this->organization->exportDonationPDF($request,$slug,1);
                        break;
                        case 2:
                        $data['reports'] = $this->organization->exportDonationPDF($request,$slug,2);
                        break;
                        case 3:
                        $data['reports'] = $this->organization->exportEventParticipantsListPDF($request,$slug);
                        break;
                        case 4:
                        $data['reports'] = $this->organization->exportVolunteersPDF($request,$slug,4);
                        break;
                        case 5:
                        $data['reports'] = $this->organization->exportVolunteersPDF($request,$slug,5);
                        break;
                        case 6:
                        $data['reports'] = $this->organization->exportMemberListPDF($request,$slug);
                        break;
                        case 7:
                        $data['reports'] = $this->organization->exportSummaryOfDonationFundPDF($request,$slug);
                        break;
                        case 8:
                        $data['reports'] = $this->organization->exportSummaryOfEventsPDF($request,$slug);
                        break;
                        case 9:
                        $data['reports'] = $this->organization->exportSummaryOfVolunteersPDF($request,$slug);
                        break;
                        case 10:
                        $data['reports'] = $this->organization->exportSummaryOfDonationCategoryPDF($request,$slug);
                        break;
                    }

                    break;

                }
            }
        }

        if($data['reports'] == null){
            //dd($data['reports']);
            $data['message']='No Data to be exported';
            return view('cocard-church.church.admin.reports',$data);

        }
        // dd($data['reports']->toArray());
        return view('cocard-church.church.admin.reports',$data);
    }
    public function backupIndex(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        

        if(Gate::denies('backup_database'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data['slug']         = $data['organization']->url;
                if($request->type =="json"){
                    return $data;
                }
                return view('cocard-church.church.admin.backup',$data);
            }else{
                return view('errors.errorpage');
            }
        }
    }
    public function backup(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        $auth = $this->activityLog->AuthGate(Gate::allows('backup_database'),Auth::user()->organization_id,$data['organization']->id);

        if($auth == false)
        {
            return view('errors.errorpage');
        }
        else
        {

            $data['slug']         = $data['organization']->url;
            $this->activityLog->log_activity(Auth::user()->id,'Backup','Backup Database', $data['organization']->id);
            $this->backup->backup($data['organization']->id);
            
            if($request->type =="json"){
                return $data;
            }
            return view('cocard-church.church.admin.backup',$data);
        }
    }

    public function settingsUpdate(Request $request, $id)
    {
        $slug = $request->slug;
        $data['organization'] = $this->organization->getUrl($slug);
        $results = $this->organization->settingsUpdate($request, $id);

        if($request->type == "json"){
            return $data;
        }

        if($results['status'] == false){
            return redirect('organization/'.$slug.'/administrator/settings')->withErrors($results['results'])->withInput();
        }else{
            $this->activityLog->log_activity(Auth::user()->id,'Updated Settings','Updated color scheme, nmi login information', $data['organization']->id);
            return redirect('organization/'.$slug.'/administrator/settings')->with('message', 'Successfully Updated Organization Settings');
        }
    }

    public function restoreDefault($slug,$id){

        $data['organization'] = $this->organization->getUrl($slug);
        $this->organization->restoreDefault($id);
        $this->activityLog->log_activity(Auth::user()->id,'Updated Settings','Restored Default Settings', $data['organization']->id);

        return redirect('organization/'.$slug.'/administrator/settings')->with('message','Restored Default Settings');

    }

    public function settings(Request $request, $slug)
    {
        $data['organization'] = $this->organization->getUrl($slug);
        

        if(Gate::denies('edit_admin_settings')){
            return view('errors.errorpage');
        }
        else
        {
            $auth = $this->activityLog->AuthGate(Auth::user()->organization_id,$data['organization']->id);

            if($auth == true){
                $data = $this->organization->settings($data['organization']->id);

                $data['nmi_user'] = '';
                $data['nmi_pass'] = '';
                if( is_object($data['organization']) ){
                    $data['nmi_user'] = $data['organization']->nmi_user;
                    $data['nmi_pass'] = $data['organization']->nmi_pass;
                }

                $data['slug'] = $slug;

                if($request->type =="json"){
                    return $data;
                }
                $theme = $this->theme($data['organization']->banner_image,$data['organization']->scheme);
                $data = array_merge($data,$theme);

                return view('cocard-church.church.admin.settings',$data);
            }else{
                return view('errors.errorpage');
            }
            
        }
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
    public function test(){
        return view('test');
    }

}
