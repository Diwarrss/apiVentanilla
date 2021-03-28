<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddInternalFilingPermissionSeeder extends Seeder
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
            'name' => 'see_based_internal',
            'guard_name' => 'web',
            'title' => 'ver_radicado_interno',
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'create_based_internal',
            'guard_name' => 'web',
            'title' => 'crear_radicado_interno',
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'edit_based_internal',
            'guard_name' => 'web',
            'title' => 'editar_radicado_interno',
            'created_at' => now(),
            'updated_at' => now()
          ],
          [
            'name' => 'change_based_internal_status',
            'guard_name' => 'web',
            'title' => 'cambiar_estado_radicado_interno',
            'created_at' => now(),
            'updated_at' => now()
          ]
        ]
      );
    }
}
