<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superappadmin@kemenkopukm.go.id',
            'role' => 'superadmin',
            'password' => Hash::make('superappkukm'),
        ]);

        // User::create([
        //     'name' => 'Bagian Keuangan',
        //     'email' => 'bag_keuangan@kemenkopukm.go.id',
        //     'role' => 'realisasi',
        //     'password' => Hash::make('keuangan_kukm'),
        // ]);

        // User::create([
        //     'name' => 'Bagian Kepegawaian',
        //     'email' => ' bag_kepegawaian@kemenkopukm.go.id',
        //     'role' => 'sdm',
        //     'password' => Hash::make('sdm_kukm'),
        // ]);

        // User::create([
        //     'name' => 'Bagian Perencanaan dan Evaluasi',
        //     'email' => 'bagian_pe_mkos@kemenkopukm.go.id',
        //     'role' => 'biro_mkos',
        //     'password' => Hash::make('kukm-evalap'),
        // ]);

        // User::create([
        //     'name' => 'Bagian Perencanaan dan Penganggaran',
        //     'email' => 'bagian_ppmkos@kemenkopukm.go.id',
        //     'role' => 'biro_mkos',
        //     'password' => Hash::make('anggaran-kukm'),
        // ]);

        // User::create([
        //     'name' => 'Admin Notulensi',
        //     'email' => 'notulensi@kemenkopukm.go.id',
        //     'role' => 'notulensi',
        //     'password' => Hash::make('notulenmenteri'),
        // ]);

        // User::create([
        //     'name' => 'Admin Rapim',
        //     'email' => 'adminrapim@kemenkopukm.go.id',
        //     'role' => 'rapim',
        //     'password' => Hash::make('rapimadmin2023'),
        // ]);

        User::create([
            'name' => 'Dashboard View Only',
            'email' => 'viewdash@kemenkopukm.go.id',
            'role' => 'view_only',
            'password' => Hash::make('dashboardkukm'),
        ]);
    }
}
