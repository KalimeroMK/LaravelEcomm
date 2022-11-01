<?php

namespace Tests\Feature\Api;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Brand\Models\Brand;
use Tests\Feature\CoreTest;

class BrandTest extends CoreTest
{
    public string $url = '/api/v1/brand/';
    public $model = Brand::class;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_brand(): TestResponse
    {
        $token = $this->authenticate();
        Storage::fake('uploads');
        
        $data = [
            'title'  => time() . 'Test title',
            'photo'  => UploadedFile::fake()->image('file.png', 600, 600),
            'status' => 'inactive',
        ];
        
        return $this->create($this->url, $token, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_brand(): TestResponse
    {
        $token = $this->authenticate();
        $data  = [
            'title'       => time() . 'Test title',
            'description' => time() . 'test-description',
            'status'      => 'inactive',
        ];
        
        $id = $this->model::firstOrFail()->id;
        
        return $this->update($this->url, $token, $data, $id);
    }
    
    /**
     * test find product.
     *
     * @return TestResponse
     */
    public function test_find_brand(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->show($this->url, $token, $id);
    }
    
    /**
     * test get all products.
     *
     * @return TestResponse
     */
    public function test_get_all_brand(): TestResponse
    {
        $token = $this->authenticate();
        
        return $this->list($this->url, $token);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_brand(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->destroy($this->url, $token, $id);
    }
}
