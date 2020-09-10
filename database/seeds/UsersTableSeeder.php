<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /* DB::table('users')->insert([
        'username' => 'Diwarrs',
        'email' => 'diego@soho.cl',
        'password' => Hash::make('123456789'),
        'created_at' => now(),
        'updated_at' => now()
      ]); */
    }
}
