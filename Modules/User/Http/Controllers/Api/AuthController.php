<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers\Api;

use App\Requests\AuthRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Factory;
use Laravel\Socialite\Facades\Socialite;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\User\Actions\LoginUserAction;
use Modules\User\Actions\RegisterUserAction;
use Modules\User\Models\User;

class AuthController extends CoreController
{
    public function login(Request $request): JsonResponse
    {
        $result = (new LoginUserAction())->execute($request->email, $request->password);
        if ($result) {
            return $this->sendResponse($result, 'User signed in');
        }

        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    }

    public function register(AuthRequest $request): JsonResponse
    {
        $result = (new RegisterUserAction())->execute($request->all());

        return $this->sendResponse($result, 'User created successfully.');
    }

    /**
     * Handle Social login request
     *
     * @return RedirectResponse
     */
    public function socialLogin($social)
    {
        return Socialite::driver($social)->redirect();
    }

    /**
     * Obtain the user information from Social Logged in.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function handleProviderCallback($social)
    {
        $userSocial = Socialite::driver($social)->user();
        $user = User::where(['email' => $userSocial->getEmail()])->first();
        if ($user) {
            Auth::login($user);

            return redirect()->route('admin');
        }

        return view('auth.register', ['name' => $userSocial->getName(), 'email' => $userSocial->getEmail()]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
