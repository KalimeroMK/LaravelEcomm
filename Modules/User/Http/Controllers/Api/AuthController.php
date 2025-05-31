<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\User\Actions\LoginUserAction;
use Modules\User\Actions\RegisterUserAction;
use Modules\User\DTOs\UserDTO;
use Modules\User\Http\Requests\Store;
use Modules\User\Http\Resource\UserResource;
use Modules\User\Repository\UserRepository;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends CoreController
{
    public function __construct(
        public readonly UserRepository $repository,
        private readonly LoginUserAction $loginAction,
        private readonly RegisterUserAction $registerAction
    ) {
        // Optional: place auth middleware here if needed
    }

    public function login(Request $request): JsonResponse
    {
        $result = $this->loginAction->execute($request->email, $request->password);

        if ($result) {
            return $this->sendResponse($result, 'User signed in');
        }

        return $this->sendError('Unauthorised.', ['error' => 'Unauthorised'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @throws ReflectionException
     */
    public function register(Store $request): JsonResponse
    {
        $user = $this->registerAction->execute(UserDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond([
                'user' => new UserResource($user),
                'token' => $user->createToken('MyAuthApp')->plainTextToken,
            ]);
    }

    public function socialLogin(string $social): RedirectResponse
    {
        return Socialite::driver($social)->redirect();
    }

    public function handleProviderCallback(string $social): RedirectResponse|JsonResponse
    {
        $userSocial = Socialite::driver($social)->user();

        $user = $this->repository->findByEmail($userSocial->getEmail());

        if ($user) {
            Auth::login($user);

            return redirect()->route('admin');
        }

        return response()->json([
            'name' => $userSocial->getName(),
            'email' => $userSocial->getEmail(),
            'message' => 'User not found. Please register.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->tokens()?->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
