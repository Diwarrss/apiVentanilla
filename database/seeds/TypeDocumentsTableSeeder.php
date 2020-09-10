<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TypeDocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('type_documents')->insert(
        [
          [
            'name' => 'ACTAS',
            'slug' => Str::slug('ACTAS','-'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'ACUERDOS',
            'slug' => Str::slug('ACUERDOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CARTIFICACIONES',
            'slug' => Str::slug('CARTIFICACIONES'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CIRCULARES',
            'slug' => Str::slug('CIRCULARES'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CONCEPTOS',
            'slug' => Str::slug('CONCEPTOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CONCILICION BANCARIA',
            'slug' => Str::slug('CONCILICION BANCARIA'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CONTRATO',
            'slug' => Str::slug('CONTRATO'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CONTROL RESIDUOS GENERADOS',
            'slug' => Str::slug('CONTROL RESIDUOS GENERADOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'CONVENIOS',
            'slug' => Str::slug('CONVENIOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'PERMANENCIAS',
            'slug' => Str::slug('PERMANENCIAS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'OFICIOS',
            'slug' => Str::slug('OFICIOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'PROYECTOS DE ACUERDO',
            'slug' => Str::slug('PROYECTOS DE ACUERDO'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'RESOLUCIONES',
            'slug' => Str::slug('RESOLUCIONES'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'SUPERVISIONES',
            'slug' => Str::slug('SUPERVISIONES'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'FACTURAS',
            'slug' => Str::slug('FACTURAS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'JURIDICOS',
            'slug' => Str::slug('JURIDICOS'),
            'state' => true,
            'created_at' => now(),
            'updated_at' => now()
          ],
        ]
      );
    }
}
