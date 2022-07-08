<?php

namespace Modules\Banner\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;

class BannerRepository
{
    
    /**
     * @return Builder|Banner
     */
    public function getAll(): Builder|Banner
    {
        return Banner::orderBy('id', 'DESC');
    }
    
    /**
     * @param $id
     *
     * @return Model|Banner|Collection|_IH_Banner_C|array
     */
    public function getById($id): Model|Banner|Collection|_IH_Banner_C|array
    {
        return Banner::findOrFail($id);
    }
    
}
