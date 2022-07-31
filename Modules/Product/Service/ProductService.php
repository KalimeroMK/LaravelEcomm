<?php

namespace Modules\Product\Service;

use Exception;
use Modules\Admin\Models\Condition;
use Modules\Admin\Models\Size;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class ProductService extends CoreService
{
    private ProductRepository $product_repository;
    
    public function __construct(ProductRepository $product_repository)
    {
        $this->product_repository = $product_repository;
    }
    
    use ImageUpload;
    
    public function index()
    {
        return $this->product_repository->findAll();
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
        if ($color) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        } else {
            $data['color'] = '';
        }
        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        }
        
        try {
            $product = $this->product_repository->create($data);
            $product->categories()->attach($data['category']);
            $product->sizes()->attach($data['size']);
            
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
        ];
    }
    
    /**
     * @param $data
     * @param  int  $id
     *
     * @return array|null
     */
    public function update($data, int $id): ?array
    {
        $color = $data['color'];
        if ($color) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        } else {
            $data['color'] = '';
        }
        if (isset($data['image'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        } else {
            $data['photo'] = Product::find($id)->photo;
        }
        
        try {
            return $this->product_repository->update($id, $data);
        } catch (Exception $exception) {
            return [$exception->getMessage()];
        }
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->product_repository->delete($id);
    }
    
}