@extends('layouts.app')

@section('content')
{{-- Immersive full-screen overlay covering sidebar & mobile header --}}
<div class="fixed inset-0 z-[9999] overflow-y-auto flex bg-white" id="immersive-register" v-pre>

    {{-- Subtle warm tint top-left --}}
    <div class="absolute inset-0 pointer-events-none" style="
        background:
            radial-gradient(ellipse at 5% 5%, rgba(220,38,38,0.04) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 95%, rgba(220,38,38,0.03) 0%, transparent 50%);
    "></div>

    {{-- ── Layout ── --}}
    <div class="relative flex flex-col md:flex-row w-full min-h-full">

        {{-- ── LEFT BRAND PANEL (desktop only) ── --}}
        <div class="hidden md:flex md:w-[42%] flex-col items-center justify-center px-12 lg:px-16 py-16 relative"
             style="background: linear-gradient(160deg, #fff5f5 0%, #fff 60%);">
            {{-- Vertical red accent line --}}
            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-px h-2/3"
                 style="background: linear-gradient(to bottom, transparent, #e2e8f0, transparent);"></div>

            <div class="text-center animate-brand-in">
                <div class="flex flex-col items-center justify-center mb-6 py-4">

                    <div class="relative z-10 flex items-center justify-center px-4 transition-transform duration-300 hover:scale-105">
                        <svg class="w-56 h-56 drop-shadow-lg" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Golden decorative rings -->
                            <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
                            <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            
                            <!-- Central Taiji -->
                            <g transform="translate(70, 70)">
                                <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                                <path d="M50 1C22.936 1 1 22.936 1 50C1 77.064 22.936 99 50 99C50 74.5 50 74.5 50 50C50 25.5 50 25.5 50 1Z" fill="white"/>
                                <path d="M50 1C77.064 1 99 22.936 99 50C99 77.064 77.064 99 50 99V50V1Z" fill="black"/>
                                <path d="M50 99C36.469 99 25.5 88.031 25.5 74.5C25.5 60.969 36.469 50 50 50V99Z" fill="black"/>
                                <path d="M50 50C63.531 50 74.5 39.031 74.5 25.5C74.5 11.969 63.531 1 50 1V50Z" fill="white"/>
                                <circle cx="50" cy="74.5" r="8" fill="white"/>
                                <circle cx="50" cy="25.5" r="8" fill="black"/>
                            </g>

                            <!-- Circular Text wrapping around -->
                            <defs>
                                <path id="badgeArc" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
                            </defs>
                            <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                                <textPath href="#badgeArc" startOffset="50%" text-anchor="middle" letter-spacing="10">
                                    皇恩筆記本
                                </textPath>
                            </text>
                            
                            <!-- Bottom decorative elements: 9 dots arranged up and down (zigzag) and spread out -->
                            <!-- Inner dots (5) -->
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(30, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-30, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(60, 120, 120)" />
                            <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-60, 120, 120)" />
                            
                            <!-- Outer dots (4) -->
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(15, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-15, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(45, 120, 120)" />
                            <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-45, 120, 120)" />
                        </svg>
                    </div>
                </div>

                <h1 class="sr-only">皇恩筆記本</h1>
                <p class="text-slate-400 text-sm font-medium tracking-[0.2em] uppercase mb-12">
                    法寶管理系統
                </p>

                {{-- Decorative divider --}}
                <div class="flex items-center justify-center gap-4 mb-10">
                    <div class="h-px w-16 bg-slate-200"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-red-400"></div>
                    <div class="h-px w-16 bg-slate-200"></div>
                </div>

                <p class="text-slate-400 text-[13px] leading-relaxed max-w-xs mx-auto font-medium tracking-wide">
                    加入系統，開始記錄與管理<br>您的皇恩筆記
                </p>
            </div>
        </div>

        {{-- ── RIGHT FORM PANEL (One Thing at a Time) ── --}}
        <div class="flex-1 flex flex-col items-center justify-center p-[20px] md:p-[40px] lg:px-16" 
             x-data="registerFlow()" x-init="initFlow()">

            <div class="md:hidden flex flex-col items-center mb-8 animate-form-in" style="animation-delay: 0s;">

                <div class="relative z-10 flex items-center justify-center px-2 transition-transform duration-300 hover:scale-105">
                    <svg class="w-40 h-40 drop-shadow-md" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Golden decorative rings -->
                        <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
                        <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="1.5"/>
                        <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="1.5"/>
                        
                        <!-- Central Taiji -->
                        <g transform="translate(70, 70)">
                            <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                            <path d="M50 1C22.936 1 1 22.936 1 50C1 77.064 22.936 99 50 99C50 74.5 50 74.5 50 50C50 25.5 50 25.5 50 1Z" fill="white"/>
                            <path d="M50 1C77.064 1 99 22.936 99 50C99 77.064 77.064 99 50 99V50V1Z" fill="black"/>
                            <path d="M50 99C36.469 99 25.5 88.031 25.5 74.5C25.5 60.969 36.469 50 50 50V99Z" fill="black"/>
                            <path d="M50 50C63.531 50 74.5 39.031 74.5 25.5C74.5 11.969 63.531 1 50 1V50Z" fill="white"/>
                            <circle cx="50" cy="74.5" r="8" fill="white"/>
                            <circle cx="50" cy="25.5" r="8" fill="black"/>
                        </g>

                        <!-- Circular Text wrapping around -->
                        <defs>
                            <path id="badgeArcMobile" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
                        </defs>
                        <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                            <textPath href="#badgeArcMobile" startOffset="50%" text-anchor="middle" letter-spacing="10">
                                皇恩筆記本
                            </textPath>
                        </text>
                        
                        <!-- Bottom decorative elements: 9 dots arranged up and down (zigzag) and spread out -->
                        <!-- Inner dots (5) -->
                        <circle cx="120" cy="188" r="3.5" fill="#dc2626" />
                        <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(30, 120, 120)" />
                        <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-30, 120, 120)" />
                        <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(60, 120, 120)" />
                        <circle cx="120" cy="188" r="3.5" fill="#dc2626" transform="rotate(-60, 120, 120)" />
                        
                        <!-- Outer dots (4) -->
                        <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(15, 120, 120)" />
                        <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-15, 120, 120)" />
                        <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(45, 120, 120)" />
                        <circle cx="120" cy="208" r="3.5" fill="#dc2626" transform="rotate(-45, 120, 120)" />
                    </svg>
                </div>
                <h1 class="sr-only">皇恩筆記本</h1>
            </div>

            {{-- Form wrapper --}}
            <div class="w-full max-w-sm relative min-h-[350px] flex flex-col justify-center">

                {{-- Errors (Global) --}}
                @if ($errors->any())
                <div class="absolute top-0 inset-x-0 -mt-8 p-4 bg-red-50 border border-red-100 rounded-2xl z-50 animate-form-in">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                        <li class="text-red-500 text-[13px] font-bold">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="w-full relative" @keydown.enter.prevent="handleEnter">
                    @csrf

                    {{-- STEP 1: Name --}}
                    <div x-show="step === 1" 
                         x-transition:enter="transition-all duration-500 ease-out delay-150"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-300 ease-in absolute inset-0"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="w-full">
                         
                        <h2 class="text-slate-900 font-black tracking-widest font-outfit mb-2 whitespace-nowrap" style="font-size: 24px !important;">首先，請問您的法號？</h2>
                        <p class="text-slate-400 text-[14px] font-medium tracking-wider mb-10 whitespace-nowrap">這是您在系統中的唯一識別</p>

                        <input id="name" type="text" name="name" x-model="name" x-ref="nameInput" required autocomplete="name"
                            class="immersive-input w-full bg-transparent border-0 border-b-2 border-slate-200 text-slate-900 text-[26px] font-bold py-3 px-0 focus:border-red-500 focus:outline-none transition-colors placeholder-slate-200 tracking-wide"
                            placeholder="輸入法號">

                        <div class="mt-10">
                            <button type="button" @click="nextStep" :disabled="!name.trim()"
                                class="w-full py-4 rounded-2xl font-black text-[16px] tracking-widest transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white !important;">
                                下一步 →
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Email --}}
                    <div x-show="step === 2" style="display: none;"
                         x-transition:enter="transition-all duration-500 ease-out delay-150"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-300 ease-in absolute inset-0"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="w-full">
                         
                        <button type="button" @click="prevStep" class="text-slate-400 hover:text-slate-600 mb-6 flex items-center text-sm font-bold transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            返回
                        </button>

                        <h2 class="text-slate-900 font-black tracking-widest font-outfit mb-2 whitespace-nowrap" style="font-size: 24px !important;">聯絡信箱</h2>
                        <p class="text-slate-400 text-[14px] font-medium tracking-wider mb-10 whitespace-nowrap">作為未來登入與通知使用</p>

                        <input id="email" type="email" name="email" x-model="email" x-ref="emailInput" required autocomplete="email"
                            class="immersive-input w-full bg-transparent border-0 border-b-2 border-slate-200 text-slate-900 text-[22px] font-bold py-3 px-0 focus:border-red-500 focus:outline-none transition-colors placeholder-slate-200 tracking-wide"
                            placeholder="請輸入您的電子郵件">

                        <div class="mt-10">
                            <button type="button" @click="nextStep" :disabled="!email.trim() || !email.includes('@')"
                                class="w-full py-4 rounded-2xl font-black text-[16px] tracking-widest transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white !important;">
                                下一步 →
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: Password --}}
                    <div x-show="step === 3" style="display: none;"
                         x-transition:enter="transition-all duration-500 ease-out delay-150"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition-all duration-300 ease-in absolute inset-0"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="w-full">
                         
                        <button type="button" @click="prevStep" class="text-slate-400 hover:text-slate-600 mb-6 flex items-center text-sm font-bold transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            返回
                        </button>

                        <h2 class="text-slate-900 font-black tracking-widest font-outfit mb-2 whitespace-nowrap" style="font-size: 24px !important;">最後，設定安全密碼</h2>
                        <p class="text-slate-400 text-[14px] font-medium tracking-wider mb-8 whitespace-nowrap">密碼長度至少需 8 個字元</p>

                        <div class="space-y-6">
                            <div class="relative">
                                <label class="block text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] mb-2">密碼</label>
                                <input id="password" type="password" name="password" x-model="password" x-ref="passwordInput" required autocomplete="new-password"
                                    class="immersive-input w-full bg-transparent border-0 border-b-2 border-slate-200 text-slate-900 text-[20px] font-bold py-2 px-0 pr-10 focus:border-red-500 focus:outline-none transition-colors placeholder-slate-200 tracking-wide"
                                    placeholder="輸入密碼">
                                <button type="button" onclick="togglePassword('password', this)" class="absolute right-0 bottom-3 text-slate-300 hover:text-slate-500 transition-colors">
                                    <svg class="h-6 w-6 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg class="h-6 w-6 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.504-4.659M8.39 8.39a3 3 0 014.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </button>
                            </div>

                            <div class="relative">
                                <label class="block text-slate-400 text-[11px] font-black uppercase tracking-[0.2em] mb-2">確認密碼</label>
                                <input id="password-confirm" type="password" name="password_confirmation" x-model="password_confirmation" required autocomplete="new-password"
                                    class="immersive-input w-full bg-transparent border-0 border-b-2 border-slate-200 text-slate-900 text-[20px] font-bold py-2 px-0 pr-10 focus:border-red-500 focus:outline-none transition-colors placeholder-slate-200 tracking-wide"
                                    placeholder="再次輸入密碼">
                                <button type="button" onclick="togglePassword('password-confirm', this)" class="absolute right-0 bottom-3 text-slate-300 hover:text-slate-500 transition-colors">
                                    <svg class="h-6 w-6 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg class="h-6 w-6 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.504-4.659M8.39 8.39a3 3 0 014.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mt-10">
                            <button type="submit" :disabled="!password || password.length < 8 || password !== password_confirmation"
                                class="w-full py-4 rounded-2xl font-black text-[16px] tracking-widest transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_8px_24px_rgba(220,38,38,0.2)]"
                                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white !important;">
                                完成註冊並登入
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Step Indicator Dots --}}
                <div class="flex justify-center mt-12 space-x-3">
                    <div class="h-1.5 rounded-full transition-all duration-500" :class="step === 1 ? 'w-8 bg-red-500' : (step > 1 ? 'w-4 bg-red-200' : 'w-4 bg-slate-200')"></div>
                    <div class="h-1.5 rounded-full transition-all duration-500" :class="step === 2 ? 'w-8 bg-red-500' : (step > 2 ? 'w-4 bg-red-200' : 'w-4 bg-slate-200')"></div>
                    <div class="h-1.5 rounded-full transition-all duration-500" :class="step === 3 ? 'w-8 bg-red-500' : 'w-4 bg-slate-200'"></div>
                </div>

                {{-- Footer --}}
                <p class="text-center mt-8 text-slate-400 text-[13px] font-medium">
                    已有帳號？<a href="{{ route('login') }}" class="text-red-500 font-bold hover:text-red-600 transition-colors ml-1">返回登錄</a>
                </p>

            </div>
        </div>
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

document.addEventListener('alpine:init', () => {
    Alpine.data('registerFlow', () => ({
        step: 1,
        name: '{{ old("name", "") }}',
        email: '{{ old("email", "") }}',
        password: '',
        password_confirmation: '',
        
        initFlow() {
            // Auto jump to step with error if there are validation errors
            const errors = @json($errors->messages());
            if (Object.keys(errors).length > 0) {
                if (errors.name) this.step = 1;
                else if (errors.email) this.step = 2;
                else if (errors.password) this.step = 3;
            }
            
            // Focus first input
            setTimeout(() => {
                if (this.step === 1 && this.$refs.nameInput) this.$refs.nameInput.focus();
            }, 600);
        },
        
        nextStep() {
            if (this.step === 1 && this.name.trim()) {
                this.step = 2;
                setTimeout(() => this.$refs.emailInput.focus(), 600);
            } else if (this.step === 2 && this.email.trim() && this.email.includes('@')) {
                this.step = 3;
                setTimeout(() => this.$refs.passwordInput.focus(), 600);
            }
        },
        
        prevStep() {
            if (this.step > 1) {
                this.step--;
                setTimeout(() => {
                    if (this.step === 1) this.$refs.nameInput.focus();
                    if (this.step === 2) this.$refs.emailInput.focus();
                }, 600);
            }
        },
        
        handleEnter() {
            if (this.step === 1) {
                this.nextStep();
            } else if (this.step === 2) {
                this.nextStep();
            } else if (this.step === 3) {
                if (this.password && this.password.length >= 8 && this.password === this.password_confirmation) {
                    this.$el.closest('form').submit();
                }
            }
        }
    }));
});
</script>
@endpush

@push('styles')
<style>
@keyframes brandIn {
    from { opacity: 0; transform: translateX(-20px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes formIn {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-brand-in {
    animation: brandIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.animate-form-in {
    opacity: 0;
    animation: formIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.immersive-input {
    caret-color: #dc2626;
}
.immersive-input:-webkit-autofill,
.immersive-input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
    -webkit-text-fill-color: #0f172a !important;
    transition: background-color 5000s ease-in-out 0s;
}
</style>
@endpush
