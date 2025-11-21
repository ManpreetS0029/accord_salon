<?php
/**
 * Created by PhpStorm.
 * User: hardeepsingh
 * Date: 26/08/18
 * Time: 11:07 PM
 */
namespace App\CustomClasses;
use App\Client;
use App\ClientPackage;
use App\ClientPackageItems;
use App\ClientPayment;
use App\ClientPaymentUsed;
use App\PaymentMode;
use App\Sale;
use App\SaleItem;
use App\SalePayment;
use App\ServiceDoneStaff;
use App\Services;
use App\Product;
use App\ProductStock;
use App\ProductIssue;
use App\Staff;


use \Illuminate\Support\Facades\DB;

class HS_Staff
{

    //month = 01,02,03
    protected function calculateSalaryWithDays( $presentDays, $officialSalary, $months, $years )
    {

         $monthDays = HS_Common::getMonthDays( $months, $years );

         $officialSalary = $officialSalary == '' ? 0 : $officialSalary;
         $perDaySalary =  $officialSalary / $monthDays;

        $salary = round($perDaySalary * $presentDays);

        return $salary;

    }

    //month = 01,02,03
    public function calculateMonthSalary($staffId, $month, $year)
    {
        $staff = Staff::findOrFail($staffId);
        $staff->month = str_pad($month, 2, '0', STR_PAD_LEFT  );
        $staff->year = $year;
        $listAttendance = $staff->getAttendanceForMonth;
        $salaryIncrementInfo = $staff->salaryIncrementInfo;

        $incrementSalary = '';
        $incrementDate = '';
        foreach ( $salaryIncrementInfo  as $incrementInfo )
        {
            if( date("m", strtotime($incrementInfo->fromdate ) ) <= $month )
            {
                $incrementDate = $incrementInfo->fromdate;
                $incrementSalary = $incrementInfo->salary;
            }
        }

        $absentDays = 0;
        foreach ( $listAttendance as $attendance )
        {
            if( $attendance->attandence == "Absent"   )
            {
                $absentDays += 1;
            }
            else if( $attendance->attandence == "Half Day"  )
            {
                $absentDays += 0.5;
            }

        }
        $calculateSalary = 0;
        if( $absentDays > 0 )
        {
            $calculateSalary = $incrementSalary - ( ($incrementSalary / HS_Common::getMonthDays($month, $year)) * $absentDays ) ;
        }

        return round($calculateSalary);
    }

    function getSalaryAllDetails( $staffId, $tillMonth = '', $tillYear = ''  )
    {

        if( $tillMonth == '' )
        {
            $tillMonth = date("m");
        }

        if( $tillYear == '')
        {
            $tillYear = date("Y");
        }

        $staff = Staff::findOrFail($staffId);
        $salaryIncrements = $staff->salaryIncrementInfo;
        $attandances = $staff->attandances;
        $salaryPaids = $staff->salaryPaids;


        $groupAttendances = array();
        //group attendance days by months and years

        foreach ( $attandances as $attandance )
        {


           $key = date( "Y_m", strtotime( $attandance->attandance_date));
            if( isset( $groupAttendances[$key] ) )
            {
                $groupAttendances[$key] +=  $this->getAttendanceIncrement( $attandance->attandence );
            }
            else
            {
                $groupAttendances[$key] =  $this->getAttendanceIncrement( $attandance->attandence );
            }
        }



  //  $incrementInfoArr =   $this->getOfficialSalaryForMonth( $salaryIncrements, date("m"), date("Y") );
        $arrsalaryPaid = array();

        foreach ( $salaryPaids as $paids )
        {
           $months = str_pad($paids->months,2,'0', STR_PAD_LEFT );
           $keys = $paids->years.'_'.$months;
            if( isset($arrsalaryPaid[$keys] ) )
            {
               $arrsalaryPaid[$keys] += $paids->amount;
            }
            else
            {
                $arrsalaryPaid[$keys] = $paids->amount;
            }

        }
        $arrsalaryInfo = array();

        if( count($salaryIncrements) > 0 ) {

            $startMonth = intval(date("m", strtotime($salaryIncrements[0]->fromdate)));
            $startYear = date("Y", strtotime($salaryIncrements[0]->fromdate));



            while ( ($startMonth <= $tillMonth && $startYear <= $tillYear ) || ($startMonth > $tillMonth && $startYear < $tillYear )  ) {

                  $months = str_pad($startMonth,2,'0', STR_PAD_LEFT );
                 $keys = $startYear.'_'.$months;

                 $incrementInfoArr = $this->getOfficialSalaryForMonth( $salaryIncrements,  $months, $startYear  );


                 $arrsalaryInfo[$keys]['official_salary'] =  $incrementInfoArr['official_salary'];
                 $arrsalaryInfo[$keys]['attendance_count'] = isset($groupAttendances[$keys]) ? $groupAttendances[$keys] : 0 ;

                 $arrsalaryInfo[$keys]['calculated_salary'] = $this->calculateSalaryWithDays( $arrsalaryInfo[$keys]['attendance_count'], $incrementInfoArr['official_salary'], $months, $startYear  );

                 if( isset($arrsalaryInfo[$keys]['paid_amount']  ) )
                 {
                     $arrsalaryInfo[$keys]['paid_amount'] +=  isset($arrsalaryPaid[$keys]) ? $arrsalaryPaid[$keys] : 0;

                 }
                 else
                 {
                     $arrsalaryInfo[$keys]['paid_amount'] = isset($arrsalaryPaid[$keys]) ? $arrsalaryPaid[$keys] : 0;
                 }



                if ($startMonth >= 12) {
                    $startMonth = 0;
                    $startYear++;
                }
                $startMonth++;

            }
        }


        if( count($arrsalaryInfo) > 0 )
        {
            $totalBalance = 0;
            foreach ( $arrsalaryInfo as $key => $val )
            {
                $arrsalaryInfo[$key]['balance_amount'] =  $arrsalaryInfo[$key]['calculated_salary'] - $arrsalaryInfo[$key]['paid_amount'];
                $totalBalance += $arrsalaryInfo[$key]['balance_amount'];
            }

            $arrsalaryMain['results'] =  $arrsalaryInfo;
            $arrsalaryMain['total_balance'] = $totalBalance;
            return $arrsalaryMain;
        }



        return $arrsalaryInfo;
    }


    function getOfficialSalaryForMonth( $salaryIncrementInfo, $months = '', $years = '' )
    {

        $incrementSalary = '';
        $incrementDate = '';
        if( $months == '' )
        {
            $months = date("m");
        }
        if( $years == '')
        {
            $years = date("Y");
        }


        foreach ( $salaryIncrementInfo  as $incrementInfo )
        {
            $salaryMonth = date("m", strtotime($incrementInfo->fromdate ) );
            $salaryYear = date("Y", strtotime($incrementInfo->fromdate ) );
            if( ( $salaryMonth  <= $months  &&    $salaryYear <= $years) ||  ( $salaryMonth > $months && $salaryYear < $years ) )
            {
                $incrementDate = $incrementInfo->fromdate;
                $incrementSalary = $incrementInfo->salary;
            }
        }
        return [ 'official_salary' => $incrementSalary, 'salary_increment_date' => $incrementDate ];

    }

   protected function getAttendanceIncrement( $attendanceType )
    {
        if( $attendanceType == 'Absent')
        {
            return 0;
        }
        else if ( $attendanceType == 'Half Day' )
        {
            return 0.5;
        }

        return 1;
    }
}