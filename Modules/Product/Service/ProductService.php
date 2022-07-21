<?php

namespace Modules\Product\Service;

use App\Traits\ImageUpload;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Product\Models\_IH_Product_C;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Service\CoreService;
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
     * @param $data
     *
     * @return string|null
     */
    public function store($data): null|string
    {
        $size  = $data['size'];
        $color = $data['color'];
        if ($size) {
            $data['size'] = implode(',', $size);
        } else {
            $data['size'] = '';
        }
        if ($color) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        } else {
            $data['color'] = '';
        }
        $image = $data['photo'];
        
        try {
            return $this->product_repository->create(
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($image),
                ]
            )->categories()->attach($data['category']);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $data
     * @param $image
     * @param  int  $id
     *
     * @return Collection|Model|_IH_Product_C|Product|Product[]
     */
    public function update($data, $image, int $id): Model|Product|Collection|_IH_Product_C|array
    {
        $size  = $data['size'];
        $color = $data['color'];
        if ($size) {
            $data['size'] = implode(',', $size);
        } else {
            $data['size'] = '';
        }
        if ($color) {
            $color         = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        } else {
            $data['color'] = '';
        }
        if (empty($image)) {
            $image = Product::find($id)->photo;
        } else {
            $image = $this->verifyAndStoreImage($image);
        }
        
        $product = Product::findOrFail($id);
        
        $product->update(
            $data + [
                'photo' => $image,
            ]
        );
        
        $product->categories()->sync($data['category'], true);
        
        return $product;
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
            'items'      => Product::whereId($id)->get(),
            'product'    => $this->product_repository->findById($id),
        
        ];
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
        ];
    }
    
    public function destroy($id): void
    {
        $this->product_repository->delete($id);
    }
    
}