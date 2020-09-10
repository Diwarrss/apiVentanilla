<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('up_files', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->integer('type');
        $table->text('url');
        $table->morphs('fileable'); //Crea 2 campos el fileable_type(Modelo), fileable_id(id del dato)
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
        Schema::dropIfExists('up_files');
    }
}
