<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.center');
        }
        
        return view('themes.morpho.views.auth.auth');
    }

    public function login(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::guard('web')->user();
            $user->updateLastLogin($request->ip());
            
            RateLimiter::clear($this->throttleKey($request));

            return redirect()->intended(route('user.center'));
        }

        RateLimiter::hit($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.center');
        }

        return view('themes.morpho.views.auth.auth');
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nickname' => ['nullable', 'string', 'max:50'],
            'agree_terms' => ['required', 'accepted'],
        ]);

        $defaultLevel = UserLevel::where('status', 1)->orderBy('min_spent')->first();

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nickname' => $request->nickname ?: substr($request->email, 0, strpos($request->email, '@')),
            'level_id' => $defaultLevel ? $defaultLevel->id : 1,
            'status' => User::STATUS_ACTIVE,
            'balance' => 0,
            'total_spent' => 0,
        ]);

        event(new Registered($user));

        Auth::guard('web')->login($user);

        return redirect()->route('user.center')->with('success', '注册成功！');
    }

    public function showForgotPassword()
    {
        return view('themes.morpho.views.auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('themes.morpho.views.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    public function verifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('user.center');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '验证邮件已发送！');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, '无效的验证链接');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('user.center')->with('message', '邮箱已经验证过了');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('user.center')->with('success', '邮箱验证成功！');
    }

    public function showVerifyNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail() 
            ? redirect()->route('user.center') 
            : view('themes.morpho.views.auth.verify-email');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('user.center');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '验证链接已重新发送！');
    }
}