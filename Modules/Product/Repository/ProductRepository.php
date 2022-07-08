<?php

namespace Modules\Product\Repository;

use App\Traits\ImageUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Product\Models\_IH_Product_C;
use LaravelIdea\Helper\Modules\Product\Models\_IH_Product_QB;
use Modules\Product\Models\Product;

class ProductRepository
{
    use ImageUpload;
    
    /**
     * @return Builder|_IH_Product_QB
     */
    public function getAll(): Builder|_IH_Product_QB
    {
        return Product::with(['brand', 'categories'])->orderBy('id', 'desc');
    }
    
    /**
     * @param $data
     *
     * @return void
     */
    public function storeProduct($data)
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
        
        return Product::create(
            $data + [
                'photo' => $this->verifyAndStoreImage($image),
            ]
        )->categories()->attach($data['category']);
    }
    
    /**
     * @param $data
     * @param $image
     * @param  int  $id
     *
     * @return Collection|Model|_IH_Product_C|Product|Product[]
     */
    public function updateProduct($data, $image, int $id): Model|Product|Collection|_IH_Product_C|array
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
        
        $product->update($data + [
                'photo' => $image,
            ]);
        
        $product->categories()->sync($data['category'], true);
        
        return $product;
    }
    
    /**
     * @param  int  $id
     *
     * @return int
     */
    public function deleteProduct(int $id): int
    {
        return Product::destroy($id);
    }
    
    /**
     * Make paths for storing images.
     *
     * @return object
     */
    public function makePaths(): object
    {
        $original  = public_path().'/uploads/images/products/';
        $thumbnail = public_path().'/uploads/images/products/thumbnails/';
        $medium    = public_path().'/uploads/images/products/medium/';
        
        return (object)compact('original', 'thumbnail', 'medium');
    }
}
