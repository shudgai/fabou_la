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
     * 驗證登入請求，加入法號必填驗證，電子郵件改為非必填（若法號可唯一識別）。
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'dharma_name' => ['required', 'string'],
            'email'       => ['nullable', 'string', 'email'],
            'password'    => ['required', 'string'],
        ], [
            'dharma_name.required' => '請輸入法號。',
            'password.required'    => '請輸入密碼。',
        ]);
    }

    /**
     * 嘗試登入前，處理法號與 Email 的對應關係。
     */
    protected function attemptLogin(Request $request)
    {
        $dNameInput = trim($request->input('dharma_name'));
        $emailInput = trim($request->input('email'));
        $password = $request->input('password');

        // 如果有提供 Email，優先用 Email 找人
        if ($emailInput) {
            $user = \App\Models\User::where('email', $emailInput)->first();
        } else {
            // 如果沒提供 Email，嘗試用法號找人 (DharmaName 關聯或 User 自身的 name)
            $user = \App\Models\User::whereHas('dharmaName', function($q) use ($dNameInput) {
                $q->where('name', $dNameInput);
            })->orWhere('name', $dNameInput)->first();
        }

        if (! $user) {
            return false;
        }

        // 驗證法號是否匹配 (雙重確認)
        $userDName = trim($user->dharmaName ? $user->dharmaName->name : $user->name);
        if ($userDName !== $dNameInput) {
            throw ValidationException::withMessages([
                'dharma_name' => ['此法號與帳號資料不符。'],
            ]);
        }

        // 執行登入
        return $this->guard()->attempt(
            ['email' => $user->email, 'password' => $password],
            $request->boolean('remember')
        );
    }
}
