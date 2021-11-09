<?php

namespace Database\Seeders;

use DB;
use Schema;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();

        $email = 'lup4ldn@scio.systems';
        $password = 'scio';

        // Create the main user.
        User::create([
            'firstname' => 'Scio',
            'lastname' => 'Systems',
            'email' => $email,
            'password' => bcrypt($password),
            'identity_provider' => User::IDENTITY_PROVIDER_LOCAL,
        ]);

        // Create a second user.
        User::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt($password),
            'identity_provider' => User::IDENTITY_PROVIDER_LOCAL,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
