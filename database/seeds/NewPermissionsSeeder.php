<?php

use App\Permission;
use Illuminate\Database\Seeder;

class NewPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $printStamp = Permission::create([
        'name' => 'print_stamp',
        'guard_name' => 'web',
        'title' => 'imprmir_sello_radicado'
      ]);

      $newPersonFromSettled = Permission::create([
        'name' => 'new_person_from_settled',
        'guard_name' => 'web',
        'title' => 'crear_persona_desde_radicado'
      ]);
    }
}
