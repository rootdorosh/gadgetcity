<?php

namespace Tests\Feature\Modules\Auth\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\User\Models\User;

/**
 * Class LoginControllerTest
 * @package Tests\Feature\Auth
 * @group   auth
 */
class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function login()
    {
        $url = self::BASE_URL . 'auth/login';
      
        $response = $this->json('POST', $url, []);
        $response->assertStatus(422);
        
        $this->saveResponse($response, 'auth/login', 422);
        
        $password = Str::random(12);
        $user = factory(User::class)->create(['password' => $password]);
        
        $data = [
            'email' => $user->email,
            'password' => $password,
        ];
        
        $response = $this->json('POST', $url, $data);
        $response->assertStatus(200);
        
        $this->saveResponse($response, 'auth/login', 200);
    }
}
