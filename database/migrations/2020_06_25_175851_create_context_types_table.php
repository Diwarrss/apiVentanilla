<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContextTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('context_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 200)->unique();
            $table->string('slug')->nullable();
            $table->boolean('state');
            $table->integer('user_id')->nullable()->comment('Usuario que crea el registro');
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
        Schema::dropIfExists('context_types');
    }
}
