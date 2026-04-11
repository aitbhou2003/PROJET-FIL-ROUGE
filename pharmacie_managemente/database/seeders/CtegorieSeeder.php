<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CtegorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Categorie::create(['nom' => 'Analgésiques ']);
        Categorie::create(['nom' => 'Antibiotiques ']);
        Categorie::create(['nom' => 'Anti-inflammatoires']);
        Categorie::create(['nom' => 'Antihypertenseurs ']);
        Categorie::create(['nom' => 'Antidiabétiques ']);
        Categorie::create(['nom' => 'Antiviraux ']);
    }
}
