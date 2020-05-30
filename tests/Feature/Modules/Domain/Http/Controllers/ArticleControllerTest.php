<?php 

namespace Tests\Feature\Modules\Domain\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use App\Modules\Domain\Models\Article;

/**
 * Class ArticleControllerTest
 * 
 * @group  domain
 */
class ArticleControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
     
    /*
     * @param  Article $article
     * @return  array
     */
    private function toArray(Article $article): array
    {
            return $article->toArray();    
                }
    
    /**
     * @test
     */
    public function meta()
    {
        $url = self::BASE_URL . 'domain/domains/meta';
      
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/articles/meta', 200);   
    }

    /**
     * @test
     */
    public function index()
    {
        $url = self::BASE_URL . 'domain/domains';
        
        factory(Article::class, 3)->create();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/articles/index', 200);   
        
        $response = $this->json('GET', $url, ['page' => '-', 'per_page' => '-'], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/articles/index', 422);        
    }
    
    /**
     * @test
     */
    public function store()
    {
        $url = self::BASE_URL . 'domain/domains';
      
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/articles/store', 422);   
        
        $data = $this->toArray(factory(Article::class)->make());
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'domain/articles/store', 201);        
    }
    
    /**
     * @test
     */
    public function update()
    {       
        $article = factory(Article::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $article->id;
        $data = $this->toArray($article);
        
        $response = $this->json('PUT', $url, [], self::$headers);
        $response->assertStatus(422);        
        $this->saveResponse($response, 'domain/articles/update', 422);   
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(201);        
        $this->saveResponse($response, 'domain/articles/update', 200); 
        
        $article->delete();
        
        $response = $this->json('PUT', $url, $data, self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/articles/update', 404);        
    }
    
    /**
     * @test
     */
    public function show()
    {       
        $article = factory(Article::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $article->id;
               
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'domain/articles/show', 200); 
        
        $article->delete();
        
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/articles/show', 404);        
    }
    
    /**
     * @test
     */
    public function destroy()
    {       
        $article = factory(Article::class)->create();
        $url = self::BASE_URL . 'domain/domains/' . $article->id;
               
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(204);        
        $this->saveResponse($response, 'domain/articles/destroy', 204); 
        
        $article->delete();
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'domain/articles/destroy', 404);        
    }
}
