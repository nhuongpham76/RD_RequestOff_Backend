<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->increments('id');
            $table->integer('team_id')->index()->unsigned();
            $table->string('code')->index();
            $table->string('name')->index();
            $table->string('address');
            $table->string('password');
            $table->string('phone');
            $table->string('email')->index()->unique();
            $table->smallInteger('status')->index()->default(1);
            $table->smallInteger('role')->index()->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
