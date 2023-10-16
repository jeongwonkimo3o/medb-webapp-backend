<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nickname' => 'admin',
            'email' => 'test@medb.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test1234'), // 패스워드 해싱
            'remember_token' => Str::random(10), // 랜덤한 토큰 생성
            'created_at' => now(),
            'updated_at' => now(),
            'is_admin' => 1
            ]);
    }
}
