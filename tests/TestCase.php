<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;
use App\Modules\Auth\Services\AuthService;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    const BASE_URL = '/api/scms/';
    
    /*
     * @var AuthService $authService 
     */
    protected $authService;
    
    /**
     * @var array
     */
    protected static $headers;

    /**
     * @var User
     */
    protected $user;

    public function setUp() : void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
        $this->user->attachRole(Role::admin()->first());
        
        $this->authService = $this->app->make('\App\Modules\Auth\Services\AuthService');
        $token = $this->authService->login($this->user);
        
        self::$headers = [
            'Authorization'    => 'Bearer ' . $token,
        ];
    }    
    
    /**
     * Saving response to file for api documentation
     *
     * @param TestResponse  $response
     * @param string        $file
     * @param int           $code
     * @param bool          $truncateData
     * @return void
     */
    public function saveResponse(TestResponse $response, string $file, int $code, bool $truncateData = false) : void
    {
        $path = storage_path() . DIRECTORY_SEPARATOR . 'responses';
        if (!is_dir($path)) {
            mkdir($path);
        }
        
        $folders = explode('/', $file);
            
        foreach ($folders as $folder) {
            $path .= DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($path)) {
                mkdir($path, 0775);
            }
        }
        
        $file = $path . DIRECTORY_SEPARATOR . "/{$code}.json";
        if (is_file($file)) {
            unlink($file);
        }
        
        $data = json_decode(json_encode($response->getData()), 1);
        if (isset($data['meta']) && isset($data['data']) && is_array($data['data']) && count($data['data']) > 2) {
            $data['data'] = array_slice($data['data'], 0, 2);
        }
        
        $response = json_encode($data);
        $response = str_replace('\/', '/', $response);
        file_put_contents($file, $response);
    }
    
}
