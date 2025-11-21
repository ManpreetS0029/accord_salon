<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'servicecategories';
    //
    protected $fillable = [
        'name', 'description', 'status', 'created_at', 'updated_at'
    ];


    public function services()
    {
        return $this->hasMany('App\Services','servicecategoriesid');
    }

}
