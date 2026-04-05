<?php

declare(strict_types=1);

namespace Modules\User\Http\Controllers;

use App\Mail\MagicLoginLink;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Core\Http\Controllers\Controller;
use Modules\User\Models\User;

class MagicLoginController extends Controller
{
    public function showLoginForm(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('auth.magic_login');
    }

    public function sendToken(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $user->magic_token = Str::random(50);
        $user->token_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Send the magic link. Implement this in your Mail class.
        Mail::to($user->email)->send(new MagicLoginLink($user));

        return back()->with('magic_link_sent', 'We have emailed you a magic link!');
    }

    public function login(string $token): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($token): User {
            $user = User::whereMagicToken($token)
                ->where('token_expires_at', '>', Carbon::now())
                ->lockForUpdate()
                ->firstOrFail();

            $user->magic_token = null;
            $user->token_expires_at = null;
            $user->save();

            return $user;
        });

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect('/admin');
    }
}
