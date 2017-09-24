<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 1/5/2017
 * Time: 2:12 PM
 */

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Organization;
use App\User;
use App\UserRole;
use App\Event;
use App\Family;
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

class BackupRepository extends Repository{

    protected $listener;

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function model(){
        return 'App\Organization';
    }

    public function getOrganizationId($organization_id){
        return $this->model->where('id', $organization_id)->first();
    }

    public function backup($id){
    	$this->organization_table($id);
    }
     public function organization_table($id){
        $data['organization']   = $this->getOrganizationId($id);
        $slug           = $data['organization']->url;
        $title_ = 'Backup For '.$data['organization']->name;        
        
        Excel::create($title_, function($excel)  use ($slug,$id){

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Organization Backup');
            $excel->setCreator('CoCard')->setCompany('iSteward');
            $excel->setDescription('backup of Database');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('Organization Details', function($sheet) use ($slug,$id){
               //
            	$transaction =  \DB::table('organizations')
                            ->where('id','=',$id)
                            ->get();
            	$this->organization_sheet($sheet,$transaction);
            });
 			$excel->sheet('Users', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('users')
                            ->where('organization_id','=',$id)
                            ->get();
            	$this->users_sheet($sheet,$transaction);                
            });
            $excel->sheet('Activity Log', function($sheet) use ($slug,$id){            	
 				$transaction =  \DB::table('activity_log')
 							->join('users','users.id','=','activity_log.user_id')
                            ->where('users.organization_id','=',$id)
                            ->get();
            	$this->activity_log_sheet($sheet,$transaction);                
            });
            $excel->sheet('Donation Category', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('donation_category')
                            ->where('organization_id','=',$id)
                            ->get();
            	$this->donation_category_sheet($sheet,$transaction);                
            });
            $excel->sheet('Donation List', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('donation_list')
 							->join('donation_category','donation_list.donation_category_id','=','donation_category.id')
                            ->where('donation_category.organization_id','=',$id)
                            ->get();
            	$this->donation_list_sheet($sheet,$transaction);                
            });
            $excel->sheet('Email Group', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('email_groups')
                            ->where('organization_id','=',$id)
                            ->get();
            	$this->email_groups_sheet($sheet,$transaction);                
            });
            $excel->sheet('Email Group Members', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('email_group_members')
 							->join('email_groups','email_groups.id','=','email_group_members.email_group_id')
                            ->where('email_groups.organization_id','=',$id)
                            ->get();
            	$this->email_group_members_sheet($sheet,$transaction);                
            });
            $excel->sheet('Event', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('event')
                            ->where('organization_id','=',$id)
                            ->get();
            	$this->event_sheet($sheet,$transaction);                
            });
            $excel->sheet('Family', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('family')
                            ->where('organization_id','=',$id)
                            ->get();
            	$this->family_sheet($sheet,$transaction);                
            });
            $excel->sheet('Family Members', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('family_members')
 							->join('family','family.id','=','family_members.family_id')
                            ->where('family.organization_id','=',$id)
                            ->get();
            	$this->family_members_sheet($sheet,$transaction);                
            });
            $excel->sheet('Frequency', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('frequency')
                            ->get();
            	$this->frequency_sheet($sheet,$transaction);                
            });
            $excel->sheet('Participants', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('participants')
 							->join('event','participants.event_id','=','event.id')
 							->where('event.organization_id','=',$id)
                            ->get();
            	$this->participants_sheet($sheet,$transaction);                
            });
            $excel->sheet('Staff', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('user_roles')
                            ->join('users','users.id','=','user_roles.user_id')
                            ->join('roles','roles.id','=','user_roles.role_id')
                            ->where('users.organization_id','=',$id)
                            ->where('user_roles.role_id','!=',1)
 							->where('user_roles.role_id','!=',3)
                            ->get();
            	$this->staff_sheet($sheet,$transaction);                
            });
            $excel->sheet('Transaction', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('transaction')
 							->join('users','users.id','=','transaction.user_id')	
 							->where('users.organization_id','=',$id)
                            ->get();
            	$this->transaction_sheet($sheet,$transaction);                
            });
            $excel->sheet('Transaction Details', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('transaction_details')
 							->join('event','event.id','=','transaction_details.event_id')	
 							->where('event.organization_id','=',$id)
                            ->get();
            	$this->transaction_details_sheet($sheet,$transaction);                
            });
            $excel->sheet('User Role', function($sheet) use ($slug,$id){
 				$transaction =  \DB::table('user_roles')
 							->join('users','users.id','=','user_roles.user_id')	
 							->where('users.organization_id','=',$id)
                            ->get();
            	$this->user_roles_sheet($sheet,$transaction);                
            });
            $excel->sheet('Volunteers', function($sheet) use ($slug,$id){
            	$transaction =  \DB::table('volunteers')
            	->join('volunteer_groups','volunteer_groups.id','=','volunteers.volunteer_group_id')	
            	->join('event','event.id','=','volunteer_groups.event_id')	
            	->where('event.organization_id','=',$id)
            	->get();
            	$this->volunteers_sheet($sheet,$transaction);                
            });
            $excel->sheet('Volunteer Groups', function($sheet) use ($slug,$id){
            	$transaction =  \DB::table('volunteer_groups')
            	->join('event','event.id','=','volunteer_groups.event_id')	
            	->where('event.organization_id','=',$id)
            	->get();
            	$this->volunteer_groups_sheet($sheet,$transaction);                
            });
            /*$excel->sheet('Quickbooks', function($sheet) use ($slug,$id){
            	$transaction =  \DB::table('cocard_quickbooks_oauth')
            	->where('organization_id','=',$id)
            	->get();
            	$this->quickbooks_sheet($sheet,$transaction);                
            });*/

        })->export('xls');
    }
    public function organization_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->name,
    			$list->contact_person,
    			$list->position,
    			$list->contact_number,
    			$list->email,
    			$list->password,
    			$list->url,
    			$list->language,
    			$list->scheme,
    			$list->logo,
    			$list->banner_image,
    			$list->pending_organization_user_id,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
        if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'Name', 'Contact Person','Position','Contact No.','Email','Password','URL','Language','Scheme','Logo','Banner Image','Pending Organization Id','Status','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function users_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->first_name,
    			$list->last_name,
    			$list->middle_name,
    			$list->address,
    			$list->city,
    			$list->state,
    			$list->zipcode,
    			$list->phone,
    			$list->birthdate,
    			$list->gender,
    			$list->email,
    			$list->password,
    			$list->image,
    			$list->status,
    			$list->locale,
    			$list->api_token,
    			$list->remember_token,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
        if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'Organization Name', 'First Name','Last Name','Middle Name','Address','City','State','Zipcode','Phone','Birthdate','Gender','Email','Password','Image','Status','Locale','API Token','Remember Token','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function activity_log_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->user_id,
    			$list->activity,
    			$list->details,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
        if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'User Id', 'Activity','Details','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function donation_category_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->name,
    			$list->description,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
        if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'Organization Id', 'Name','Description','Status','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function donation_list_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->donation_category_id,
    			$list->name,
    			$list->description,
    			$list->recurring,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
        if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'Donation Category Id', 'Name','Description','Recurring','Status','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function email_groups_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
                    $data[] = array(
                        $list->id,
                        $list->organization_id,
                        $list->name,
                        $list->details,
                        $list->status,
                        $list->created_at,
                        $list->updated_at,
                    );
        }
		if($transaction == null){
            return [];
        }
        $sheet->fromArray($data, null, 'A1', false, false);
        $headings = array('Id', 'Organization Id', 'Name','Details','Status','Created','Updated');
        $sheet->prependRow(1, $headings);
    }
    public function email_group_members_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->email_group_id,
    			$list->user_id,
    			$list->name,
    			$list->email,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
            return [];
        }
	    $sheet->fromArray($data, null, 'A1', false, false);
	    $headings = array('Id', 'Email Group Id', 'User Id','Name','Email','Status','Created','Updated');
	    $sheet->prependRow(1, $headings);
    }
    public function event_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->name,
    			$list->description,
    			$list->capacity,
    			$list->pending,
    			$list->fee,
    			$list->start_date,
    			$list->end_date,
    			$list->reminder_date,
    			$list->volunteer_number,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Organization Id', 'Name','Description','Capacity','Pending','Fee','Start Date','End Date','Reminder Date','Volunteers Needed','Status','Created','Updated');
    	$sheet->prependRow(1, $headings);
    }
    public function family_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->name,
    			$list->description,
                $list->primary_phone,
                $list->secondary_phone,
                $list->primary_email,
    			$list->secondary_email,
                $list->address_1,
                $list->address_2,
                $list->city,
                $list->state,
    			$list->zipcode,
    			$list->created_at,
    			$list->updated_at,                        
    			$list->status,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Organization Id', 'Name','Description','Primary Phone','Secondary Phone','Primary Email','Secondary Email','Address 1','Address 2','City','State','Zipcode','Created','Updated','Status');
    	$sheet->prependRow(1, $headings);
    }
    public function family_members_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->user_id,
    			$list->family_id,
    			$list->first_name,
    			$list->last_name,
    			$list->middle_name,
    			$list->birthdate,
    			$list->gender,
    			$list->allergies,
    			$list->img,
    			$list->relationship,
    			$list->additional_info,
    			$list->child_number,
    			$list->created_at,
    			$list->updated_at,                        
    			$list->status,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'User Id', 'Family Id','First Name','Last Name','Middle Name','Birthdate','Gender','Allergies','Image','Relationship','Child Number','Created','Updated','Status');
    	$sheet->prependRow(1, $headings);
    }
    public function frequency_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->title,
    			$list->description,
    			$list->created_at,
    			$list->updated_at,
    			$list->status,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Title', 'Description','Created','Updated','Status');
    	$sheet->prependRow(1, $headings);
    }
    public function participants_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->user_id,
    			$list->event_id,
    			$list->qty,
    			$list->created_at,
    			$list->updated_at,
    			$list->status,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'User Id', 'Event Id','Qty','Created','Updated','Status');
                $sheet->prependRow(1, $headings);
    }
    public function staff_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->first_name,
    			$list->last_name,
    			$list->email,
                $list->phone,
                $list->title,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
            return [];
        }
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Organization Id', 'First Name', 'Last Name', 'Email', 'Contact Number','Role','Status');
                $sheet->prependRow(1, $headings);
    }
    public function transaction_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->user_id,
    			$list->transaction_key,
    			$list->token,
    			$list->total_amount,    			
    			$list->created_at,
    			$list->updated_at,
    			$list->status,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'User Id', 'Transaction Key', 'Token', 'Total Amount','Created','Updated','Status');
    	$sheet->prependRow(1, $headings);
    }
    public function transaction_details_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->transaction_id,
    			$list->volunteer_id,
    			$list->frequency_id,
    			$list->event_id,    			
    			$list->created_at,
    			$list->updated_at,
    			$list->status,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Transaction Id', 'Volunteer Id', 'Frequency Id', 'Event Id','Created','Updated','Status');
    	$sheet->prependRow(1, $headings);
    }
    public function user_roles_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->role_id,
    			$list->user_id,
    			$list->status,
    			$list->original_user_id,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Role Id', 'User Id', 'Status', 'Origin User Id');
    	$sheet->prependRow(1, $headings);
    }
    public function volunteers_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->user_id,
    			$list->name,
    			$list->email,
    			$list->volunteer_group_id,
    			$list->volunteer_group_status,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'User Id', 'Name','Email','Volunteer Group Id','Volunteer Group Status','Status', 'Created', 'Updated');
    	$sheet->prependRow(1, $headings);
    }
    public function volunteer_groups_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->type,
    			$list->volunteers_needed,
    			$list->note,
    			$list->event_id,
    			$list->status,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Type', 'Volunteers Needed','Note','Event Id','Status', 'Created', 'Updated');
    	$sheet->prependRow(1, $headings);
    }
    public function quickbooks_sheet($sheet,$transaction){
    	foreach($transaction as $list) {
    		$data[] = array(
    			$list->id,
    			$list->organization_id,
    			$list->qb_company_id,
    			$list->qb_consumer_key,
    			$list->qb_token,
    			$list->qb_consumer_secret,
    			$list->oauth_request_token,
    			$list->oauth_request_token_secret,
    			$list->oauth_access_token,
    			$list->oauth_access_token_secret,
    			$list->oauth_verifier,
    			$list->created_at,
    			$list->updated_at,
    			);
    	}
    	if($transaction == null){
    		return [];
    	}
    	$sheet->fromArray($data, null, 'A1', false, false);
    	$headings = array('Id', 'Organization Id', 'Company Id','Consumer Key','Token','Consumer Secret','Request Token','Request Token Secret','Access Token','Access Token Secret','Auth Verifier', 'Created', 'Updated');
    	$sheet->prependRow(1, $headings);
    }
    public function edit($id)
    {
        // TODO: Implement edit() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function delete($id)
    {
        return parent::delete($id); // TODO: Change the autogenerated stub
    }

    public function store(Array $input){
    }

    public function update(array $input, $id){
    }

    public function destroy($id){
    }
    

}