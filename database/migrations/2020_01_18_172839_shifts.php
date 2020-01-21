<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Shifts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db.tables.shifts'), function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->boolean(config('db.fields.monday'));
            $table->boolean(config('db.fields.tuesday'));
            $table->boolean(config('db.fields.wednesday'));
            $table->boolean(config('db.fields.thursday'));
            $table->boolean(config('db.fields.friday'));
            $table->boolean(config('db.fields.saturday'));
            $table->boolean(config('db.fields.sunday'));
            $table->double(config('db.fields.start'), 4,2);
            $table->double(config('db.fields.end'), 4,2);
            $table->double(config('db.fields.pay_per_hour'), 4,2);
            $table->timestamps();
            $table->bigInteger(config('db.fields.job_id'))->unsigned();
            $table->foreign(config('db.fields.job_id'))->references(config('db.fields.id'))->on(config('db.tables.jobs'));
        });

        Schema::table(config('db.tables.users'), function (Blueprint $table) {
            $table->foreign(config('db.fields.shift_id'))->references(config('db.fields.id'))->on(config('db.tables.shifts'));
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
