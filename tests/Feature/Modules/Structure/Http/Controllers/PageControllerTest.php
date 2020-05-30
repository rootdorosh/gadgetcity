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
use App\Base\ExtArrHelper;
use App\Modules\ContentBlock\Models\ContentBlock;
use App\Modules\ContentBlock\Front\Widget as ContentBlockWidget;

/**
 * Class PageControllerTest
 * 
 * @group structure.page
 */
class PageControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
      
    /*
     * @var StructureService
     */
    private $structureService;
    
    public function setUp() : void
    {
        parent::setUp();
        
        $this->structureService = new StructureService;
    }
    
    /*
     * @return Domain
     */
    private function createDomain(): Domain
    {
        foreach (Domain::get() as $item) {
            $item->delete();
        }
        
        $domain = factory(Domain::class)->create();
        
        $rootPage = $this->structureService->makeDomainRootPage($domain);
      
        $contactPage = $this->structureService->makePage(
            $rootPage, 
            ExtArrHelper::keyToItems(factory(Page::class)->make(['alias' => 'contact'])->toArray(), 'translations', 'locale')
        );
        
        $aboutPage = $this->structureService->makePage(
            $rootPage, 
            ExtArrHelper::keyToItems(factory(Page::class)->make(['alias' => 'about'])->toArray(), 'translations', 'locale')
        );
        
        $newsPage = $this->structureService->makePage(
            $rootPage, 
            ExtArrHelper::keyToItems(factory(Page::class)->make(['alias' => 'news'])->toArray(), 'translations', 'locale')
        );
        
        $categoryPage = $this->structureService->makePage(
            $newsPage, 
            ExtArrHelper::keyToItems(factory(Page::class)->make(['alias' => 'category'])->toArray(), 'translations', 'locale')
        );
        
        $viewPage = $this->structureService->makePage(
            $categoryPage, 
            ExtArrHelper::keyToItems(factory(Page::class)->make(['alias' => 'view'])->toArray(), 'translations', 'locale')
        );
                       
        return $domain;
    }

    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'structure/domains/pages/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        //dd($response->getData());
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/pages/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $domain = $this->createDomain();
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages';
      
        $response = $this->json('GET', $url, [], self::$headers);
        
        $response->assertStatus(200);      
        $this->saveResponse($response, 'structure/pages/index', 200);   
        
        $domain->delete();
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/pages/index', 404);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $domain = $this->createDomain();
        $parentPage = $domain->pages()->first();
        $pageData = ExtArrHelper::keyToItems(
            factory(Page::class)->make(['alias' => 'team'])->toArray(), 'translations', 'locale'
        ) + ['parent_id' => $parentPage->id];
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'structure/pages/store', 422);   
        
        $response = $this->json('POST', $url, $pageData, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'structure/pages/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $domain = $this->createDomain();
        $page = $domain->pages()->first();
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages/' . $page->id;
        $data = ExtArrHelper::keyToItems($page->toArray(), 'translations', 'locale');
        
        $response = $this->json('PUT', $url, [], self::$headers);
        $response->assertStatus(422);   
        $this->saveResponse($response, 'structure/pages/update', 422);   
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/pages/update', 200); 
        
        $page->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/pages/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $domain = $this->createDomain();
        $page = $domain->pages()->first();
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages/' . $page->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/pages/show', 200); 
        
        $page->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/pages/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $domain = $this->createDomain();
        $page = $domain->pages()->where('alias', 'news')->first();
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages/' . $page->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'structure/pages/destroy', 204); 
        
        $page->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'structure/pages/destroy', 404);        
    }
    
    /**
     * @test
     */
    public function move()
    {       
        $domain = $this->createDomain();
        $page = $domain->pages()->where('alias', 'news')->first();
        $pageParent = $domain->pages()->where('alias', 'contact')->first();
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages/' . $page->id . '/move';
               
        $response = $this->json('POST', $url, ['parent_id' => $pageParent->id], self::$headers);
        $response->assertStatus(200); 
        $this->saveResponse($response, 'structure/pages/move', 200); 
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'structure/pages/move', 422);        
    }
    
    /**
     * @test
     */
    public function copy()
    {       
        $domain = $this->createDomain();
        $page = $domain->pages()->where('alias', 'news')->first();
        //$page->blocks()->save(new Block(factory(Block::class)->make(['alias' => 'content1'])->toArray()));
        //$page->blocks()->save(new Block(factory(Block::class)->make(['alias' => 'content2'])->toArray()));
        
        $url = self::BASE_URL . 'structure/domains/' . $domain->id . '/pages/' . $page->id . '/copy';
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'structure/pages/copy', 200);         
    }
}
