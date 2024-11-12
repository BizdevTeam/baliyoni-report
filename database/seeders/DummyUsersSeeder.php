<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $userData = [
            [
                'name'=>'SuperAdmin',
                'email'=>'superadmin@gmail.com',
                'role'=>'superadmin',
                'password'=>bcrypt('superadmin')
                
            ],
            [
                'name'=>'Marketing',
                'email'=>'marketing@gmail.com',
                'role'=>'marketing',
                'password'=>bcrypt('marketing')
                
            ],
            [
                'name'=>'IT',
                'email'=>'it@gmail.com',
                'role'=>'it',
                'password'=>bcrypt('it')
                
            ],
            [
                'name'=>'PROCUREMENT',
                'email'=>'procurement@gmail.com',
                'role'=>'procurement',
                'password'=>bcrypt('procurement')
                
            ],
            [
                'name'=>'ACCOUNTING',
                'email'=>'accounting@gmail.com',
                'role'=>'accounting',
                'password'=>bcrypt('accounting')
                
            ],
            [
                'name'=>'SUPPORT',
                'email'=>'support@gmail.com',
                'role'=>'support',
                'password'=>bcrypt('support')
                
            ],
            [
                'name'=>'HRGA',
                'email'=>'hrga@gmail.com',
                'role'=>'hrga',
                'password'=>bcrypt('hrga')    
            ],
            [
                'name'=>'HRD',
                'email'=>'hrd@gmail.com',
                'role'=>'hrd',
                'password'=>bcrypt('hrd')
                
            ],
            [
                'name'=>'SPI',
                'email'=>'spi@gmail.com',
                'role'=>'spi',
                'password'=>bcrypt('spi')
            ],
        ];  
        foreach($userData as $key => $val){
            User::create($val);
        }
    }
}
