<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\PermissionRepository;
use App\Http\Requests;
use App\Permission;
use Auth;
use Gate;
use App;

class PermissionListController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function __construct(PermissionRepository $permission){
        $this->middleware('auth');
        $this->permission = $permission;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);
        }
    }

    public function index(Request $request)
    {
        if(Gate::denies('view_permissions'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data['permission'] = $this->permission->getPermission($request);
            $data['search'] = $request->input('search');
            return view('cocard-church.superadmin.permissionlist', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
