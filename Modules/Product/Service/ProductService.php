<?php

namespace Modules\Product\Service;

use Exception;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Helpers\Condition;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Product\Exceptions\SearchException;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;
use Modules\Size\Models\Size;
use Modules\Tag\Models\Tag;

class ProductService extends CoreService
{
    public ProductRepository $product_repository;
    
    public function __construct(ProductRepository $product_repository)
    {
        $this->product_repository = $product_repository;
    }
    
    use ImageUpload;
    
    /**
     * @param $data
     *
     * @return mixed
     * @throws SearchException
     */
    public function getAll($data): mixed
    {
        try {
            return $this->product_repository->search($data);
        } catch (Exception $exception) {
            throw new SearchException($exception);
        }
    }
    
    /**
     * @return array
     */
    public function create(): array
    {
        return [
            'brands'     => Brand::get(),
            'categories' => Category::get(),
            'product'    => new Product(),
            'sizes'      => Size::get(),
            'conditions' => Condition::get(),
            'tags'       => Tag::get(),
        ];
    }
    
    /**
     * @param $data
     *
     * @return string|null
     */
    public function store($data): null|string
    {
        $color = $data['color'];
        if (is_array($color)) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        }
        
        try {
            $product = $this->product_repository->create($data);
            $product->categories()->attach($data['category']);
            $product->sizes()->attach($data['size']);
            $product->tags()->attach($data['tag']);
            
            return $product;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return array
     */
    public function edit($id): array
    {
        return [
            'brands'     => Brand::get(),
            'categories' => Category::all(),
            'product'    => $this->product_repository->findById($id),
            'sizes'      => Size::get(),
            'conditions' => Condition::get(),
            'tags'       => Tag::get(),
        ];
    }
    
    /**
     * @param $id
     *
     * @return array
     */
    public function show($id): array
    {
        return [
            'product' => $this->product_repository->findById($id),
        ];
    }
    
    /**
     * @param $data
     * @param  int  $id
     *
     * @return array|null
     */
    public function update(int $id, $data): ?array
    {
        $color = $data['color'];
        if (is_array($color)) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
        if (isset($data['image'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        } else {
            $data['photo'] = Product::find($id)->photo;
        }
        
        try {
            $product = $this->product_repository->update($id, $data);
            $product->categories()->sync($data['category'], true);
            $product->sizes()->sync($data['size'], true);
            $product->tags()->sync($data['size'], true);
            
            return $product;
        } catch (Exception $exception) {
            return [$exception->getMessage()];
        }
    }
    
    /**
     * @param $id
     *
     * @return Exception|void
     */
    public function destroy($id)
    {
        try {
            $this->product_repository->delete($id);
        } catch (Exception $exception) {
            return $exception;
        }
    }
    
}