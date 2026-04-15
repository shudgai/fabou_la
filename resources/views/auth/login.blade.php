@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-slate-50 flex items-center justify-center p-6 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-fixed">
    <div class="max-w-md w-full animate-fade-in-up">
        <!-- Logo/Icon Decor -->
        <div class="text-center mb-8">
            <div class="inline-flex h-20 w-20 bg-indigo-600 rounded-3xl items-center justify-center shadow-2xl shadow-indigo-200 mb-4 transform hover:rotate-12 transition-transform duration-300">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight font-outfit">歡迎回來</h2>
            <p class="text-slate-500 text-sm mt-2">請登錄您的帳號以接續管理法寶紀錄</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-[40px] shadow-[0_20px_70px_rgba(0,0,0,0.05)] border border-slate-100 p-8 md:p-10 relative overflow-hidden group">
            <!-- Decorative gradient line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-500 to-blue-500"></div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email field -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">電子郵件信箱</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-300 @error('email') ring-2 ring-red-500/20 @enderror"
                            placeholder="example@mail.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[11px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest">密碼</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-indigo-500 hover:text-indigo-700 transition-colors uppercase tracking-wider">忘記密碼？</a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-300 @error('password') ring-2 ring-red-500/20 @enderror"
                            placeholder="輸入您的密碼">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[11px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Extra -->
                <div class="flex items-center space-x-2 ml-1">
                    <input class="w-5 h-5 rounded-lg border-none bg-slate-100 text-indigo-600 focus:ring-offset-0 focus:ring-indigo-500/20 transition-all cursor-pointer" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="text-[13px] font-bold text-slate-500 cursor-pointer select-none" for="remember">
                        記住我
                    </label>
                </div>

                <!-- Action Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center">
                        登錄帳號
                        <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    @if (Route::has('register'))
                        <p class="text-center mt-6 text-slate-400 text-sm">
                            還沒有帳號嗎？ <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">立即註冊</a>
                        </p>
                    @endif
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
