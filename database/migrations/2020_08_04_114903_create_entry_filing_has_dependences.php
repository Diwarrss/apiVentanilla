<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryFilingHasDependences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_filing_has_dependences', function (Blueprint $table) {
            $table->unsignedBigInteger('entry_filing_id');
            $table->foreign('entry_filing_id')->references('id')->on('entry_filings')->onDelete('cascade');
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
        Schema::dropIfExists('entry_filing_has_dependences');
    }
}
