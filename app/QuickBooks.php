<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuickBooks extends Model
{
    protected $table = "cocard_quickbooks_oauth";
    protected $fillable = [
        "organization_id",
        "qb_company_id",
        "qb_token",
        "qb_consumer_key",
        "qb_consumer_secret",
        "oauth_request_token",
        "oauth_request_token_secret",
        "oauth_access_token",
        "oauth_access_token_secret",
        "qb_sandbox_company_id"
];

    public function getFillable(){
        return $this->fillable;
    }
    public function Organization(){
        $this->belongsTo(Organization::class);
    }
}
