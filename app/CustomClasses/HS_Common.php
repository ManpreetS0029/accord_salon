<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 26/08/18
 * Time: 11:10 PM
 */
namespace App\CustomClasses;
use App\PaymentMode;

class HS_Common
{
    static function getMonthDays( $month, $year )
    {
         return  cal_days_in_month(CAL_GREGORIAN, $month, $year );
    }

    static function getPaymentModeDropDownVals($includeSelect = false)
    {
        $paymentModes = PaymentMode::all();

        $paymentModeArr = array();
        $paymentModeArr[null] = 'Select Payment Mode';
        foreach ($paymentModes as $pMode ) {
            $paymentModeArr[$pMode->id] = $pMode->name;
        }
        return $paymentModeArr;
    }

    static function extractDateAsString($dates)
    {
        if( $dates != '' ) {
            $dt = explode( '/', $dates );

            return ( $dt[2].'-'.$dt[1].'-'.$dt[0] );
        }
        return '';
    }

    static function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        // open the "output" stream
        // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
         $f = fopen('php://output', 'w');

          foreach ($array as $line) {
             fputcsv($f, $line, $delimiter);
         } 
    }
}