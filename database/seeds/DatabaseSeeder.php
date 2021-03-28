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
        $this->call(TypePeopleSeeder::class);
        $this->call(AddInternalFilingPermissionSeeder::class);
        $this->call(AddAnswerPermissionSeeder::class);
        $this->call(StateSeeder::class);
        factory('App\LegalRepresentative', 2)->create();
        factory('App\Company', 1)->create();
    }
}
