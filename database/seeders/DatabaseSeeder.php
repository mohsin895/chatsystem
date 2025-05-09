<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'email' => 'admin@example.com',
            'password'=> Hash::make(123456789),
            'email_verified_at' => Carbon::now(),
            'role' =>1,
        ]);
         User::factory()->create([
            'name' => 'user',
            'email' => 'user1@example.com',
            'password'=> Hash::make(123456789),
            'email_verified_at' => Carbon::now(),
            'role'=>2,
        ]);
    }
}
