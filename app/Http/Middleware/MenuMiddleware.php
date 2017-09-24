<?php

namespace App\Http\Middleware;
use Menu;
use Illuminate\Http\Request;
use Closure;
use Auth;
use App\Page;
class MenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Menu::make('FrontMenu', function($menu) use ($request){
            $pages_main = Page::get();
            $pages_head = Page::where('parent_id',0)->get();
            $count      = count($pages_head);
            foreach ($pages_main as $page_main) {
                if(($page_main->parent_id) == 0){
                $order = $page_main->order;
                    if($page_main->status == 'hidden'){
                           Page::where('parent_id',$page_main->id)->update(array('status' => 'hidden')); 
                           $menu->add($page_main->title, $page_main->slug)->data(array('order' => $page_main->order, 'class' => 'hidden'));
                        }
                    else{
                        $menu->add($page_main->title, $page_main->slug)->data(array('order' => $page_main->order));
                    }
                }       
                else{
                    $get_title = Page::select('title')->where('id',$page_main->parent_id)->get();
                    foreach ($get_title as $s) {
                        $title = $s->title;
                    }
                    $parent = lcfirst(preg_replace('/\s+/', '',(ucwords($title))));
                    if($page_main->status == 'published')
                    {
                        //dd($menu, $menu->$parent);
                        $menu->$parent->add($page_main->title, $page_main->slug, $page_main->status)->data('order', $page_main->order); 
                    }
                    else{
                        Page::where('parent_id',$page_main->id)->update(array('status' => 'hidden')); 
                        $menu->add($page_main->title, $page_main->slug)->data(array('order' => $page_main->order, 'class' => 'hidden'));
                    }
                }  
            }
            if (Auth::check()) {
                $menu->add('My Account', ['route' => 'dashboard'])->data('order', $count+2);
            }else{

                // $menu->add('Login', ['route' => 'login'])->data('order', $count+1);
                // $menu->add('Signup', ['route' => 'signup'])->data('order', $count+2);
            }
        })->sortBy('order');
        return $next($request);

    }
}
