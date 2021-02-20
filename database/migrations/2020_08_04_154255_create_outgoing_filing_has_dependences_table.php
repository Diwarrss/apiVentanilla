<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingFilingHasDependencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_filing_has_dependences', function (Blueprint $table) {
          $table->unsignedBigInteger('outgoing_filing_id');
          $table->foreign('outgoing_filing_id')->references('id')->on('outgoing_filings')->onDelete('cascade');
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
        Schema::dropIfExists('outgoing_filing_has_dependences');
    }
}
