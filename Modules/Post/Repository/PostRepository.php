<?php

namespace Modules\Post\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;
use Modules\Post\Models\Post;

class PostRepository
{
    /**
     * @return LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
     */
    public function getAll(): LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        return Post::with(['author_info', 'categories'])->orderBy('id', 'DESC')->paginate(10);
    }
    
    /**
     * @param $id
     *
     * @return Model|Banner|Collection|_IH_Banner_C|array
     */
    public function getById($id): Model|Banner|Collection|_IH_Banner_C|array
    {
        return Post::findOrFail($id);
    }
    
}