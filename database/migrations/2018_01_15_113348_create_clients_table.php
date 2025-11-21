<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('client', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clientname');
            $table->text('description');

            $table->date('dob');
            $table->string('email', 50);
            $table->string('address');
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('zipcode', 20);
            $table->string('phone', 50);
            $table->string('phone2', 50);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
