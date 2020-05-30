<?php

namespace Tests\Feature\Modules\Auth\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\User\Models\User;

/**
 * Class RemindPasswordControllerTest
 * @package Tests\Feature\Auth
 * @group   auth
 */
class RemindPasswordControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    
    /**
     * @test
     */
    public function email()
    {
        $url = self::BASE_URL . 'auth/remind-password/email';
      
        $response = $this->json('POST', $url);
        $response->assertStatus(422);
        $this->saveResponse($response, 'auth/remind_password/email', 422); 
        
        $response = $this->json('POST', $url, ['email' => $this->user->email]);
        $response->assertStatus(204);
        $this->saveResponse($response, 'auth/remind_password/email', 204);        
        
    }
    
    /**
     * @test
     */
    public function input()
    {
        //send code
        $this->json('POST', self::BASE_URL . 'auth/remind-password/email', ['email' => $this->user->email]);
        $code = $this->authService->getPasswordResetCode($this->user->email);
        
        $url = self::BASE_URL . 'auth/remind-password/input';
      
        $response = $this->json('POST', $url);
        $response->assertStatus(422);
        $this->saveResponse($response, 'auth/remind_password/input', 422); 
        
        $data = [
            'email'     => $this->user->email,
            'code'      => $code,
            'password'  => Str::random(12),
        ];
        
        $response = $this->json('POST', $url, $data);
        $response->assertStatus(204);
        $this->saveResponse($response, 'auth/remind_password/input', 204);        
        
    }
}
