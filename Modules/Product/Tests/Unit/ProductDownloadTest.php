<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductDownload;
use Modules\Product\Tests\ProductTestCase;

class ProductDownloadTest extends ProductTestCase
{
    use RefreshDatabase;

    public function test_can_create_product_download(): void
    {
        Storage::fake('local');
        
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'is_downloadable' => true,
        ]);
        
        $file = UploadedFile::fake()->create('test-ebook.pdf', 1024);
        
        $download = ProductDownload::create([
            'product_id' => $product->id,
            'file_name' => 'test-ebook.pdf',
            'file_path' => 'downloads/' . $file->hashName(),
            'original_name' => 'test-ebook.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024 * 1024, // 1MB
            'sort_order' => 1,
            'is_active' => true,
        ]);
        
        $this->assertDatabaseHas('product_downloads', [
            'id' => $download->id,
            'product_id' => $product->id,
            'file_name' => 'test-ebook.pdf',
        ]);
        
        $this->assertEquals($product->id, $download->product_id);
        $this->assertTrue($download->is_active);
    }

    public function test_product_can_have_multiple_downloads(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        ProductDownload::factory()->count(3)->create([
            'product_id' => $product->id,
        ]);
        
        $this->assertEquals(3, $product->downloads()->count());
        $this->assertEquals(3, $product->downloads->count());
    }

    public function test_active_downloads_scope(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        ProductDownload::factory()->create([
            'product_id' => $product->id,
            'is_active' => true,
        ]);
        
        ProductDownload::factory()->create([
            'product_id' => $product->id,
            'is_active' => false,
        ]);
        
        $this->assertEquals(1, $product->activeDownloads()->count());
    }

    public function test_formatted_file_size(): void
    {
        $download = new ProductDownload([
            'file_size' => 1024, // 1KB
        ]);
        
        $this->assertEquals('1 KB', $download->formatted_file_size);
        
        $download->file_size = 1024 * 1024; // 1MB
        $this->assertEquals('1 MB', $download->formatted_file_size);
        
        $download->file_size = null;
        $this->assertEquals('Unknown', $download->formatted_file_size);
    }

    public function test_download_url_generation(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        $download = ProductDownload::factory()->create([
            'product_id' => $product->id,
        ]);
        
        $url = $download->getDownloadUrl(123, 456);
        
        $this->assertStringContainsString('download=' . $download->id, $url);
        $this->assertStringContainsString('order=123', $url);
        $this->assertStringContainsString('signature=', $url);
    }

    public function test_download_url_signature_is_valid(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        $download = ProductDownload::factory()->create([
            'product_id' => $product->id,
        ]);
        
        $orderId = 123;
        $userId = 456;
        
        $url = $download->getDownloadUrl($orderId, $userId);
        
        // Extract signature from URL
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $signature = $params['signature'];
        
        // Verify signature matches expected
        $expectedSignature = hash('sha256', $download->id . ':' . $orderId . ':' . $userId . ':' . config('app.key'));
        
        $this->assertEquals($expectedSignature, $signature);
    }

    public function test_for_product_scope(): void
    {
        $product1 = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        $product2 = Product::factory()->create(['type' => Product::TYPE_DOWNLOADABLE]);
        
        ProductDownload::factory()->count(2)->create(['product_id' => $product1->id]);
        ProductDownload::factory()->create(['product_id' => $product2->id]);
        
        $this->assertEquals(2, ProductDownload::forProduct($product1->id)->count());
        $this->assertEquals(1, ProductDownload::forProduct($product2->id)->count());
    }

    public function test_product_relation(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        $download = ProductDownload::factory()->create([
            'product_id' => $product->id,
        ]);
        
        $this->assertInstanceOf(Product::class, $download->product);
        $this->assertEquals($product->id, $download->product->id);
    }
}
