<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\DocumentGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = new Collection();
        $users->push(User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
        ]));

        $users = $users->merge(User::factory(50)->create());

        foreach ($users as $user) {
            $groups = DocumentGroup::factory(rand(20, 50))->create([
                'user_id' => $user->id,
            ]);

            foreach ($groups as $group) {
                $files = Document::factory(rand(10, 30))->create([
                    'document_group_id' => $group->id,
                ]);
            }
        }
    }
}
