<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID'); // locale Indonesia
 
        $bikeTypes = ['Road', 'Gravel', 'Mountain'];

        User::create([
            'name'      => 'Munif Syahli',
            'email'     => 'munif@bikepath.com',
            'password'  => Hash::make('password123'),
            'age'       => 21,
            'bike_type' => 'Road',
        ]);

        for ($i = 0; $i < 9; $i++) {
            User::create([
                'name'      => $faker->name(),
                'email'     => $faker->unique()->safeEmail(),
                'password'  => Hash::make('password123'),
                'age'       => $faker->numberBetween(17, 45),
                'bike_type' => $faker->randomElement($bikeTypes),
            ]);
        }
    }
}
