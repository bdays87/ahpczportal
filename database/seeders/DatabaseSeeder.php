<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'surname' => 'admin',
            'password' => 'admin',
            'email' => 'admin@admin.com',
            'phone' => '123456789',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}
