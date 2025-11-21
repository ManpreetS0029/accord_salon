<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 28/08/18
 * Time: 8:52 AM
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class StaffSalaryPaid  extends Model
{
    protected $table = 'staffsalarypaid';

    function staff()
    {
        return $this->belongsTo( 'App\Staff', 'staffid', 'id' );
    }
}
