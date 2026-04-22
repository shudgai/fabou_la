<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DharmaName;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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

    /**
     * 驗證登入請求，加入法號必填驗證。
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'dharma_name' => ['required', 'string'],
            'email'       => ['required', 'string'],
            'password'    => ['required', 'string'],
        ], [
            'dharma_name.required' => '請輸入法號。',
            'email.required'       => '請輸入電子郵件。',
            'password.required'    => '請輸入密碼。',
        ]);
    }

    /**
     * 嘗試登入前，先驗證法號是否存在於 dharma_names 表中。
     */
    protected function attemptLogin(Request $request)
    {
        $dharmaNameInput = trim($request->input('dharma_name'));

        // 查詢 dharma_names 表中是否有這個法號
        $exists = DharmaName::where('name', $dharmaNameInput)->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'dharma_name' => ['此法號不在法號表中，無法登入。'],
            ]);
        }

        // 法號驗證通過，繼續正常的 email + password 驗證
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }
}
