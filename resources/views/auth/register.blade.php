@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-slate-50 flex items-center justify-center p-6 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-fixed py-12">
    <div class="max-w-md w-full animate-fade-in-up">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex h-20 w-20 bg-emerald-600 rounded-3xl items-center justify-center shadow-2xl shadow-emerald-200 mb-4 transform hover:-rotate-12 transition-transform duration-300">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight font-outfit">加入筆記本系統</h2>
            <p class="text-slate-500 text-sm mt-2">建立您的個人帳號以開始紀錄與管理</p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-[40px] shadow-[0_20px_70px_rgba(0,0,0,0.05)] border border-slate-100 p-8 md:p-10 relative overflow-hidden group">
            <!-- Decorative gradient line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name field -->
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">真實姓名 / 稱呼</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-300 @error('name') ring-2 ring-red-500/20 @enderror"
                            placeholder="請輸入您的姓名">
                    </div>
                    @error('name')
                        <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email field -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">電子郵件信箱</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-300 @error('email') ring-2 ring-red-500/20 @enderror"
                            placeholder="example@mail.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">設定密碼</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-300 @error('password') ring-2 ring-red-500/20 @enderror"
                            placeholder="請設計至少八位數密碼">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5 pb-2">
                    <label for="password-confirm" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">確認密碼</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all placeholder-slate-300"
                            placeholder="請再次輸入相同密碼">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center">
                        完成註冊並登錄
                        <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <p class="text-center mt-6 text-slate-400 text-sm">
                        已經有帳號了嗎？ <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">返回登錄</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer Decor -->
        <p class="text-center mt-12 text-slate-300 text-xs font-bold uppercase tracking-[0.2em]">© {{ date('Y') }} FABOU SYSTEM v2.0</p>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}
</style>
@endsection
