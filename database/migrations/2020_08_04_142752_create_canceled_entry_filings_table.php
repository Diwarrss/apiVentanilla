<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanceledEntryFilingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('canceled_entry_filings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->unsignedBigInteger('cancellationReason_id')->nullable();
            $table->foreign('cancellationReason_id')->references('id')->on('cancellation_reasons')->onDelete('cascade');
            $table->unsignedBigInteger('entryFiling_id')->nullable();
            $table->foreign('entryFiling_id')->references('id')->on('entry_filings')->onDelete('cascade');
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
        Schema::dropIfExists('canceled_entry_filings');
    }
}
