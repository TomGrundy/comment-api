<?php

use App\User;  
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersTableSeeder extends Seeder  
{
    public function run()
    {
        User::create([
            'name' => 'test1',
            'email' => 'test1@test.net',
            'api_token' => 'abc123',
            'moderator' => false
        ]);

        User::create([
            'name' => 'moderator',
            'email' => 'mod@internet.website',
            'api_token' => 'xyz789',
            'moderator' => true
        ]);
    }
}