<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Groups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db.tables.groups'), function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->string(config('db.fields.name'), '100');
        });

        // Insert some stuff
        DB::table(config('db.tables.groups'))->insert(array(
            array(
                config('db.fields.id') => config('db.values.groups.admin.id'),
                config('db.fields.name') => config('db.values.groups.admin.name')
            ),
            array(
                config('db.fields.id') => config('db.values.groups.worker.id'),
                config('db.fields.name') => config('db.values.groups.worker.name')
            )
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('db.tables.groups'));
    }
}
