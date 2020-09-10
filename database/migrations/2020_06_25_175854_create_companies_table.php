<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('slug')->nullable();
            $table->string('initials', 8);
            $table->string('nit');
            $table->string('address', 100);
            $table->string('phone', 22);
            $table->text('image');
            $table->text('logo');
            $table->boolean('state');
            $table->enum('type', ["basic","professional","avanced"]);
            $table->unsignedBigInteger('legal_representative_id');
            $table->foreign('legal_representative_id')->references('id')->on('legal_representatives')->onDelete('cascade');
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
        Schema::dropIfExists('companies');
    }
}
