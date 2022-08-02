<?php

namespace Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;
use Modules\Newsletter\Database\Factories\NewsletterFactory;

class Newsletter extends Core
{
    use HasFactory;
    
    protected $table = 'newsletters';
    
    protected $casts = [
        'is_validated' => 'bool',
    ];
    
    protected $hidden = [
        'token',
    ];
    
    protected $fillable = [
        'email',
        'token',
        'is_validated',
    ];
    
    /**
     * @return NewsletterFactory
     */
    public static function Factory(): NewsletterFactory
    {
        return NewsletterFactory::new();
    }
    
}