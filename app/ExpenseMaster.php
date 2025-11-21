<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseMaster extends Model
{
    //
    protected $table = 'expensesmaster';
    protected $fillable = ['name'];
}
