<?php

namespace Modules\Product\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;

class ProductReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('review-list');
    }

    public function view(User $user): bool
    {
        return $user->can('review-list');
    }

    public function create(User $user): bool
    {
        return $user->can('review-create');
    }

    public function update(User $user, ProductReview $review): bool
    {
        return $user->can('review-update');
    }

    public function delete(User $user, ProductReview $review): bool
    {
        return $user->can('review-delete');
    }

}
