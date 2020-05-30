<?php 

namespace Tests\Feature\Modules\ContentBlock\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\ContentBlock\Models\ContentBlock;
use App\Base\ExtArrHelper;

/**
 * Class ContentBlockControllerTest
 * 
 * @group  contentBlock
 */
class ContentBlockControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
     
    /*
     * @param  ContentBlock $contentBlock
     * @return  array
     */
    private function toArray(ContentBlock $contentBlock): array
    {
        return ExtArrHelper::keyToItems($contentBlock->toArray(), 'translations', 'locale'); 
    }
    
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'content-block/content-blocks/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'content_block/content_blocks/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'content-block/content-blocks';
        
        factory(ContentBlock::class, 3)->create();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'content_block/content_blocks/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'content_block/content_blocks/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'content-block/content-blocks';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'content_block/content_blocks/store', 422);   
        
        $data = $this->toArray(factory(ContentBlock::class)->make());
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'content_block/content_blocks/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $contentBlock = factory(ContentBlock::class)->create();
        $url = self::BASE_URL . 'content-block/content-blocks/' . $contentBlock->id;
        $data = $this->toArray($contentBlock);
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'content_block/content_blocks/update', 422);   
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'content_block/content_blocks/update', 200); 
        
        $contentBlock->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'content_block/content_blocks/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $contentBlock = factory(ContentBlock::class)->create();
        $url = self::BASE_URL . 'content-block/content-blocks/' . $contentBlock->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'content_block/content_blocks/show', 200); 
        
        $contentBlock->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'content_block/content_blocks/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $contentBlock = factory(ContentBlock::class)->create();
        $url = self::BASE_URL . 'content-block/content-blocks/' . $contentBlock->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'content_block/content_blocks/destroy', 204); 
        
        $contentBlock->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'content_block/content_blocks/destroy', 404);        
    }
}
