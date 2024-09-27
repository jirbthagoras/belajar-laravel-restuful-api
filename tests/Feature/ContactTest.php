<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContactTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM addresses");
        DB::delete("DELETE FROM contacts");
        DB::delete("DELETE FROM users");
    }
    /**
     * A basic feature test example.
     */
    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class]);

        $this->withHeaders(["Authorization" => "test"])
        ->post("/api/contacts", [
            "first_name" => "Jabriel",
            "last_name" => "Talula",
            "email" => "jabriel@gmail.com",
            "phone" => "08179990181",
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" =>
                [
                    "first_name" => "Jabriel",
                ]
            ]);
    }

    public function testCreateFailed()
    {

        $this->seed([UserSeeder::class]);

        $this->withHeaders(["Authorization" => "test"])
            ->post("/api/contacts", [
                "first_name" => "",
                "last_name" => "",
                "email" => "jabriel@gmail.com",
                "phone" => "08179990181",
            ])
            ->assertStatus(401)
            ->assertJson([
                "errors" =>
                    [
                        "first_name" => [
                            "The first name field is required."
                        ],
                    ]
            ]);

    }

    public function testCreateUnauthorized()
    {

        $this->seed([UserSeeder::class]);

        $this->withHeaders([])
            ->post("/api/contacts", [
                "first_name" => "",
                "last_name" => "",
                "email" => "jabriel@gmail.com",
                "phone" => "08179990181",
            ])
            ->assertStatus(401)
            ->assertJson([
                "errors" =>
                    [
                        "message" => [
                            "Unauthorized."
                        ],
                    ]
            ]);

    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->get("/api/contacts/$contact->id", [
            "Authorization" => "test"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test~",
                ],
            ]);
    }

    public function testGetNotFound()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
            "Authorization" => "test"
        ])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Contact not found"],
                ],
            ]);

    }

    public function testGetAnotherUserContact()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->get("/api/contacts/" . ($contact->id), [
            "Authorization" => "test2"
        ])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["Contact not found"],
                ],
            ]);

    }

    public function testUpdateSuccess()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->withHeaders(["Authorization" => "test"])
            ->put("/api/contacts/$contact->id", [
                "first_name" => "test3",
                "last_name" => "test3",
                "email" => "test3",
                "phone" => "test3",
            ])
            ->assertStatus(200)
            ->assertJson([
                "data" =>
                    [
                        "first_name" => "test3",
                    ]
            ]);



    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->withHeaders(["Authorization" => "test2"])
            ->put("/api/contacts/$contact->id", [
                "first_name" => "test3",
                "last_name" => "test3",
                "email" => "test3",
                "phone" => "test3",
            ])
            ->assertStatus(404)
            ->assertJson([
                "errors" =>
                    [
                        "message" => ["Contact not found"],
                    ]
            ]);
    }

    public function testDeleteSuccess()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()
            ->limit(1)
            ->first();

        $this->withHeaders(["Authorization" => "test"])
            ->delete("/api/contacts/$contact->id")
            ->assertStatus(200)
            ->assertJson([
                "data" =>
                    [
                        "status" => [
                            "status" => true,
                        ],
                    ]
            ]);

    }


}
