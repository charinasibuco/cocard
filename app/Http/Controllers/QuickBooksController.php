<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Acme\Repositories\QuickBooksRepository as QB;
use Auth;

class QuickBooksController extends Controller
{
    public function __construct(QB $quickbooks)
    {
        $this->quickbooks = $quickbooks;
        $this->auth = Auth();
    }



    public function save(Request $request){
       $input = $request->except("_token");
        $input['organization_id'] = $this->auth->user()->organization_id;
        $this->quickbooks->save($input);
        return "true";
    }

}
