<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntryFilingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_filings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->integer('cons_year');
            $table->integer('year');
            $table->string('title', 150);
            $table->string('slug')->nullable();
            $table->string('settled', 20);
            $table->enum('access_level', ["public","restricted"]);
            $table->enum('means_document', ["fisic","digital","fisic\/digital"]);
            $table->integer('folios');
            $table->integer('annexes')->nullable();
            $table->text('subject');
            $table->string('key_words', 200);
            $table->text('attachments')->nullable();
            $table->boolean('state');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('campus_id')->nullable();
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->unsignedBigInteger('priority_id');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->unsignedBigInteger('dependence_id');
            $table->foreign('dependence_id')->references('id')->on('dependences')->onDelete('cascade');
            $table->unsignedBigInteger('type_document_id');
            $table->foreign('type_document_id')->references('id')->on('type_documents')->onDelete('cascade');
            $table->unsignedBigInteger('context_type_id');
            $table->foreign('context_type_id')->references('id')->on('context_types')->onDelete('cascade');
            /* $table->unsignedBigInteger('dependence_id');
            $table->foreign('dependence_id')->references('id')->on('dependences')->onDelete('cascade'); */
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
        Schema::dropIfExists('entry_filings');
    }
}
