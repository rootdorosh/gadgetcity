<?php

namespace Tests\Feature\Modules\User\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use App\Base\ExtArrHelper;

/**
 * Class UserControllerTest
 * 
 * @group user
 */
class UserControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'user/users/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/users/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'user/users';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/users/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/users/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'user/users';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/users/store', 422);   
        
        $data = factory(User::class)->make()->toArray();
        $data['password'] = Str::random(16);
        User::where('email', $data['email'])->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'user/users/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $user = factory(User::class)->create();
        $url = self::BASE_URL . 'user/users/' . $user->id;
        $data = $user->toArray();
        if (isset($data['password'])) {
            unset($data['password']);
        }
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/users/update', 422);   
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/users/update', 200); 
        
        $user->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/users/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $user = factory(User::class)->create();
        $url = self::BASE_URL . 'user/users/' . $user->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/users/show', 200); 
        
        $user->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/users/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $user = factory(User::class)->create();
        $url = self::BASE_URL . 'user/users/' . $user->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'user/users/destroy', 204); 
        
        $user->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/users/destroy', 404);        
    }
    
    /**
     * @test
     */
    public function bulkDestroy()
    {       
        $user = factory(User::class)->create();
        $url = self::BASE_URL . 'user/users/bulk-destroy';
        $path = 'user/users/bulk_destroy';
        
        $data = [
            'ids' => [$user->id],
        ];
        
        $response = $this->json('DELETE', $url, $data, self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, $path, 204); 
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, $path, 422);        
    }
    
}
