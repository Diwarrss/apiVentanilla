<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContextTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('context_types')->insert(
        [
          [
            'name' => 'JURIDICO/ADMINISTRATIVO',
            'slug' => Str::slug('JURIDICO/ADMINISTRATIVO','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'DOCUMENTAL',
            'slug' => Str::slug('DOCUMENTAL','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'DE PROCEDENCIA',
            'slug' => Str::slug('DE PROCEDENCIA','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'PROCEDIMENTAL',
            'slug' => Str::slug('PROCEDIMENTAL','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'TECNOLÓGICO',
            'slug' => Str::slug('TECNOLÓGICO','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
