<?php

namespace App\Http\Middleware;

use App\CustomClasses\HS_Revenue;
use Closure;

class ClosingDay
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $dt = date("Y-m-d");

        $hsRevenue = new HS_Revenue();

        $hsRevenue->addOpeningBalanceForDate( $dt , $shouldUpdateClosingBalance = '0' );
         $openingBalance = $hsRevenue->isOpeningBalanceAddedForDate($dt);
        if( false !== $openingBalance )
        {

            
            $request->attributes->add( [ 'openingBalance' => $openingBalance ]);

        }
        else
        {

            $request->attributes->add( [ 'openingBalance' => 'not' ]);

        }



         return $next( $request );

    }
}
