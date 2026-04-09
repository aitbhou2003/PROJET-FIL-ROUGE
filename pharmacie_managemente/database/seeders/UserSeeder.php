<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         User::create([
            'firstname' => 'Admin',
            'lastname' => 'Pharmacien',
            'email' => 'admin@pharmacie.com',
            'password' => bcrypt('password'),
            'role_id' => 1, 
            'telephone' => '0612345678',
            'is_actif' => true,
        ]);

         User::create([
            'firstname' => 'Jean',
            'lastname' => 'Employe',
            'email' => 'employe@pharmacie.com',
            'password' => bcrypt('password'),
            'role_id' => 2, 
            'telephone' => '0698765432',
            'is_actif' => true,
        ]);
    }

}
