<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements(config('db.fields.id'));
            $table->string(config('db.fields.name'));
            $table->string(config('db.fields.email'))->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string(config('db.fields.password'));
            $table->rememberToken();
            $table->timestamps();
            $table->bigInteger(config('db.fields.group_id'))->unsigned()->default(config('db.values.groups.worker.id'));
            $table->bigInteger(config('db.fields.shift_id'))->unsigned()->nullable()->unique();
            $table->foreign(config('db.fields.group_id'))->references(config('db.fields.id'))->on(config('db.tables.groups'));
        });

        // Insert some stuff
        DB::table(config('db.tables.users'))->insert(array(
            array(
                config('db.fields.id') => 1,
                config('db.fields.name') => 'admin',
                config('db.fields.email') => 'admin@mail.com',
                config('db.fields.password') => Hash::make('password'),
                config('db.fields.group_id') => config('db.values.groups.admin.id'),
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
        Schema::dropIfExists('users');
    }
}
