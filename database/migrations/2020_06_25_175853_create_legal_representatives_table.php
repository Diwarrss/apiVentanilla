<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalRepresentativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_representatives', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('document');
            $table->string('name', 100);
            $table->string('phone', 22);
            $table->string('address', 100);
            $table->string('email', 130);
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
        Schema::dropIfExists('legal_representatives');
    }
}
