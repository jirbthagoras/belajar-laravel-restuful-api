<?php

namespace Database\Seeders;

use App\Models\Contact;
use http\Client\Curl\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::query()
        ->where('username', "=", "test")
        ->first();

        Contact::query()->create([
            "first_name" => "test",
            "last_name" => "test",
            "email" => "test",
            "phone" => "test",
            "user_id" => $user->id
        ]);
    }
}
