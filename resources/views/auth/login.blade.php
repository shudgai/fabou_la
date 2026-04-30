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

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center space-x-3 text-red-600 animate-shake">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-base font-bold leading-tight">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3 text-emerald-600 animate-fade-in">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-base font-bold leading-tight">{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Dharma Name field -->
                <div class="space-y-2">
                    <label for="dharma_name" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">法號</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="dharma_name" type="text" name="dharma_name" value="{{ old('dharma_name') }}" required autocomplete="off" autofocus
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-300 @error('dharma_name') ring-2 ring-red-500/20 @enderror"
                            placeholder="輸入您的法號">
                    </div>
                    @error('dharma_name')
                        <p class="text-red-500 text-[11px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email field -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">電子郵件信箱</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email"
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
                        @if (Route::has('password.direct'))
                            <a href="{{ route('password.direct') }}" class="text-[13px] font-bold text-indigo-500 hover:text-indigo-700 transition-colors uppercase tracking-wider">直接重設密碼？</a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-12 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder-slate-300 @error('password') ring-2 ring-red-500/20 @enderror"
                            placeholder="輸入您的密碼">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg class="h-5 w-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="h-5 w-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.504-4.659M8.39 8.39a3 3 0 014.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[11px] font-bold mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Extra -->
                <div class="flex items-center space-x-2 ml-1">
                    <input class="w-5 h-5 rounded-lg border-none bg-slate-100 text-indigo-600 focus:ring-offset-0 focus:ring-indigo-500/20 transition-all cursor-pointer" type="checkbox" name="remember" id="remember" {{ old('remember') || !old('_token') ? 'checked' : '' }}>
                    <label class="text-[13px] font-bold text-slate-500 cursor-pointer select-none" for="remember">
                        記住我
                    </label>
                </div>

                <!-- Action Button -->
                <div class="pt-2 space-y-4">
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center">
                        登錄帳號
                        <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-100"></div>
                        <span class="flex-shrink mx-4 text-slate-300 text-[10px] font-bold uppercase tracking-widest">或使用社會化登入</span>
                        <div class="flex-grow border-t border-slate-100"></div>
                    </div>

                    <a href="{{ route('auth.google') }}" class="w-full bg-white text-slate-700 font-bold py-4 rounded-2xl border border-slate-100 shadow-sm hover:bg-slate-50 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center space-x-3 group">
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        <span class="group-hover:text-indigo-600 transition-colors">使用 Google 帳號登入</span>
                    </a>

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

@endsection

@push('scripts')
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const eyeOpen = el.querySelector('.eye-open');
    const eyeClosed = el.querySelector('.eye-closed');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

// Persistence logic
document.addEventListener('DOMContentLoaded', function() {
    const dharmaInput = document.getElementById('dharma_name');
    const emailInput = document.getElementById('email');
    const rememberCheckbox = document.getElementById('remember');
    const loginForm = document.querySelector('form');

    // Load from localStorage
    if (localStorage.getItem('saved_dharma_name')) {
        dharmaInput.value = localStorage.getItem('saved_dharma_name');
    }
    if (localStorage.getItem('saved_email')) {
        emailInput.value = localStorage.getItem('saved_email');
    }

    // Save on submit
    loginForm.addEventListener('submit', function() {
        if (rememberCheckbox.checked) {
            localStorage.setItem('saved_dharma_name', dharmaInput.value);
            localStorage.setItem('saved_email', emailInput.value);
        } else {
            localStorage.removeItem('saved_dharma_name');
            localStorage.removeItem('saved_email');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}
.animate-shake {
    animation: shake 0.4s ease-in-out infinite;
    animation-iteration-count: 2;
}
</style>
@endpush
