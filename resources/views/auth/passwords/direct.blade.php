@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-slate-50 flex items-center justify-center p-6 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] bg-fixed">
    <div class="max-w-md w-full animate-fade-in-up">
        <!-- Logo/Icon Decor -->
        <div class="text-center mb-8">
            <div class="inline-flex h-20 w-20 bg-orange-500 rounded-3xl items-center justify-center shadow-2xl shadow-orange-100 mb-4 transform hover:rotate-12 transition-transform duration-300">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight font-outfit">快速重設密碼</h2>
            <p class="text-slate-500 text-sm mt-2">輸入帳號與新密碼即可立即完成重設</p>
        </div>

        <!-- Reset Card -->
        <div class="bg-white rounded-[40px] shadow-[0_20px_70px_rgba(0,0,0,0.05)] border border-slate-100 p-8 md:p-10 relative overflow-hidden group">
            <!-- Decorative gradient line -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-orange-400 to-rose-400"></div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center space-x-3 text-red-600 animate-shake">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span class="text-base font-bold leading-tight">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.direct.update') }}" class="space-y-6">
                @csrf

                <!-- Dharma Name field -->
                <div class="space-y-2">
                    <label for="dharma_name" class="text-sm font-bold text-slate-400 uppercase tracking-widest ml-1">法號</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="dharma_name" type="text" name="dharma_name" value="{{ old('dharma_name') }}" required autofocus
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-base focus:ring-2 focus:ring-orange-500/20 transition-all placeholder-slate-300 @error('dharma_name') ring-2 ring-red-500/20 @enderror"
                            placeholder="請輸入您的法號">
                    </div>
                </div>

                <!-- Email field -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-slate-400 uppercase tracking-widest ml-1">電子郵件信箱</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-base focus:ring-2 focus:ring-orange-500/20 transition-all placeholder-slate-300 @error('email') ring-2 ring-red-500/20 @enderror"
                            placeholder="請輸入您的帳號 Email">
                    </div>
                </div>

                <!-- Password field -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-slate-400 uppercase tracking-widest ml-1">新密碼</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full pl-12 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-base focus:ring-2 focus:ring-orange-500/20 transition-all placeholder-slate-300 @error('password') ring-2 ring-red-500/20 @enderror"
                            placeholder="輸入新密碼">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-500 transition-colors">
                            <svg class="h-5 w-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <svg class="h-5 w-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password field -->
                <div class="space-y-2">
                    <label for="password-confirm" class="text-sm font-bold text-slate-400 uppercase tracking-widest ml-1">確認新密碼</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full pl-12 pr-12 py-4 bg-slate-50 border-none rounded-2xl text-base focus:ring-2 focus:ring-orange-500/20 transition-all placeholder-slate-300"
                            placeholder="再次輸入新密碼">
                        <button type="button" onclick="togglePassword('password-confirm', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-500 transition-colors">
                            <svg class="h-5 w-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <svg class="h-5 w-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-500 text-white font-bold py-5 rounded-2xl shadow-xl shadow-orange-100 hover:bg-orange-600 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center text-lg">
                        立即更新密碼
                        <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Decor -->
        <p class="text-center mt-12 text-slate-300 text-xs font-bold uppercase tracking-[0.2em]">© {{ date('Y') }} FABOU SYSTEM v2.0</p>
    </div>
</div>

<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const eyeOpen = button.querySelector('.eye-open');
    const eyeClosed = button.querySelector('.eye-closed');
    
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
</script>
@endsection

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
.hidden { display: none; }
</style>
@endpush
