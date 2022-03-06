<?php

    namespace Modules\User\Http\Controllers\API;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Request;
    use Modules\User\Http\Requests\LoginRequest;
    use Modules\User\Models\User;

    class PassportAuthController extends Controller
    {
        /**
         * @param  LoginRequest  $request
         *
         * @return JsonResponse
         */
        public function register(LoginRequest $request)
        {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = $user->createToken('LaravelAuthApp')->accessToken;

            return response()->json(['token' => $token], 200);
        }

        /**
         * @param  Request  $request
         *
         * @return JsonResponse
         */
        public function login(Request $request)
        {
            if (auth()->attempt(['email' => $request['email'], 'password' => $request['password']])) {
                $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;

                return response()->json(['token' => $token], 200);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        }
    }