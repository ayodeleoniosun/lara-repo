<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\CreateUsers;

uses(RefreshDatabase::class, CreateUsers::class);

test('cannot login with invalid credentials', function () {
    $data = [
        'email_address' => 'email@boilerplate.test',
        'password'      => 'email@boilerplate.test',
    ];

    $response = $this->postJson($this->apiBaseUrl . '/accounts/login', $data);
    $response->assertUnauthorized();
    $responseJson = json_decode($response->content());

    $this->assertEquals('error', $responseJson->status);
    $this->assertEquals('Incorrect login credentials', $responseJson->message);
});

test('can login with valid credentials', function () {
    $user = $this->createUser();

    $data = ['email_address' => $user->email_address, 'password' => 'password'];

    $response = $this->postJson($this->apiBaseUrl . '/accounts/login', $data);
    $responseJson = json_decode($response->content());

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user' => ['id', 'first_name', 'last_name', 'email_address', 'slug', 'phone_number',
                    'verified', 'created_at', 'updated_at',
                ],
                'token',
            ],
        ]);

    $this->assertEquals('success', $responseJson->status);
    $this->assertEquals('Login successful', $responseJson->message);
});
