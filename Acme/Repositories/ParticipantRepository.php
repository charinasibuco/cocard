<?php
namespace Acme\Repositories;
use App\Http\Requests\Request;
use Acme\Repositories\Repository;
use Illuminate\Support\Facades\Validator;
use App\Participant;
use App\Event;
use Carbon\Carbon;
use DB;
use Acme\Common\Pagination as Pagination;
use Acme\Common\Constants as Constants;
use Acme\Common\DataFields as DataFields;

class ParticipantRepository extends Repository{


	public function model(){
		return 'App\Participant';
	}

    use Pagination;

/**/
//get participants

	public function getParticipant($request, $id)
    {
        //dd($request);
        $this->SetPage($request);

        $query = $this->model->where('user_id', '=', $id)
        		->leftJoin('event as e','e.id','=','participants.event_id');
        if ($request->has(Constants::KEYWORD)) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->select('event.name')->from('event')->where('event.name', 'LIKE', '%' . $search . '%')
                    ->orderBy('event.created_at', 'desc');
            })
            ->orWhere('participants.qty', 'LIKE', '%' . $search . '%');
        }

        $order_by   =  $this->SortBy;
        $sort       =  $this->SortOrder;

        return $query->select('participants.*',
                        "e.name as event_name",
                        "e.fee",
                        "e.recurring",
                        "e.recurring_end_date",
                        DB::raw("(e.fee*participants.qty) as total_amount"),
                        DB::raw('
                        (
                            CASE WHEN (e.recurring <> 0 AND e.recurring_end_date = "0000-00-00 00:00:00") THEN
                                (CASE
                                WHEN e.recurring = 1 THEN  DATE_ADD(e.start_date, INTERVAL e.no_of_repetition WEEK) 
                                WHEN e.recurring = 2 THEN  DATE_ADD(e.start_date, INTERVAL e.no_of_repetition MONTH ) 
                                WHEN e.recurring = 3 THEN  DATE_ADD(e.start_date, INTERVAL e.no_of_repetition YEAR  )
                                ELSE e.start_date END)
                            WHEN (e.recurring = 0 AND e.recurring_end_date = "0000-00-00 00:00:00") THEN
                                e.start_date
                            ELSE e.recurring_end_date END
                        ) as base_end_date')
                        )
            ->orderBy('participants.created_at', 'desc')
            ->paginate($this->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->PageIndex);
    }
    //selecting all participants
    public function getParticipantAll($request, $id)
    {

        $query = $this->model->where('user_id', '=', $id)
        		->leftjoin('event','event.id','=','participants.event_id');
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->select('name')->from('event')->where('event.name', 'LIKE', '%' . $search . '%')
                    ->orderBy('event.created_at', 'desc')
                    ->get();
            })
            ->orWhere('participants.qty', 'LIKE', '%' . $search . '%');
        }

        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('participants.name as participant_name',
                            'participants.email as participant_email',
                            'participants.qty as participant_qty',
                            'participants.start_date as participant_start_date',
                            'participants.end_date as participant_end_date',
                            'participants.occurence as participant_occurence',
                            'participants.created_at as participant_created_at',
                            'event.name as event_name',
                            'event.fee as event_fee',
                            'event.no_of_repetition as no_of_repetition',
                            'event.recurring as event_recurring')
            ->orderBy('participants.created_at', 'desc')
            ->get();
        
    }

    //saving of participants
	public function save($request){
		// $input = $request->all();
		// $this->model->create($input);
        //dd($request);
        $request->start_date = Carbon::parse($request->start_date)->format("Y-m-d");
        $request->end_date = Carbon::parse($request->end_date)->format("Y-m-d");
		$event 	= Event::where('id', $request->event_id)->first();
        //dd($event,$request);
		if(!empty($event))
		{
			$available = $event->capacity - $event->pending;

			//if($available > 0)
			//{
				//if already paid
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$participant = new Participant;
				$participant->user_id 	        = $request->user_id;
				$participant->event_id 	        = $request->event_id;
				$participant->qty 		        = $request->qty;
                $participant->name              = $request->participant_name;
                $participant->start_date        = $request->start_date;
                $participant->end_date          = $request->end_date;
				$participant->occurence 		= $request->occurence;
				$participant->email 	        = $request->email;
				$participant->save();

				// $pending = $event->pending + $request->qty;
				// return Event::where('id', $request->event_id)->update(['pending' => $pending]);
				#dd($request->all());
				#return ['status' => true, 'results' => 'Successfully Send Request'];
			//}
		}
		#return ['status' => false, 'results' => 'No available space for this Event'];
	}
    public function getEventPerOccurence($request, $id)
    {
        $this->SetPage($request);

        $query = $this->model->where('event_id', '=', $id);
        
        return $query->select('*')
            ->paginate($this->pagination->PageSize,[Constants::SYMBOL_ALL],
                        Constants::PAGE_INDEX,
                        $this->pagination->PageIndex);
    }
	 public function create(){
	 	//
    }
    public function edit($id){
    	//
    }

    public function destroy($id){

       //
    }
}