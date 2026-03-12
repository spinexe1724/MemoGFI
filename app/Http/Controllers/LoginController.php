<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '/memos';

    /**
     * Jumlah maksimal percobaan login sebelum user dikunci.
     *
     * @var int
     */
    protected $maxAttempts = 3; // <-- Ganti angka ini untuk mengubah batas percobaan

    /**
     * Durasi (dalam menit) user akan dikunci setelah gagal login.
     *
     * @var int
     */
    protected $decayMinutes = 2; // <-- Ganti angka ini untuk mengubah durasi kunci

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Logika untuk menangani terlalu banyak percobaan login (throttling)
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Logika validasi error spesifik dari permintaan sebelumnya
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if (!$user) {
            $this->incrementLoginAttempts($request);
            throw ValidationException::withMessages([
                $this->username() => 'Email yang Anda masukan tidak terdaftar.',
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            $this->incrementLoginAttempts($request);
            throw ValidationException::withMessages([
                $this->username() => 'Password yang Anda masukan salah.',
            ]);
        }

        $this->clearLoginAttempts($request);
        $this->guard()->login($user, $request->boolean('remember'));

        return $this->sendLoginResponse($request);
    }
}
