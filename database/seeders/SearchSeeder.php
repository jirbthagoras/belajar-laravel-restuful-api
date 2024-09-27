<?php

namespace Database\Seeders;

use App\Models\Contact;
use http\Client\Curl\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::query()
            ->where("username", "=", "test")
            ->first();

        for($i = 0; $i < 20; $i++){
            Contact::query()
                ->create([
                    "first_name" => "First Name - $i",
                    "last_name" => "Last Name - $i",
                    "email" => "email$i@email.com",
                    "phone" => "00$i",
                    "user_id" => $user->id,
                ]);
        }
    }
}
