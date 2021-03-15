<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldEntryfilingIdOutgoingFilingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('outgoing_filings', function (Blueprint $table) {
          $table->unsignedBigInteger("entry_filing_id")->nullable();
          $table->foreign("entry_filing_id")->references("id")->on("entry_filings");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('outgoing_filings', function (Blueprint $table) {
          $table->dropColumn('entry_filing_id');
        });
    }
}
