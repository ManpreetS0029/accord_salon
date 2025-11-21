<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductIssue extends Model
{
    //
    protected $table = 'productissueto';
    protected $fillable =  ['barcode', 'productid', 'qnty', 'productname', 'staffid', 'staffname', 'issuedate', 'remarks'];

    public function product()
    {
        return $this->hasOne( 'App\Product', 'id', 'productid');
    }

    public function staff()
    {
        return $this->hasOne( 'App\Staff', 'id', 'staffid');
    }
}
