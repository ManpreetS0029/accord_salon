<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    //

    protected $table = 'staffmembers';
    //
    protected $fillable = ['firstname', 'lastname', 'dob', 'gender', 'address', 'mobile', 'phone', 'designation', 'hiringdate', 'idprooftype', 'idproofvalue', 'activestatus'];


    public function getName()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function salaryPaids()
    {
        return $this->hasMany('App\StaffSalaryPaid',  'staffid', 'id' )->orderBy('created_at', 'asc');
    }

    public function attandances()
    {
        return $this->hasMany('App\Attendance', 'staffid', 'id')->orderBy('attandance_date', 'asc');
    }

    public function salaryIncrementInfo()
    {

       return $this->hasMany('App\StaffSalaryIncrement', 'staffid', 'id')->orderBy('fromdate', 'asc');

    }
    public function getAttendanceForMonth()
    {
        if( $this->month && $this->year ) {
            return $this->hasMany('App\Attendance', 'staffid', 'id')->whereMonth('attandance_date', '=', $this->month )->whereYear('attandance_date', '=', $this->year )->orderBy('attandance_date', 'desc');
        }
        else
        {
            return $this->hasMany('App\Attendance', 'staffid', 'id')->orderBy('attandance_date', 'desc');
        }

    }
    
    public function getAttendanceForDate($dates)
    {
        $attenDances = $this->attandances;
        foreach( $attenDances as $attendance ) {
            if( $attendance->attandance_date == $dates )
            {
                return $attendance;
            }
  
        }

        return false;
    }


    
    public function getLatestSalary()
    {
        $salaries = $this->salaryPaids;
        if( count($salaries) > 0 )
        {
            return $salaries[count($salaries)-1];
        }

        return array();
         /*foreach( $salaries as $salary ) {

            return $salary;
        } */
        

    }




}
