<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddAnswerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('permissions')->insert(
        [
          [
            'name' => 'answer_filing',
            'guard_name' => 'web',
            'title' => 'responder_radicacion',
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
