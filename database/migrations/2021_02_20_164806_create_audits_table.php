<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('table', 50);
            $table->enum('action', ["update","disable","enable","canceled","deleteFile","uploadFile"]);
            $table->integer('data_id');
            $table->json('all_data');
            $table->unsignedBigInteger('dependence_id')->nullable();
            $table->foreign('dependence_id')->references('id')->on('dependences')->onDelete('cascade');
            $table->unsignedBigInteger('type_document_id')->nullable();
            $table->foreign('type_document_id')->references('id')->on('type_documents')->onDelete('cascade');
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->unsignedBigInteger('context_type_id')->nullable();
            $table->foreign('context_type_id')->references('id')->on('context_types')->onDelete('cascade');
            $table->unsignedBigInteger('type_identification_id')->nullable();
            $table->foreign('type_identification_id')->references('id')->on('type_identifications')->onDelete('cascade');
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
            $table->unsignedBigInteger('user_table_id')->nullable();
            $table->foreign('user_table_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('entry_filing_id')->nullable();
            $table->foreign('entry_filing_id')->references('id')->on('entry_filings')->onDelete('cascade');
            $table->unsignedBigInteger('outgoing_filing_id')->nullable();
            $table->foreign('outgoing_filing_id')->references('id')->on('outgoing_filings')->onDelete('cascade');
            $table->unsignedBigInteger('cancellation_reason_id')->nullable();
            $table->foreign('cancellation_reason_id')->references('id')->on('cancellation_reasons')->onDelete('cascade');
            $table->unsignedBigInteger('type_people_id')->nullable();
            $table->foreign('type_people_id')->references('id')->on('type_people')->onDelete('cascade');
            $table->Integer('up_file_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('audits');
    }
}
