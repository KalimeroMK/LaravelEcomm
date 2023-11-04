<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Core\Traits\ApiResponses;

class CoreController extends Controller
{
    use ApiResponses;
    use AuthorizesRequests;
}
