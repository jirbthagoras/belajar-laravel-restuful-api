<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function testRegisterSuccess(): void
    {
        $this->post("/api/users", [
            "username" => "Kenntcky.",
            "password" => "banona",
            "name" => "Banon Kenta Oktora"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "username" => "Kenntcky.",
                    "name" => "Banon Kenta Oktora"
                ]
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => "Banon Kenta Oktora"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => ['The username field is required.'],
                    "password" => ['The password field is required.'],
            ]]);
    }
    public function testRegisterAlreadyExist(): void
    {
        $this->testRegisterSuccess();

        $this->post("/api/users", [
            "username" => "Kenntcky.",
            "password" => "banona",
            "name" => "Banon Kenta Oktora"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => "Username already registered",
                ]
            ]);
    }

    public function testLoginSuccess()
    {

        $this->testRegisterSuccess();

        $this->post("/api/users/login", [
            "username" => "Kenntcky.",
            "password" => "banona",
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "Kenntcky.",
                    "name" => "Banon Kenta Oktora"
                ]
            ]);

    }

    public function testLoginFailed()
    {

        $this->testRegisterSuccess();

        $this->post("/api/users/login", [
            "username" => "",
            "password" => "",
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "username" => ["The username field is required."],
                    "password" => ["The password field is required."]
                ]
            ]);

    }

    public function testLoginUsernameOrPasswordWrong()
    {

        $this->testRegisterSuccess();

        $this->post("/api/users/login", [
            "username" => "erlangga",
            "password" => "a",
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Username or Password Wrong"]
                ]
            ]);

    }


}
