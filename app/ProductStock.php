<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $table = 'productstock';
    //
    protected $fillable = ['productid', 'quantity'];

    public function product()
    {
        return $this->belongsTo('App\Product', 'id','productid' );
    }
}
