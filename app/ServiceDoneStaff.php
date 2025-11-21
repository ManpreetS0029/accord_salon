<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceDoneStaff extends Model
{
    protected $table = 'saleservicedoneby';
    //
    //    protected $fillable = [
    //  'name', 'description', 'status', 'created_at', 'updated_at'
    //];


    public function staff()
    {
        return $this->hasOne('App\Staff', 'staffid', 'id'); //('App\Services','servicecategoriesid');
    }

    public function saleitem()
    {
        return $this->belongsTo("App\SaleItem", 'saleitemid', 'id' );
            //return $this->hasOne('App/Staff', 'staffid', 'id'); //('App\Services','servicecategoriesid');
    }
    
}
