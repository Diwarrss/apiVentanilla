<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingFilingHasPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_filing_has_people', function (Blueprint $table) {
          $table->unsignedBigInteger('outgoing_filing_id');
          $table->foreign('outgoing_filing_id')->references('id')->on('outgoing_filings')->onDelete('cascade');
          $table->unsignedBigInteger('people_id');
          $table->foreign('people_id')->references('id')->on('people')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outgoig_filing_has_people');
    }
}
