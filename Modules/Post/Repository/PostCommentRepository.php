<?php

namespace Modules\Post\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Post\Models\PostComment;

class PostCommentRepository
{
    
    /**
     * @return LengthAwarePaginator
     */
    public static function getAllComments(): LengthAwarePaginator
    {
        return PostComment::with(['user_info', 'post'])->paginate(10);
    }
    
    /**
     * @return LengthAwarePaginator
     */
    public static function getAllUserComments(): LengthAwarePaginator
    {
        return PostComment::where('user_id', auth()->user()->id)->with('user_info')->paginate(10);
    }
    
    public function getById($id): Model|PostComment|Collection|_IH_Banner_C|array
    {
        return PostComment::findOrFail($id);
    }
    
}