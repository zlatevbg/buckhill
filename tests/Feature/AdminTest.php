<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\File;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_login_validation_fail()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'xxx@buckhill.co.uk',
            'password' => 'xxx',
        ]);

        $response->assertStatus(422)->assertJson(fn (AssertableJson $json) => $json->has('errors'));
    }

    public function test_admin_login_fail()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'password',
        ]);

        $response->assertStatus(401)->assertJson(['error' => 'Unauthorized']);
    }

    public function test_admin_login_success()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
        $this->assertAuthenticated();
    }

    public function test_admin_create()
    {
        $token = $this->authenticate('admin@buckhill.co.uk', 'admin');
        $headers = ['Authorization' => "Bearer $token"];

        $user = User::factory()->make([
            'password' => 'admin',
            'password_confirmation' => 'admin',
            'avatar' => File::all()->random()->uuid,
            'marketing' => 1,
        ])->makeVisible('password')->toArray();

        $response = $this->postJson('/api/v1/admin/create', $user, $headers);

        $response->assertStatus(200)->assertSee('id');
    }

    public function test_admin_user_listing()
    {
        $token = $this->authenticate('admin@buckhill.co.uk', 'admin');

        $response = $this->getUsersListing($token);

        $response->assertStatus(200)->assertSee('total')->assertJsonCount(5, 'data');
    }

    public function test_admin_logout()
    {
        $token = $this->authenticate('admin@buckhill.co.uk', 'admin');

        $response = $this->json('get', '/api/v1/admin/logout', [
            'token' => $token,
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Successfully logged out']);
    }

    public function test_user_cant_access_admin_page()
    {
        $token = $this->authenticate('user@buckhill.co.uk', 'userpassword', 'user');

        $response = $this->getUsersListing($token);

        $response->assertStatus(403)->assertJson(['error' => 'This action is unauthorized.']);
    }

    /**
     * Authenticate user.
     *
     * @return void
     */
    protected function authenticate($email, $password, $route = 'admin')
    {
        $response = $this->postJson('/api/v1/' . $route . '/login', [
            'email' => $email,
            'password' => $password,
        ]);

        return $response->json()['token'];
    }

    /**
     * Get User Listing.
     *
     * @return void
     */
    protected function getUsersListing($token)
    {
        $response = $this->json('get', '/api/v1/admin/user-listing', [
            'token' => $token,
            'page' => 1,
            'limit' => 5,
            'sortBy' => 'first_name',
            'desc' => 0,
        ]);

        return $response;
    }
}
