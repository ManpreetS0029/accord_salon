<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    protected $table = 'packagemaster';
    //
    protected $fillable = ['title','description','price'];

    public function packageservices()
    {
        return $this->hasMany('App\PackagesServices', 'packageid');
    }

    public function getDiscountAmount( $discountType, $discountValue )
    {

        $discountAmount = 0;
        if( $discountType != '' &&  $discountValue > 0  )
        {
            if( $discountType == 'percent' )
            {
                $discountAmount = $this->price * $discountValue / 100 ;
            }
            else
            {
                $discountAmount = $discountValue;
            }
                             
        }

        return $discountAmount;
    }
}
