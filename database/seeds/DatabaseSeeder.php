<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //factory('App\User', 2)->create();
        //$this->call(UsersTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class); //van los usuarios
        $this->call(GendersTableSeeder::class);
        $this->call(ContextTypesTableSeeder::class);
        $this->call(PrioritiesTableSeeder::class);
        $this->call(TypeDocumentsTableSeeder::class);
        $this->call(TypeIdentificationsTableSeeder::class);
        factory('App\LegalRepresentative', 2)->create();
        factory('App\Company', 1)->create();
    }
}
