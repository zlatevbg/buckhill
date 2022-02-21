<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\File;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserLoginValidationFail()
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'xxx@buckhill.co.uk',
            'password' => 'xxx',
        ]);

        $response->assertStatus(422)->assertJson(fn (AssertableJson $json) => $json->has('errors'));
    }

    public function testUserLoginFail()
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'user@buckhill.co.uk',
            'password' => 'password',
        ]);

        $response->assertStatus(401)->assertJson(['error' => 'Unauthorized']);
    }

    public function testUserLoginSuccess()
    {
        $response = $this->postJson('/api/v1/user/login', [
            'email' => 'user@buckhill.co.uk',
            'password' => 'userpassword',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
        $this->assertAuthenticated();
    }

    public function testUserCreate()
    {
        $user = User::factory()->make([
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'avatar' => File::all()->random()->uuid,
            'marketing' => 1,
        ])->makeVisible('password')->toArray();

        $response = $this->postJson('/api/v1/user/create', $user);

        $response->assertStatus(200)->assertSee('id');
    }

    public function testUserGetInfo()
    {
        $token = $this->authenticate('user@buckhill.co.uk', 'userpassword');

        $response = $this->getUserListing($token);

        $response->assertStatus(200)->assertJson(['id' => 2]);
    }

    public function testUserEdit()
    {
        $user = User::where('is_admin', 0)->where('email', '!=', 'user@buckhill.co.uk')->inRandomOrder()->first();

        $token = $this->authenticate($user['email'], 'userpassword');

        $response = $this->putJson('/api/v1/user/edit', [
            'token' => $token,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'] . ' Edited',
            'email' => $user['email'],
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'address' => $user['address'],
            'phone_number' => $user['phone_number'],
        ]);

        $response->assertStatus(200)->assertJson(['last_name' => $user['last_name'] . ' Edited']);
    }

    public function testUserGetOrders()
    {
        $token = $this->authenticate('user@buckhill.co.uk', 'userpassword');

        $response = $this->json('get', '/api/v1/user/orders', [
            'token' => $token,
            'page' => 1,
            'limit' => 5,
            'sortBy' => 'amount',
            'desc' => 1,
        ]);

        $response->assertStatus(200)->assertSee('total')->assertJsonCount(5, 'data');
    }

    public function testUserForgotPassword()
    {
        $response = $this->postJson('/api/v1/user/forgot-password', [
            'email' => 'user@buckhill.co.uk',
        ]);

        $response->assertStatus(200)->assertJson(fn (AssertableJson $json) => $json->has('reset_token'));
    }

    public function testUserResetPassword()
    {
        $user = User::where('email', 'user@buckhill.co.uk')->first();
        $token = Password::broker()->createToken($user);

        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => 'user@buckhill.co.uk',
            'password' => 'userpassword',
            'password_confirmation' => 'userpassword',
            'token' => $token,
        ]);

        $response->assertStatus(200)->assertJson(['success' => trans(Password::PASSWORD_RESET)]);
    }

    public function testUserLogout()
    {
        $token = $this->authenticate('user@buckhill.co.uk', 'userpassword');

        $response = $this->json('get', '/api/v1/user/logout', [
            'token' => $token,
        ]);

        $response->assertStatus(200)->assertJson(['message' => 'Successfully logged out']);
    }

    public function testUserDelete()
    {
        $user = User::factory()->create([
            'email' => 'zlatevbg@gmail.com',
        ]);

        $this->assertModelExists($user);

        $token = $this->authenticate('zlatevbg@gmail.com', 'userpassword');

        $response = $this->deleteJson('/api/v1/user', [
            'token' => $token,
        ]);

        $this->assertDeleted($user);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function testUser1CantAccessUser2Products()
    {
        // Login first user
        $token1 = $this->authenticate('user@buckhill.co.uk', 'userpassword');

        $response = $this->json('get', '/api/v1/user/orders', [
            'token' => $token1,
        ]);

        // Get product uuid belonging to the first user
        $product = $response->json()['data'][0]['products'][0]['product'];

        // Login second user
        $user = User::factory()->create([
            'email' => 'zlatevbg@gmail.com',
        ]);

        $token2 = $this->authenticate('zlatevbg@gmail.com', 'userpassword');

        // Try to get product belonging to the first user
        $response = $this->json('get', '/api/v1/product/' . $product, [
            'token' => $token2,
        ]);

        $response->assertStatus(403)->assertJson(['error' => 'This action is unauthorized.']);
    }

    public function testAdminCantAccessUserPage()
    {
        $token = $this->authenticate('admin@buckhill.co.uk', 'admin', 'admin');

        $response = $this->getUserListing($token);

        $response->assertStatus(403)->assertJson(['error' => 'This action is unauthorized.']);
    }

    /**
     * Authenticate user.
     *
     * @return void
     */
    protected function authenticate($email, $password, $route = 'user')
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
    protected function getUserListing($token)
    {
        $response = $this->json('get', '/api/v1/user', [
            'token' => $token,
        ]);

        return $response;
    }
}
