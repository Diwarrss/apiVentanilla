<?php

use Illuminate\Database\Seeder;

class TypePeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('type_people')->insert(
        [
          [
            'name' => 'DEPENDENCIA',
            'type' => false,
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'FUNCIONARIO',
            'type' => true,
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'EXTERNO',
            'type' => true,
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
