<?php

namespace Modules\User\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Modules\User\Service\UserService;

class UserController extends Controller
{
    
    private UserService $user_service;
    
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }
    
    public function index()
    {
    }
    
}