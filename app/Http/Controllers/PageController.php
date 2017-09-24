<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Acme\Repositories\PageRepository;
use App\Http\Requests;
use Gate;
use App\Page;
use Auth;
use App;
class PageController extends Controller
{
    public function __construct(PageRepository $page){
        $this->middleware('auth');
        $this->page = $page;
        $this->auth = Auth::user();
        if($this->auth != null){
          App::setLocale($this->auth->locale);   
        }
    }
   public function index(Request $request)
   {
    if(Gate::denies('view_pages'))
    {
        return view('errors.errorpage');
    }
    else
    {
        $data['pages'] = $this->page->getPage($request);
        $data['search']= $request->input('search');
        return view('cocard-church.page.index', $data);
    }
   }
   public function create(Request $request)
   {
    if(Gate::denies('add_page'))
    {
        return view('errors.errorpage');
    }
    else
    {
        $data = $this->page->create();
        $data['pages'] = $this->page->getPage($request);
        $data['action'] = route('page_store');
        return view('cocard-church.page.form',$data);
    }
   }
   public function store(Request $request)
    {
        $results = $this->page->save($request);
        if($results['status'] == false)
        {
            return redirect()->route('page_create')->withErrors($results['results'])->withInput();
        }
        return redirect()->route('page')->with('message', 'Successfully Added Page');
    }
    public function edit(Request $request, $id)
    {
        if(Gate::denies('edit_page'))
        {
            return view('errors.errorpage');
        }
        else
        {
            $data = $this->page->edit($id);
            $data['pages'] = $this->page->getPage($request);
            $data['action'] = route('page_update', $id);
            return view('cocard-church.page.form', $data);
        }
    }
    public function update(Request $request, $id)
    {
        #dd($request->all());
        $results = $this->page->save($request, $id);
        if($results['status'] == false)
        {
            return redirect()->route('page_edit', $id)->withErrors($results['results'])->withInput();
        }
         return redirect()->route('page', $id)->with('message', 'Successfully Update Page');
        #dd($results);
    }
    public function destroy(Request $request, $id)
    {
        if(Gate::denies('delete_page')){
            return view('errors.errorpage');
        }
        else{
            $this->page->delete($id);
            Page::select('id')->where('parent_id', $id)->delete($id);
            return redirect()->route('page')->with('message', 'Successfully Deleted');
        }
    }
    public function filterPages(Request $request){
        $parent_id = $request->parent_id;
        $page =  Page::where('parent_id', $parent_id)->orderBy('order','asc')->get();
        return json_encode($page);
    }
    public function allPages(Request $request){
       $pages = Page::get();
       return json_encode($pages); 
    }
}
