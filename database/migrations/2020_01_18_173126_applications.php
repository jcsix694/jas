<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Applications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db.tables.applications'), function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->timestamps();
            $table->bigInteger(config('db.fields.shift_id'))->unsigned();
            $table->bigInteger(config('db.fields.worker_id'))->unsigned();
            $table->bigInteger(config('db.fields.status_id'))->unsigned();
            $table->bigInteger(config('db.fields.admin_id'))->unsigned()->nullable();
            $table->foreign(config('db.fields.worker_id'))->references(config('db.fields.id'))->on(config('db.tables.users'));
            $table->foreign(config('db.fields.shift_id'))->references(config('db.fields.id'))->on(config('db.tables.jobs'));
            $table->foreign(config('db.fields.status_id'))->references(config('db.fields.id'))->on(config('db.tables.statuses'));
            $table->foreign(config('db.fields.admin_id'))->references(config('db.fields.id'))->on(config('db.tables.users'));

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
