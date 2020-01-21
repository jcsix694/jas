<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Statuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('db.tables.statuses'), function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->string(config('db.fields.name'), '100');
        });

        // Insert some stuff
        DB::table(config('db.tables.statuses'))->insert(array(
            array(
                config('db.fields.id') => config('db.values.statuses.pending.id'),
                config('db.fields.name') => config('db.values.statuses.pending.name')
            ),
            array(
                config('db.fields.id') => config('db.values.statuses.approved.id'),
                config('db.fields.name') => config('db.values.statuses.approved.name')
            ),
            array(
                config('db.fields.id') => config('db.values.statuses.rejected.id'),
                config('db.fields.name') => config('db.values.statuses.rejected.name')
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
        //
    }
}
