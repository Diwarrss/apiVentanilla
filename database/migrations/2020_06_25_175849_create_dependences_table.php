<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identification', 20)->unique()->nullable();
            $table->string('names');
            $table->string('slug')->nullable();
            $table->string('telephone', 11)->nullable();
            $table->string('address')->nullable();
            $table->boolean('state');
            $table->enum('type', ["dependence","person"]);
            $table->text('attachments')->nullable();
            $table->unsignedBigInteger('dependence_id')->nullable();
            $table->foreign('dependence_id')->references('id')->on('dependences')->onDelete('cascade');
            $table->unsignedBigInteger('type_identification_id')->nullable();
            $table->foreign('type_identification_id')->references('id')->on('type_identifications')->onDelete('cascade');
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
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
        Schema::dropIfExists('dependences');
    }
}
