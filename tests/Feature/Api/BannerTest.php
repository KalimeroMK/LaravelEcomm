<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Banner\Models\Banner;
use Tests\Feature\CoreTest;

class BannerTest extends CoreTest
{
    public string $url = '/api/v1/banner/';
    use WithFaker;
    
    public $model = Banner::class;
    
    /**
     * test create product.
     *
     * @return TestResponse
     */
    public function test_create_banner(): TestResponse
    {
        $token = $this->authenticate();
        Storage::fake('uploads');
        
        $data = [
            'title'       => $this->faker->unique()->word,
            'description' => $this->faker->text,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
            'photo'       => UploadedFile::fake()->image('file.png', 600, 600),
            'status'      => 'inactive',
        ];
        
        return $this->create($this->url, $token, $data);
    }
    
    /**
     * test update product.
     *
     * @return TestResponse
     */
    public function test_update_banner(): TestResponse
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
    public function test_find_banner(): TestResponse
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
    public function test_get_all_banners(): TestResponse
    {
        $token = $this->authenticate();
        
        return $this->list($this->url, $token);
    }
    
    /**
     * test delete products.
     *
     * @return TestResponse
     */
    public function test_delete_banner(): TestResponse
    {
        $token = $this->authenticate();
        $id    = $this->model::firstOrFail()->id;
        
        return $this->destroy($this->url, $token, $id);
    }
}
