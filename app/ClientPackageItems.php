<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientPackageItems extends Model
{
    //
    protected $table = 'tblclientpackageitems';

    public function package()
    {
        return $this->belongsTo( 'App\ClientPackage', 'packageid', 'id' );
    }

}


