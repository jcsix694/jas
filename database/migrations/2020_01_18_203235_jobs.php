<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Jobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db.tables.jobs'), function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->string(config('db.fields.name'), '100');
            $table->longText(config('db.fields.description'));
            $table->integer(config('db.fields.no_shifts'));
            $table->timestamps();
            $table->bigInteger(config('db.fields.admin_id'))->unsigned();
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
