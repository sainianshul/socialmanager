<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Super Admin
        User::updateOrCreate(
            ['email' => 'admin@socialmanager.com'],
            [
                'name' => 'Super Admin',
                'phone' => '9999999999',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'phone_verified_at' => now(),
                'created_by' => User::CREATED_BY_SELF,
            ]
        );

        // 2. Create Default Manager
        User::updateOrCreate(
            ['email' => 'manager@socialmanager.com'],
            [
                'name' => 'General Manager',
                'phone' => '8888888888',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_MANAGER,
                'status' => User::STATUS_ACTIVE,
                'phone_verified_at' => now(),
                'created_by' => User::CREATED_BY_SELF,
            ]
        );

        // 2. Sample Indian Users
        $indianNames = [
            'Rahul Sharma', 'Priya Patel', 'Amit Kumar', 'Sneha Gupta', 'Vikram Singh',
            'Anjali Desai', 'Rajesh Verma', 'Neha Joshi', 'Sanjay Mishra', 'Pooja Reddy',
            'Karan Malhotra', 'Divya Iyer', 'Ravi Tiwari', 'Kavita Rao', 'Arjun Kapoor'
        ];

        $cities = [
            'Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Ahmedabad', 'Chennai', 
            'Kolkata', 'Surat', 'Pune', 'Jaipur', 'Lucknow', 'Kanpur'
        ];

        $password = Hash::make('password123');
        $basePhone = 9800000000;

        foreach ($indianNames as $index => $name) {
            $email = Str::slug($name) . '@example.com';
            $phone = (string)($basePhone + $index);
            
            if (!User::where('phone', $phone)->exists()) {
                User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => $password,
                    'role' => User::ROLE_USER,
                    'status' => User::STATUS_ACTIVE,
                    'phone_verified_at' => now(),
                    'city' => $cities[array_rand($cities)],
                    'created_by' => User::CREATED_BY_ADMIN,
                ]);
            }
        }
    }
}
