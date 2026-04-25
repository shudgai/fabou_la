<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * 顯示快速重設密碼頁面
     */
    public function showDirectResetForm()
    {
        return view('auth.passwords.direct');
    }

    /**
     * 執行快速重設密碼（免郵件驗證，需核對法號）
     */
    public function directReset(Request $request)
    {
        $request->validate([
            'dharma_name' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'dharma_name.required' => '請輸入法號。',
            'email.exists' => '找不到該電子郵件地址。',
            'password.confirmed' => '兩次輸入的密碼不一致。',
            'password.min' => '密碼長度至少需要 8 個字元。',
        ]);

        $user = User::where('email', $request->email)->first();

        // 驗證法號是否匹配
        if (!$user->dharmaName || $user->dharmaName->name !== trim($request->dharma_name)) {
            throw ValidationException::withMessages([
                'dharma_name' => ['法號與此電子郵件不匹配，無法重設。'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', '密碼已成功直接重設！請使用新密碼登錄。');
    }
}
