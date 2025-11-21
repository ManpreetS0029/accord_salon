<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('greator_then_zero', function($attribute, $value, $parameters, $validator) {
            if( is_array($value) )
            {
                foreach ( $value as $val )
                {
                    if ( $val <= 0 || $val == '' )
                    {
                        return false;
                    }
                }
                return true;
            }
            else {
                return ($value != '' && $value > 0);
            }
        });

        Validator::extend('required_if_selected_grator_than_zero', function($attribute, $value, $parameters, $validator) {

             $valueParam = $validator->getData();

             if( $valueParam != '' )
             {
                 return $value > 0 ;
             }
            return  true;

        });




        view()->composer('*', function($view){
            $view_name = str_replace('.', '-', $view->getName());
            view()->share('view_name', $view_name);
         });

      /* Validator::replacer('required_if_selected', function($message, $attribute, $rule, $parameters) {

            return  $parameters[0];//'The '.$attribute.' should greator than zero. '; //$message;
            //return str_replace(':field', $parameters[0], $message);
        });*/
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
