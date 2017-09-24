<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table 	= 'pages';
    protected $fillable	= ['parent_id', 'title','slug','content','status','template', 'order'];

    public function parent(){
        return $this->belongsTo(Page::class, 'parent_id', 'id');
    }
}
