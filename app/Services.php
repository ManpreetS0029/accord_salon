<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Category', 'servicecategoriesid', 'id' );
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
