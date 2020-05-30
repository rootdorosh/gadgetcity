<?php

namespace Tests\Feature\Modules\Event\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\Event\Models\Event;
use App\Base\ExtArrHelper;

/**
 * Class EventControllerTest
 * 
 * @group   event
 */
class EventControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
        
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'event/events/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/events/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'event/events';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/events/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'event/events/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'event/events';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'event/events/store', 422);   
        
        $data = ExtArrHelper::keyToItems(factory(Event::class)->make()->toArray(), 'translations', 'locale');
        Event::where('event_id', $data['event_id'])->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'event/events/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $data = ExtArrHelper::keyToItems(factory(Event::class)->make()->toArray(), 'translations', 'locale');
        $event = Event::updateOrCreate(['event_id' => $data['event_id']], $data);
        $url = self::BASE_URL . 'event/events/' . $event->id;
        
        
        $response = $this->json('PUT', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'event/events/update', 422);   
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/events/update', 200); 
        
        $event->delete();
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'event/events/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $data = ExtArrHelper::keyToItems(factory(Event::class)->make()->toArray(), 'translations', 'locale');
        $event = Event::updateOrCreate(['event_id' => $data['event_id']], $data);
        $url = self::BASE_URL . 'event/events/' . $event->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'event/events/show', 200); 
        
        $event->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'event/events/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $data = ExtArrHelper::keyToItems(factory(Event::class)->make()->toArray(), 'translations', 'locale');
        $event = Event::updateOrCreate(['event_id' => $data['event_id']], $data);
        $url = self::BASE_URL . 'event/events/' . $event->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'event/events/destroy', 204); 
        
        $event->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'event/events/destroy', 404);        
    }
}
