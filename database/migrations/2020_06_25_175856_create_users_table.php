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
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('email', 130)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('state')->default(true);
            $table->rememberToken();
            $table->string('firm')->nullable()->default('users/firm.png');
            $table->string('image')->nullable()->default('users/profile.png');
            $table->unsignedBigInteger('dependence_id')->nullable();
            $table->foreign('dependence_id')->references('id')->on('dependences');
            $table->unsignedBigInteger('dependencePerson_id')->nullable();
            $table->foreign('dependencePerson_id')->references('id')->on('dependences');
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
