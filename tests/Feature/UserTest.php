<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

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
                    "username" => ["username already registered"],
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

    public function testGetSuccess()
    {

        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "username" => "test",
                ]
            ]);

    }


    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized."],
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::query()->where("name", "=", "test")->first();

        $this->withHeaders(["Authorization" => "test"])->
        patch('/api/users/current',
            ["username" => "memek"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "memek",
                ]
            ]);

        $newUser = User::query()->where("name", "=", "test")->first();

        assertNotEquals($oldUser, $newUser);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::query()->where("name", "=", "test")->first();

        $this->withHeaders(["Authorization" => "test"])->
        patch('/api/users/current',
            ["password" => "baru"]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "test",
                ]
            ]);

        $newUser = User::query()->where("name", "=", "test")->first();

        assertNotEquals($oldUser, $newUser);
    }

    public function testUpdateUnauthorized  ()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::query()->where("name", "=", "test")->first();

        $this->patch('/api/users/current',
            ["username" => "test1"]
        )->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized."]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->withHeaders(["Authorization" => "test"])->
        delete('/api/users/logout')->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->withHeaders(["Authorization" => "memek"])->
        delete('/api/users/logout')->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["Unauthorized."]
                ]
            ]);
    }


}
