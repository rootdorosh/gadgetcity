<?php

namespace Tests\Feature\Modules\User\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\User\Models\Role;
use App\Base\ExtArrHelper;

/**
 * Class RoleControllerTest
 * 
 * @group user
 */
class RoleControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'user/roles/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/roles/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'user/roles';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/roles/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/roles/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'user/roles';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/roles/store', 422);   
        
        $data = factory(Role::class)->make()->toArray();
        Role::where('slug', $data['slug'])->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'user/roles/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $role = factory(Role::class)->create();
        $url = self::BASE_URL . 'user/roles/' . $role->id;
        $data = $role->toArray();
        
        $response = $this->json('PUT', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'user/roles/update', 422);   
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/roles/update', 200); 
        
        $role->delete();
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/roles/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $role = factory(Role::class)->create();
        $url = self::BASE_URL . 'user/roles/' . $role->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'user/roles/show', 200); 
        
        $role->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/roles/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $role = factory(Role::class)->create();
        $url = self::BASE_URL . 'user/roles/' . $role->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'user/roles/destroy', 204); 
        
        $role->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'user/roles/destroy', 404);        
    }
    
    /**
     * @test
     */
    public function bulkDestroy()
    {       
        $role = factory(Role::class)->create();
        $url = self::BASE_URL . 'user/roles/bulk-destroy';
        $path = 'user/roles/bulk_destroy';
        
        $data = [
            'ids' => [$role->id],
        ];
        
        $response = $this->json('DELETE', $url, $data, self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, $path, 204); 
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, $path, 422);        
    }
    
}
