<?php 

namespace Tests\Feature\Modules\Domain\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\Domain\Models\Domain;

/**
 * Class DomainControllerTest
 * 
 * @group  domain
 */
class DomainControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
     
    /*
     * @param  Domain $domain
     * @return  array
     */
    private function toArray(Domain $domain): array
    {
            return $domain->toArray();    
                }
    
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'domain/domains/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/domains/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'domain/domains';
        
        factory(Domain::class, 3)->create();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/domains/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/domains/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'domain/domains';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/domains/store', 422);   
        
        $data = $this->toArray(factory(Domain::class)->make());
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'domain/domains/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $domain->id;
        $data = $this->toArray($domain);
        
        $response = $this->json('PUT', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/domains/update', 422);   
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'domain/domains/update', 200); 
        
        $domain->delete();
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/domains/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $domain->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/domains/show', 200); 
        
        $domain->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/domains/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $domain = factory(Domain::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $domain->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'domain/domains/destroy', 204); 
        
        $domain->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/domains/destroy', 404);        
    }
}
