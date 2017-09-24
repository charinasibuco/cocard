<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Acme\Repositories\FrequencyRepository;
use App\Frequency;
use Auth;
use App;

class FrequencyController extends Controller
{
    public function __construct(FrequencyRepository $frequency){
        $this->middleware('auth');
        $this->frequency = $frequency;
        $this->auth = Auth::user();   
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }
    public function index(Request $request){
        $data['frequency'] = $this->frequency->getFrequency($request);
        $data['search'] = $request->input('search');
        if($request->type =="json"){
          return $data;
        }
        return view('cocard-church.frequency.index',$data);
    }

    public function create()
    {
        $data = $this->frequency->create();
        return view('cocard-church.frequency.form', $data);
    }
    public function save(Request $request, $id = 0){
        if($request->type =="json"){
          return $data;
        }
        $results = $this->frequency->save($request);
        if($results['status'] == false)
        {
            return redirect()->route('frequency_create')->withErrors($results['results'])->withInput();
        }
        return redirect()->route('frequency')->with('message', 'Successfully Added Page');
    }
    public function edit($id)
    {

       $data = $this->frequency->edit($id);
       return view('cocard-church.frequency.form', $data);
    }

    public function update(Request $request , $id){
        $results = $this->frequency->save($request, $id);
        if($request->type =="json"){
          return $data;
        }
        if($results['status'] == false)
        {
            return redirect()->route('frequency_edit', $id)->withErrors($results['results'])->withInput();
        }
         return redirect()->route('frequency_edit', $id)->with('message', 'Successfully Update Page');
    }

    public function destroy($id)
    {
      $this->frequency->destroy($id);
      return redirect()->route('frequency')->with('status','Frequency successfully deleted!');
    }
}
