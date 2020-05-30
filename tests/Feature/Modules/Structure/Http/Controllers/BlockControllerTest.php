<?php

namespace Tests\Feature\Modules\Structure\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\{
    WithFaker,
    DatabaseTransactions
};
use App\Modules\Structure\Models\{
    Domain,
    Page,
    Block
};
use App\Modules\Structure\Services\StructureService;
use App\Modules\Structure\Services\Crud\BlockCrudService;
use App\Base\ExtArrHelper;
use App\Modules\ContentBlock\Front\Widget as ContentBlockWidget;
use App\Modules\ContentBlock\Models\ContentBlock;

/**
 * Class BlockControllerTest
 * 
 * @group structure.block
 */
class BlockControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
      
    /*
     * @var StructureService
     */
    private $structureService;
    
    /*
     * @var BlockCrudService
     */
    private $blockCrudService;
    
    /*
     * @var Domain
     */
    private $domain;
    
    /*
     * @var Page
     */
    private $page;
    
    public function setUp() : void
    {
        parent::setUp();
        
        $this->structureService = new StructureService;
        $this->blockCrudService = new BlockCrudService;
    }
    
    /*
     * init data
     */
    private function init()
    {
        foreach (Domain::get() as $item) {
            $item->delete();
        }
        
        $this->domain = factory(Domain::class)->create();
        
        $this->page = $this->structureService->makeDomainRootPage($this->domain);      
    }
   
    /*
     * @param bool $create
     * @return mixed
     */
    private function makeBlock(bool $create = false)
    {
        $contentBlock = factory(ContentBlock::class)->create();
        $contentBlockWidget = new ContentBlockWidget;
        
        $data = [
            'alias' => 'content1',
            'widget_id' => 'ContentBlock',
            'action' => 'index',
            'template' => 'empty',
            'block_id' => $contentBlock->id,
        ];
        
        if ($create) {
            return $this->blockCrudService->insert($this->page, $data);
        } else {
            return $data;
        }
    }
    
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'structure/domains/blocks/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/blocks/meta', 200);   
    }
    
    /**
     * @test
     */
    public function insert()
    {
        $this->init();
        
        $url = self::BASE_URL . 'structure/domains/' . $this->domain->id . '/pages/' . $this->page->id . '/blocks';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'structure/blocks/insert', 422);   
        
        
        $response = $this->json('POST', $url, $this->makeBlock(), self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'structure/blocks/insert', 204);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {
        $this->init();
        
        $url = self::BASE_URL . 'structure/domains/' . $this->domain->id . '/pages/' . $this->page->id . '/blocks';
      
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(422);    
        $this->saveResponse($response, 'structure/blocks/destroy', 422);   
        
        $block = $this->makeBlock(true);
        
        $response = $this->json('DELETE', $url, ['alias' => $block->alias], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'structure/blocks/destroy', 204);        
    }
    
}
