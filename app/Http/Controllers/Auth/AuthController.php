<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Requests\AuthRequest;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Modules\User\Models\User;

    class AuthController extends Controller
    {
        /**
         * @param  Request  $request
         *
         * @return JsonResponse
         */
        public function login(Request $request)
        {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authUser         = Auth::user();
                $success['token'] = $authUser->createToken('MyAuthApp')->plainTextToken;
                $success['name']  = $authUser->name;

                return $this->sendResponse($success, 'User signed in');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
            }
        }

        /**
         * @param  AuthRequest  $request
         *
         * @return JsonResponse
         */
        public function register(AuthRequest $request)
        {
            $request['password'] = bcrypt($request['password']);
            $user                = User::create($request->all());
            $success['token']    = $user->createToken('MyAuthApp')->plainTextToken;
            $success['name']     = $user->name;

            return $this->sendResponse($success, 'User created successfully.');
        }
    }
