<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FirstInitSeeder extends Seeder
{

    use WithoutModelEvents;

    /**
     * Запускается и на PROD и на всех тестовых ENV
     */
    public function run()
    {

        if( !DB::table('roles')->count() ){

            DB::table('roles')->insert([
                'name' => 'Super Admin',
                'key' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('roles')->insert([
                'name' => 'Admin',
                'key' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('roles')->insert([
                'name' => 'Разработчик',
                'key' => 'developer',
                'created_at' => now(),
                'updated_at' => now()
            ]);

        }

    }
}
