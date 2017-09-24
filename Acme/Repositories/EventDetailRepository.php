<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Event;
use App\EventDetail;
use DB;
use App\ActivityLog;
class EventDetailRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';
 
/**/
    protected $listener;

    public function model(){
        return 'App\Event';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function getEvent($request)
    {
        $query = $this->model;
        if ($request->has('search')) {
            $search = trim($request->input('search'));
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->paginate();
            });
        }
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        return $query->select('event.*')
            ->orderBy('event.'.$order_by, $sort)
            ->paginate();
    }

    public function create(){

        $data['action']                = route('event_store');
        $data['action_name']           = 'Add';

        $data['organization_id']      = 1;
        $data['name']                 = old('name');
        $data['description']          = old('description');
        $data['capacity']             = old('capacity');
        $data['fee']                  = old('fee');
        $data['volunteer']            = old('volunteer');
        $data['start_date']           = old('start_date');
        $data['end_date']             = old('end_date');
        $data['reminder_date']        = old('reminder_date');
        $data['status']               = old('status');

        return $data;
    }

    public function save($request, $id = 0){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $action     = ($id == 0) ? 'event_create' : 'event_edit';

        $input      = $request->except(['_token','confirm']);

        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'name'              => 'required',
            'description'              => 'required',
        ], $messages);

        if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        if($id == 0){
            #$this->model->create($input);

            $event = new Event;
            $event->organization_id                  = 1;
            $event->name                             = $input['name'];
            $event->save();

            $getEvent = $event->orderBy('id','name')->first();

            $event = new EventDetail;
            $event->event_id                         = $getEvent->id;
            $event->description                      = $input['description'];
            $event->capacity                         = $input['capacity'];
            $event->fee                              = $input['fee'];
            $event->volunteer_number                 = $input['volunteer'];
            $event->start_date                       = $input['start_date'];
            $event->end_date                         = $input['end_date'];
            $event->reminder_date                    = $input['reminder_date'];
            $event->save();

            $this->model->orderBy('created_at', 'name');
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];
    }

    public function edit($id){
        $data['action']         = route('event_update', $id);
        $data['action_name']    = 'Edit';
        $event                  = $this->model->find($id);

        $data['name']          = (is_null(old('name'))?$event->title:old('name'));
        $data['status']        = (is_null(old('status'))?$event->status:old('status'));

        return $data;
    }

    /*public function update(array $request, $id){
        $this->model->find($id)->update($request);
    }*/

    public function show($id){
        return $this->model->find($id);
    }


    public function destroy($id){
        $this->model->where('id',$id)->delete();
    }
}
