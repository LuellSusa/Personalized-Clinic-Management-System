<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@titaclinic.test',
        ], [
            'first_name' => 'Clinic',
            'last_name' => 'Administrator',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
