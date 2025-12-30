<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $this->call([
            // ProvinceSeeder::class,
            // RoleSeeder::class,
            // ParamSeeder::class,
            // PadananDataSeeder::class,
            UserSeeder::class,
            // UserEselon1Seeder::class,
            // KlSeeder::class,
            // UnitSeeder::class,
            // ParticipantSeeder::class,
        ]);
    }
}
