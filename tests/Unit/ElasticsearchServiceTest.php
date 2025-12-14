<?php

namespace Tests\Unit;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientInterface;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;
use Tests\TestCase;

class ElasticsearchServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MockInterface $clientMock;
    protected ElasticsearchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a partial mock of the service to override the constructor behavior
        // because the constructor builds the real client.
        // Instead, we will inject the mock client into the service via reflection or by mocking the ClientBuilder.
        // However, since ClientBuilder is a static call in constructor, it's hard to mock without valid facade or dependency injection refactor.
        // For this test, we will refactor the service slightly to allow setting the client, OR we use Mockery to intercept.
        
        // Simpler approach: We can extend the class or use Reflection to set the protected property.
    }

    public function test_it_can_index_a_product()
    {
        // Disable observers to prevent real indexing attempts during factory creation
        Product::unsetEventDispatcher();

        // Create a product
        $product = Product::factory()->create([
            'title' => 'Test Product',
            'price' => 100,
        ]);

        // Mock the ClientInterface (which is NOT final)
        $clientMock = Mockery::mock(ClientInterface::class);
        $clientMock->shouldReceive('index')
            ->once()
            ->with(Mockery::on(function ($params) use ($product) {
                return $params['index'] === 'products' &&
                       $params['id'] === (string) $product->id &&
                       $params['body']['title'] === 'Test Product';
            }));

        // Inject the mock
        $service = new ElasticsearchService();
        $this->setProtectedProperty($service, 'elasticsearch', $clientMock);

        // Act
        $service->indexProduct($product);
    }

    public function test_it_can_delete_a_product()
    {
        $clientMock = Mockery::mock(ClientInterface::class);
        $clientMock->shouldReceive('delete')
            ->once()
            ->with(['index' => 'products', 'id' => '1']);

        $service = new ElasticsearchService();
        $this->setProtectedProperty($service, 'elasticsearch', $clientMock);

        $service->deleteProduct(1);
    }

    public function test_it_handles_indexing_errors_gracefully()
    {
        Product::unsetEventDispatcher();
        $product = Product::factory()->create();

        $clientMock = Mockery::mock(ClientInterface::class);
        $clientMock->shouldReceive('index')
            ->once()
            ->andThrow(new Exception('Indexing failed'));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Elasticsearch indexing failed');
            });

        $service = new ElasticsearchService();
        $this->setProtectedProperty($service, 'elasticsearch', $clientMock);

        $service->indexProduct($product);
    }

    /**
     * Helper to set protected properties
     */
    protected function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
