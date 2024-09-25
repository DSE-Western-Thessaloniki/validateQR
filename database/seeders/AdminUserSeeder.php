<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::where('username', 'admin')->count();
        if (!$admin) {
            User::factory()->create([
                'name' => 'Admin',
                'username' => 'admin',
                'role' => 255,
                'active' => 1
            ]);
        } else {
            echo "Admin is already registered";
        }
    }
}
