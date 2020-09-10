<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('priorities')->insert(
        [
          [
            'name' => 'ALTA',
            'initials' => 'A',
            'state' => true,
            'days' => '3',
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'MEDIA',
            'initials' => 'M',
            'state' => true,
            'days' => '5',
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'BAJA',
            'initials' => 'B',
            'state' => true,
            'days' => '7',
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
