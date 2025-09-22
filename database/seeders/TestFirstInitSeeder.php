<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestFirstInitSeeder extends Seeder
{

    use WithoutModelEvents;

    public function run()
    {

        if( !DB::table('users')->count() ){

            DB::table('users')->insert([
                'email' => 'admin@mail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('users')->insert([
                'email' => 'usual_admin@mail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('users')->insert([
                'email' => 'dev@mail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('users')->insert([
                'email' => 'user@mail.com',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now()
            ]);


            DB::table('role_user')->insert([
                'user_id' => 1,
                'role_id' => 1
            ]);

            DB::table('role_user')->insert([
                'user_id' => 2,
                'role_id' => 2
            ]);

            DB::table('role_user')->insert([
                'user_id' => 3,
                'role_id' => 3
            ]);

            DB::table('role_user')->insert([
                'user_id' => 3,
                'role_id' => 1
            ]);

        }

    }
}
