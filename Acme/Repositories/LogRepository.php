<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 6/14/2016
 * Time: 11:36 AM
 */

namespace Acme\Repositories;

use Acme\Repositories\Repository;

class LogRepository extends Repository
{
/**/
    const LIMIT = 50;

    protected $listener;

    public function model()
    {
        // TODO: Implement model() method.
        return 'App\Log';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function getLogs($request, $id = 0){

        $query      = $this->model;
        $order_by   = ($request->input('order_by')) ? $request->input('order_by') : 'id';
        $sort       = ($request->input('sort'))? $request->input('sort') : 'desc';

        if ($request->has('search')) {
            $query->where(function($user) use($request){
                $user->user->where('first_name','LIKE','%'.$request->input(search).'%');
                $user->user->orwhere('last_name','LIKE','%'.$request->input(search).'%');
            });
        }
        if($id == 0){
            return $query->orderBy($order_by, $sort)->paginate(self::LIMIT);
        }

        return $query->where('user_id', $id)->orderBy($order_by, $sort)->paginate(self::LIMIT);
    }

    public function create(){

    }

    public function edit($id)
    {
        // TODO: Implement edit() method.
    }

    public function destroy($id){

    }
}