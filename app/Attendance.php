<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //

    protected $table = 'staffattandence';
    //
    // protected $fillable = ['firstname', 'lastname', 'dob', 'gender', 'address', 'mobile', 'phone', 'designation', 'hiringdate', 'idprooftype', 'idproofvalue'];

    public function staff()
    {
        return $this->belongsTo('App\Staff', 'staffid', 'id');
        //        return $this->hasMany('App\Salary',  'staffid', 'id' )->orderBy('fromdate', 'desc');
    }

}
