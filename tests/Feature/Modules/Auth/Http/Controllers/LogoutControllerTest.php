<?php

namespace Tests\Feature\Modules\Auth\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\User\Models\User;

/**
 * Class LogoutControllerTest
 * @package Tests\Feature\Auth
 * @group   auth
 */
class LogoutControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    
    /**
     * @test
     */
    public function logout()
    {
        $url = self::BASE_URL . 'auth/logout';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(204);
        $this->saveResponse($response, 'auth/logout', 204);        
    }
}
