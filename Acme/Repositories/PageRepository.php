<?php
namespace Acme\Repositories;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\URL;
use Acme\Repositories\Repository;
use Illuminate\Support\Facades\Validator;

class PageRepository extends Repository{

	const LIMIT = 7;

	public function model()
	{
		return 'App\Page';
	}
	public function getPageSlug($slug){
		return $this->model->where('slug', $slug)->first();
	}
/**/
	public function getPage($request = null)
	{
		if($request != null){
			if($request->has('search')){
				return $this->model->where('title', 'LIKE', '%' . $request->input('search'). '%')
					->orWhere('slug', 'LIKE', '%' . $request->input('search') . '%')
					->orWhere('content', 'LIKE', '%' . $request->input('search') . '%')
					->orWhere('status', 'LIKE', '%' . $request->input('search') . '%')
					->select('*')
					->orderBy('order','asc')
					->paginate(self::LIMIT);
			}
		}
		if($request->input('order_by') && $request->input('sort')){
			return $this->model->orderBy($request->input('order_by'), $request->input('sort'))
				->paginate(self::LIMIT);
		}
		return $this->model->orderBy('order','asc')->paginate(self::LIMIT);
	}

	public function create()
	{
		$data['header']     = 'Add';
		$data['page_id']    = 0;
		$data['parent_id']  = old('parent_id');
		$data['title'] 		= old('title');
		$data['content'] 	= old('content');
		$data['slug'] 		= old('slug');
		$data['template'] 	= old('template');
		$data['status'] 	= old('status');
		$data['order']		= old('order');
		$data['meta_title']	= old('meta_title');
		$data['keywords']		= old('keywords');
		$data['description']	= old('description');
		$data['last_id'] 	= '';
		return $data;
	}
	public function edit($id)
	{
		$page 				= $this->model->find($id);
		$data['header']     = 'Edit';
		$data['page_id']    = $id;
		$data['parent_id']	= is_null(old('parent_id'))? $page->parent_id : old('parent_id');
		$data['title']		= is_null(old('title'))? $page->title : old('title');
		$data['content'] 	= is_null(old('content'))? $page->content : old('content');
		$data['slug'] 		= is_null(old('slug'))? $page->slug : old('slug');
		$data['template'] 	= is_null(old('template'))? $page->template : old('template');
		$data['status'] 	= is_null(old('status'))? $page->status : old('status');
		$data['order'] 	    = is_null(old('order'))? $page->order : old('order');
		$data['meta_title']	= is_null(old('meta_title'))? $page->meta_title : old('meta_title');
		$data['keywords']   = is_null(old('keywords'))? $page->keywords : old('keywords');
		$data['description']= is_null(old('description'))? $page->description : old('description');
		$data['last_id']    = $this->model->where('parent_id', $data['parent_id'])->orderBy('id', 'desc')->first()->id;
		return $data;
	}
	// public function update(array $request, $id)
	// {
	// 	$input = $request->all();
	// 	$this->model->update($input);
	// }

	public function save($request, $id = 0){
        $action         = ($id == 0) ? 'page_create' : 'page_update';
        $input          = $request->all();
        $display_slug 	= $input['display_slug'];
        $messages       = ['required'      => 'The :attribute is required'];
//			dd($input);
        $validator      = Validator::make($input, [
			'title' => 'required',
			'slug' => 'required|unique:pages,slug' . ($id ? ",$id" : '')
		], $messages);

        if($validator->fails()){
			 return ['status' => false, 'results' => $validator];
		}

        if($id == 0){
        	// dd($request->all());
           // $pages       = $this->model->create(['parent_id'=>$input['parent_id'], 'title' => $input['title'], 'content' => $input['content'], 'slug' =>$input['slug'],'status' => $input['status'], 'order'=>$input['order'],
            									//'meta_title' => $input['meta_title'], 'keywords' => $input['keywords'], 'description' => $input['description']]);
            // dd($this->model->get());
            $pages = new $this->model;
            $pages->parent_id =	$input['parent_id'];
			$pages->title = 	$input['title'];
			$pages->content = 	$input['content'];
			$pages->slug = 	$input['slug'];
			$pages->status = 	$input['status'];
			$pages->order = 	$input['order'];
			$pages->meta_title = 	$input['meta_title'];
			$pages->keywords = 	$input['keywords'];
			$pages->description = 	$input['description'];
			$pages->save();
			// if($request->destination == 'after'){
			// 	 $filter_pages = $this->model->where('id', '!=', $pages->id)->where('parent_id', $pages->parent_id)->where('order', '>=', $pages->order)->get();
			// 	}else{
			// 		 $filter_pages = $this->model->where('id', '!=', $pages->id)->where('parent_id', $pages->parent_id)
			// 		 				->where('order', '<=', $pages->order)
			// 		 				->where('order', '>=', $pages->order)
			// 		 				->get();
			// 	}
           
   //          foreach ($filter_pages as $filter_page) {
   //          	if($request->destination == 'after'){
   //          		$filter_page->order = $filter_page->order + 1;
   //          	}else{
   //          		$filter_page->order = $filter_page->order - 1;
   //          	}
   //          	$filter_page->save();
   //          }
			$filter_pages = $this->model->where('id', '!=', $pages->id)->where('parent_id', $pages->parent_id)->get();
            foreach ($filter_pages as $filter_page) {
            	if($request->destination == 'after'){
            		if($filter_page->order < $request->old_order)
            		{
            			$filter_page->order = $filter_page->order + 1;
            		}
            		if($filter_page->order >= $pages->order ){
            			$filter_page->order = $filter_page->order + 1;
            		}

            	}else{
            		if($filter_page->order > $request->old_order)
            		{
            			$filter_page->order = $filter_page->order - 1;
            		}
            		if($filter_page->order <= $pages->order ){
            			$filter_page->order = $filter_page->order - 1;
            		}
            	}
            	$filter_page->save();
            }
            // dd($filter_pages);
            return ['status' => true, 'results' => 'Success'];
        }else{
            $pages 				 = $this->model->find($id);
            $up_pages = $this->model->where('id',$input['parent_id'])->first();
            $last_id  = $this->model->orderBy('id','desc')->first()->id;
           // dd($this->model->orderBy('id','desc')->first()->id + 1);
            // dd($up_pages->parent_id);
            //error
            //parent page cant select dropdown
            if(isset($up_pages->parent_id)){
                if($id == $up_pages->parent_id){
                $old_parent_id      = $request->old_parent_id;
                $up_pages->parent_id= $old_parent_id;
                $up_pages->save();
                $pages->id           = $last_id + 1;
                $pages->parent_id    = $id;
                $pages->save();
                $up_pages->id        = $id;
                $up_pages->save();
                $pages->id           = $input['parent_id'];
                $pages->save();
                // $new = $this->model->where('id',$pages->id)->first();
                // $new->parent_id = $id;
                // $new->save();
                // $pages->id           = $up_pages->id;
                // $pages->save();
                //dd('true', $id, $up_pages->parent_id, $old_parent_id);
                }else{
                    $pages->parent_id    = $input['parent_id'];
                }

            
            if($request->old_parent_id == $up_pages->parent_id || $up_pages->id > $id){
            	$pages->id 			 = $last_id + 1;
            	$pages->parent_id    = $id;
            	$pages->save();

            	$up_pages->id 		 = $id;
            	$up_pages->save();

            	$pages->id 			 = $input['parent_id'];
            	$pages->save();
            }
        }
            // dd('break');
            $pages->title        = $input['title'];
            $pages->content      = $input['content'];
            $pages->slug         = $display_slug.$input['slug'];

            $pages->order        = $input['order'];
            $pages->meta_title   = $input['meta_title'];
            $pages->keywords     = $input['keywords'];
            $pages->description  = $input['description'];
            $pages->status 	     = $request->status;
            $status = $this->model->where('id',$pages->parent_id)->first();
            $pages->save();
            $filter_pages = $this->model->where('id', '!=', $pages->id)->where('parent_id', $pages->parent_id)->get();
            foreach ($filter_pages as $filter_page) {
            	if($request->destination == 'after'){
            		if($filter_page->order < $request->old_order)
            		{
            			$filter_page->order = $filter_page->order + 1;
            		}
            		if($filter_page->order >= $pages->order ){
            			$filter_page->order = $filter_page->order + 1;
            		}

            	}else{
            		if($filter_page->order > $request->old_order)
            		{
            			$filter_page->order = $filter_page->order - 1;
            		}
            		if($filter_page->order <= $pages->order ){
            			$filter_page->order = $filter_page->order - 1;
            		}
            	}
            	$filter_page->save();
            }
        }
        return ['status' => true, 'results' => 'Success'];
    }

	public function getMenu($request, $id = 0 ){
		$url			= URL::to('/') != $request->url() ? str_replace(URL::to('/') . '/', '', $request->url()) : URL::to('/');
//		$url			= URL::to('/');

		$pages 			= $this->model->where('parent_id', $id)->where('status', 'published')->orderBy('order', 'asc');
		$results		= [];
		foreach($pages->get() as $page){
			$p 					= new \stdClass();
			$p->title			= $page->title;
			$p->slug			= $page->slug;
			$p->active			= ($url == $page->slug) ? true : false;
//			$p->url				= $url;
			$p->url				= URL::to('/') . '/'. $page->slug;
			$p->children		= $this->getMenu($request, $page->id);
			$results[]	= $p;
		}

		return $results;
	}

	public function delete($id)
	{
		return $this->model->find($id)->delete();
	}
}
