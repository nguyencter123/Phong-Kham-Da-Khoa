<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(\App\Http\Requests\Auth\LoginRequest $request)
    {
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendFailedLoginResponse(\Illuminate\Http\Request $request)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => ['Email hoặc mật khẩu không chính xác'],
        ]);
    }

    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ['Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Quản trị viên.'],
            ]);
        }

        return match ($user->role) {
            'admin' => redirect('/admin'),
            'receptionist' => redirect('/receptionist'),
            'doctor' => redirect('/doctor'),
            'patient' => redirect('/patient'),
            default => abort(403, 'Role không hợp lệ hoặc chưa được gán quyền.'),
        };
        
        if (!$user->is_active) {

        auth()->logout();

        return redirect('/login')

            ->withErrors([

                'email' => 'Tài khoản của bạn đã bị khóa.'

            ]);
        }
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
