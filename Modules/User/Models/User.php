<?php

declare(strict_types=1);

/**
 * Created by Zoran Shefot Bogoevski.
 */

namespace Modules\User\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Models\Cart;
use Modules\Core\Traits\ClearsCache;
use Modules\Google2fa\Models\Google2fa;
use Modules\Notification\Models\Notification;
use Modules\Order\Models\Order;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostComment;
use Modules\Product\Models\ProductReview;
use Modules\User\Database\Factories\UserFactory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $photo
 * @property string|null $provider
 * @property string|null $provider_id
 * @property string $status
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|Post[] $posts
 * @property-read int|null $carts_count
 * @property-read int|null $orders_count
 * @property-read int|null $post_comments_count
 * @property-read int|null $posts_count
 * @property-read int|null $product_reviews_count
 * @property-read int|null $wishlists_count
 *
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePhoto($value)
 * @method static Builder|User whereProvider($value)
 * @method static Builder|User whereProviderId($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder|User permission($permissions)
 * @method static Builder|User role($roles, $guard = null)
 *
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Cart[] $carts
 * @property-read Collection|Order[] $orders
 * @property-read Collection|PostComment[] $post_comments
 * @property-read Collection|ProductReview[] $product_reviews
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection|Wishlist[] $wishlists
 */
class User extends Authenticatable
{
    use ClearsCache;
    use HasApiTokens;
    use HasFactory;
    use HasPermissions;
    use HasRoles;
    use Impersonate;
    use Notifiable;

    protected $table = 'users';

    /**
     * @var string[]
     */
    protected array $dates = [
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'status',
        'remember_token',
    ];

    public static function Factory(): UserFactory
    {
        return UserFactory::new();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function post_comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'added_by');
    }

    public function product_reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Determine if the user is a super-admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function loginSecurity(): HasOne
    {
        return $this->hasOne(Google2fa::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
            ->whereNull('read_at')
            ->where('notifiable_type', get_class($this));
    }
}
