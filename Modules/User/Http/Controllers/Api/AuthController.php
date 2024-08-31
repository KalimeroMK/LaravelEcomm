<?php

namespace Modules\User\Http\Controllers\Api;

use App\Requests\AuthRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\Factory;
use Laravel\Socialite\Facades\Socialite;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\User\Models\User;

class AuthController extends CoreController
{
    /**
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $success['token'] = Auth::user()->createToken('MyAuthApp')->plainTextToken;
            $success['name'] = Auth::user()->name;

            return $this->sendResponse($success, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    /**
     * @return JsonResponse
     */
    public function register(AuthRequest $request)
    {
        $request['password'] = Hash::make($request['password']);
        $user = User::create($request->all());
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User created successfully.');
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
        } else {
            return view('auth.register', ['name' => $userSocial->getName(), 'email' => $userSocial->getEmail()]);
        }
    }
}
