<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $users = [
            [
                'name' => 'CRM_SMART',
                'email' => 'crm@smart.com.co',
                'password' => Hash::make('password'),
                'is_internal' => true
            ],
            [
                'name' => 'Defensor del Consumidor',
                'email' => 'defensor@gov.com.co',
                'password' => Hash::make('password'),
                'is_internal' => false
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
