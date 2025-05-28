<?php

namespace Database\Seeders;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Enseignant
        User::create([
            'name' => 'Ahmed Benali',
            'email' => 'enseignant@example.com',
            'password' => Hash::make('password'),
            'role' => 'enseignant',
            'department' => 'Informatique',
            'cin' => 'K123456',
            'phone' => '0612345678',
        ]);

        // Directeur
        User::create([
            'name' => 'Ahmed Mansouri',
            'email' => 'directeur@example.com',
            'password' => Hash::make('password'),
            'role' => 'directeur',
        ]);

        // Chef de département
        User::create([
            'name' => 'Karim Belhaj',
            'email' => 'chef@example.com',
            'password' => Hash::make('password'),
            'role' => 'chef_departement',
            'department' => 'Informatique',
        ]);

        // Comptable
        User::create([
            'name' => 'Youssef Alaoui',
            'email' => 'comptable@example.com',
            'password' => Hash::make('password'),
            'role' => 'comptable',
        ]);
    }
}