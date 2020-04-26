<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginViaEmailPasswordTest extends TestCase
{
    use DatabaseTransactions;


    // If user not type email or password
    // Must throw Invalid exception
    public function testEmailPasswordMustInvalidIfEmpty()
    {
        $response = $this->postJson('api/v1/login', [
            //
        ]);

        $response->assertStatus(422);
    }


    public function testErrorIfInvalidEmail()
    {
        $response = $this->postJson('api/v1/login', [
            'email' => 'test'
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'email'
        ]);
    }


    // If email does't exists
    // Throw error
    public function testErrorIfEmailDoestExists()
    {
        $response = $this->postJson('api/v1/login', [
            'email' => 'test@mail.com',
            'password' => 'thisispassword'
        ]);

        $response->assertStatus(422);
    }


    public function testErrorIfCredentialsInvalid()
    {
        User::create([
            'name' => 'Test',
            'email' => 'test@mail.com',
            'password' => 'thisispassword'
        ]);

        $response = $this->postJson('api/v1/login', [
            'email' => 'test@mail.com',
            'password' => 'aaaaaaa' // Invalid password
        ]);

        $response->assertStatus(400);
    }

    // Success
    // Must have token
    // Token must valid json expect
    public function testLoginSuccessAndResponseAccessToken()
    {
        factory(User::class)->create([
            'email' => 'test@mail.com',
            'password' => 'thisispassword'
        ]);

        $response = $this->postJson('api/v1/login', [
            'email' => 'test@mail.com',
            'password' => 'thisispassword'
        ]);

        $response->assertSuccessful();

        // { data : { access_token: 'xxxxx' } }
        $response->assertJsonStructure([
            'data' => [
                'access_token'
            ]
        ]);
    }
}
