<?php

namespace Tests\Feature\Modules\Event\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\Event\Models\Queue;

/**
 * Class QueueControllerTest
 * 
 * @group   event
 */
class QueueControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'event/queues/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/queues/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'event/queues';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/queues/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'event/queues/index', 422);        
    }
        
    /**
     * @test
     */
    public function show()
    {       
        $queue = factory(Queue::class)->create();
        
        $url = self::BASE_URL . 'event/queues/' . $queue->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/queues/show', 200); 
        
        $queue->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'event/queues/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $queue = factory(Queue::class)->create();
        
        $url = self::BASE_URL . 'event/queues/' . $queue->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'event/queues/destroy', 204); 
        
        $queue->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'event/queues/destroy', 404);        
    }
}
