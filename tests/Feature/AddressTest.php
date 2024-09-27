<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AddressTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM addresses");
        DB::delete("DELETE FROM contacts");
        DB::delete("DELETE FROM users");
    }

    public function testCreateSuccess(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->withHeaders(["Authorization" => "test"])
        ->post("/api/contacts/$contact->id/addresses",
            [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "test",
            ])
        ->assertStatus(201);
    }

    public function testGetSuccess()
    {

        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $this->get("/api/contacts/$address->contact_id/addresses/$address->id", ["Authorization" => "test"])
            ->assertStatus(200);

    }


}
