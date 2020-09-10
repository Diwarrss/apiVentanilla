<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identification', 15)->unique();
            $table->string('names');
            $table->string('telephone', 11)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('email', 130)->nullable();
            $table->boolean('state');
            $table->enum('type', ["company","person"]);
            $table->integer('user_id')->nullable()->comment('Usuario que crea el registro');
            $table->unsignedBigInteger('type_identification_id')->nullable();
            $table->foreign('type_identification_id')->references('id')->on('type_identifications')->onDelete('cascade');
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
            $table->unsignedBigInteger('people_id')->nullable();
            $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
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
        Schema::dropIfExists('people');
    }
}
