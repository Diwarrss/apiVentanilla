<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeInTypeDependencesTable extends Migration
{
  public function __construct()
  {
    DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
  }
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::table('dependences', function (Blueprint $table) {
        $table->integer('type')->change();
      });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::table('dependences', function (Blueprint $table) {
        $table->enum('type', ["dependence","person"]);
      });
  }
}
