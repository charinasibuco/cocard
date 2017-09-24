<?php

namespace Acme\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Frequency;

class FrequencyRepository extends Repository{

    const LIMIT                 = 20;
    const INPUT_DATE_FORMAT     = 'Y-m-d';
    const OUTPUT_DATE_FORMAT    = 'F d,Y';

/**/
    protected $listener; 

    public function model(){
        return 'App\Frequency';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function findFrequency($id){
        #dd($this->model->find($id));
        return $this->model->find($id);
    }
    public function getFrequency()
    {
        $query = $this->model->get();

        return $query;
    }
      public function getFrequencyTitle($id)
    {
        $query = $this->model->where('id',$id);
        return $query;
    }

    public function create(){

        $data['action']                = route('frequency_store');
        $data['action_name']           = 'Add';
        $data['title']                 = old('title');
        $data['description']           = old('description');
        $data['status']                = old('status');

        return $data;
    }

    public function save($request, $id = 0){
        $action     = ($id == 0) ? 'frequency_store' : 'frequency_update';

        $input      = $request->except(['_token','confirm']);

        $messages   = [
            'required' => 'The :attribute is required',
        ];
        $validator  = Validator::make($input, [
            'title'              => 'required',
        ], $messages);

        if($validator->fails()){
            #return $this->listener->failed($validator, $action, $id);
            return ['status' => false, 'results' => $validator];
        }

        if($id == 0){
            $this->model->create($input);
            #$this->listener->setMessage('User is successfully created!');
        }else{
            $this->model->where('id',$id)->update($input);
           #$this->listener->setMessage('User is successfully updated!');
        }

        #return $this->listener->passed($action, $id);
        return ['status' => true, 'results' => 'Success'];

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
