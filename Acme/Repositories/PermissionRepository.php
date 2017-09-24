<?php
namespace Acme\Repositories;
use Acme\Repositories\Repository;
use Illuminate\Support\Facades\Validator;

class PermissionRepository extends Repository{
    const LIMIT = 25;

    protected $listener;

    public function model(){
        return 'App\Permission';
    }

    public function setListener($listener){
        $this->listener = $listener;
    }

    public function getAdminPermission(){
        return $this->model->join('role_permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                            ->where('role_permissions.role_id', '2')
                            ->get();
    }
/**/
    public function getPermission($request){
        if($request != null){
            if($request->has('search')){
                return  $this->model->where('title', 'LIKE', '%' . $request->input('search'). '%')
                    ->orWhere('description', 'LIKE', '%' . $request->input('search') . '%')
                    ->select('title','description')
                    ->orderBy('title', $request->input('sort'))
                    ->paginate(self::LIMIT);
            }
        
        if($request->input('order_by') && $request->input('sort')){
            return $this->model->orderBy($request->input('order_by'), $request->input('sort'))->paginate(self::LIMIT);
            }
        }
        return $this->model->paginate(self::LIMIT);
    }

    public function getPermissionToRole(){
        return $this->model->get();
    }

    public function create(){
        $data['title']     = old('title');
        $data['description']    = old('description');
         return $data;   
    }

    public function save($request, $id = 0){
        $action         = ($id == 0) ? 'permission_add' : 'permission_edit';
        $input          = $request->all();
        $messages       = ['required'      => 'The :attribute is required'];

        $validator      = Validator::make($input, ['name' => 'required','label' => 'required',], $messages);

        if ($validator->fails()) { return $this->listener->failed($validator, $action);}

        if($id == 0){
            $this->model->create(['name' => $input['name'], 'label' => $input['label']]);
            $this->listener->setMessage('Permission is successfully created!');
        }else{
            $this->model->find($id)->update(['name' => $input['name'], 'label' => $input['label']]);
            $this->listener->setMessage('Permission is successfully updated!');
        }

        return $this->listener->passed($action);

    }

    public function edit($id){

        $permission         = $this->model->find($id);
        $data['name']       = $permission->name;
        $data['label']      = (is_null(old('label'))?$permission->label:old('label'));

        return $data;
    }


    public function destroy($id){
        return $this->model->find($id)->delete();
    }

}