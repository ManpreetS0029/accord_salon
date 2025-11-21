<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    //
    protected $fillable = ['name', 'description', 'price', 'startstock', 'status'];

    public function stock()
    {
        return $this->hasMany( 'App\ProductStock', 'productid', 'id' );
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
