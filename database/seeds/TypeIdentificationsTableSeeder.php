<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeIdentificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('type_identifications')->insert([
        [
          'name' => 'REGISTRO CIVIL',
          'initials' => 'RC',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'TARJETA DE IDENTIDAD',
          'initials' => 'TI',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'CÉDULA DE CIUDADANÍA',
          'initials' => 'CC',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'CÉDULA DE EXTRANJERÍA',
          'initials' => 'CE',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'PASAPORTE',
          'initials' => 'PA',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'MENOR SIN IDENTIDAD',
          'initials' => 'MS',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'ADULTO SIN IDENTIDAD',
          'initials' => 'AS',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'NIT',
          'initials' => 'NIT',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'name' => 'CÓDIGO DE OFICINA',
          'initials' => 'CO',
          'state' => true,
          'created_at' => now(),
          'updated_at' => now()
        ]
      ]);
    }
}
