<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalFilingHasDependencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_filing_has_dependences', function (Blueprint $table) {
          $table->unsignedBigInteger('internal_filing_id');
          $table->foreign('internal_filing_id')->references('id')->on('internal_filings')->onDelete('cascade');
          $table->unsignedBigInteger('dependence_id');
          $table->foreign('dependence_id')->references('id')->on('dependences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_filing_has_dependences');
    }
}
