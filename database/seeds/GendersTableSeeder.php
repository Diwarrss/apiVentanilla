<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GendersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('genders')->insert(
        [
          [
            'name' => 'HOMBRE',
            'initials' => 'H',
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'MUJER',
            'initials' => 'M',
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
