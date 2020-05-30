<?php

namespace Tests\Feature\Modules\Structure\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\Structure\Models\Domain;
use App\Base\ExtArrHelper;

/**
 * Class DomainControllerTest
 * 
 * @group structure.domain
 */
class DomainControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'structure/domains/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/domains/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'structure/domains';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/domains/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'structure/domains/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'structure/domains';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'structure/domains/store', 422);   
        
        $data = factory(Domain::class)->make()->toArray();
        Domain::where('alias', $data['alias'])->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'structure/domains/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'structure/domains/' . $domain->id;
        $data = $domain->toArray();
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);   
        $this->saveResponse($response, 'structure/domains/update', 422);   
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/domains/update', 200); 
        
        $domain->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/domains/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'structure/domains/' . $domain->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/domains/show', 200); 
        
        $domain->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/domains/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'structure/domains/' . $domain->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'structure/domains/destroy', 204); 
        
        $domain->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/domains/destroy', 404);        
    }
}
