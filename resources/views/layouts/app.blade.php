<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no" class="notranslate">

<head>
    <!-- 字體大小初始化：讀 localStorage，避免頁面閃爍 -->
    <script>
        (function () {
            try {
                var size = localStorage.getItem('fabou_font_size') || 'font-medium';
                document.documentElement.className = size + ' notranslate';
            } catch (e) {
                // iOS Private Browsing: localStorage throws SecurityError
                document.documentElement.className = 'font-medium notranslate';
            }
        })();
    </script>
    <meta charset="utf-8">
    <meta name="google" content="notranslate">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>皇恩筆記本</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo_v3.svg') }}?v=3">
    
    <!-- Web App Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">


    <!-- Fonts: minimal set with font-display: swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700;900&family=Outfit:wght@400;500;600;700;900&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Preload Critical Images removed to avoid "unused preload" browser warnings in SPA -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>
        body {
            font-family: 'Outfit', 'Noto Sans TC', sans-serif;
            background-color: #ffffff;
        }

        h1,
        h2,
        h3 {
            font-family: 'Noto Sans TC', sans-serif !important;
            font-weight: 700 !important;
        }
        
        .font-noto { font-family: 'Noto Sans TC', sans-serif !important; }
    </style>
    @stack('styles')
</head>

<body class="antialiased text-slate-900 bg-white font-medium">
    <div class="min-h-screen flex flex-col">
        <!-- Retractable Sidebar -->
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex-1 flex {{ request()->routeIs('note.index') ? '' : 'overflow-hidden' }}">
            <!-- Sidebar backdrop for mobile -->
            <div x-show="sidebarOpen" x-cloak
                @click="sidebarOpen = false" 
                class="fixed inset-0 z-[150] bg-slate-900/40 lg:hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0">
            </div>

            <aside 
                x-cloak
                :class="{ 
                    'translate-x-0': sidebarOpen, 
                    '-translate-x-full': !sidebarOpen,
                    'w-[90%] lg:w-64': !sidebarCollapsed,
                    'w-[90%] lg:w-20': sidebarCollapsed
                }"
                class="fixed inset-y-0 left-0 z-[200] bg-white border-r border-slate-200 shadow-2xl transition-all duration-300 lg:static lg:translate-x-0 flex flex-col"
                style="padding-top: env(safe-area-inset-top, 0px); padding-bottom: env(safe-area-inset-bottom, 0px);">
                
                <!-- Sidebar Header -->
                <div class="h-auto flex items-center justify-between px-4 border-b border-slate-100 shrink-0 overflow-hidden">
                    <a href="{{ url('/') }}" class="inline-flex flex-col items-center justify-center transition-transform active:scale-95 py-2 w-full">
                        <svg class="transition-all duration-300 drop-shadow-sm" :class="sidebarCollapsed ? 'w-10 h-10' : 'w-16 h-16'" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Golden decorative rings -->
                            <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
                            <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="1.5"/>
                            
                            <!-- Central Taiji -->
                            <g transform="translate(70, 70)">
                                <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                                <mask id="taiji-mask-sidebar">
                                    <rect x="50" y="-10" width="60" height="120" fill="white"></rect>
                                    <circle cx="50" cy="25.5" r="24.5" fill="white"></circle>
                                    <circle cx="50" cy="74.5" r="24.5" fill="black"></circle>
                                </mask>
                                <circle cx="50" cy="50" r="49" fill="black" mask="url(#taiji-mask-sidebar)"></circle>
                                <circle cx="50" cy="74.5" r="8" fill="white"/>
                                <circle cx="50" cy="25.5" r="8" fill="black"/>
                            </g>

                            <!-- Circular Text wrapping around -->
                            <defs>
                                <path id="badgeArcSidebar" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
                            </defs>
                            <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                                <textPath href="#badgeArcSidebar" startOffset="50%" text-anchor="middle" letter-spacing="10">
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
                    </a>

                    <!-- Mobile Close / Desktop Collapse Toggle -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex p-1.5 rounded-lg bg-slate-50 text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg :class="{ 'rotate-180': sidebarCollapsed }" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M11 19l-7-7 7-7m8 14l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    @auth
                        <a href="{{ route('home') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">首頁</span>
                        </a>

                        <a href="{{ route('note.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('note.index') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">我的筆記本</span>
                        </a>

                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.management') }}"
                                class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.management') ? 'bg-rose-50 text-rose-700 shadow-sm shadow-rose-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2" />
                                        <circle cx="12" cy="12" r="3" stroke-width="2" />
                                    </svg>
                                </div>
                                <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">系統管理</span>
                            </a>
                        @endif
                    @else
                        <!-- Guest Links -->
                        <a href="{{ url('/') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->is('/') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">首頁</span>
                        </a>
                        <a href="{{ route('login') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('login') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">立即登錄</span>
                        </a>
                        <a href="{{ route('register') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('register') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm md:text-lg" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">註冊帳戶</span>
                        </a>
                    @endauth
                </div>

                <!-- Sidebar Footer (User Info & Collapse Toggle) -->
                <div class="p-4 border-t border-slate-100 space-y-4">
                    @auth
                        <div class="flex items-center" :class="{ 'justify-center': sidebarCollapsed, 'space-x-3': !sidebarCollapsed }">
                            <div x-show="!sidebarCollapsed" class="min-w-0 flex-1 overflow-hidden text-center" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <div class="text-[17px] font-black text-slate-900 leading-none mb-1.5">{{ Auth::user()->display_name }}</div>
                                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-[13px] text-red-500 font-bold hover:underline tracking-widest">登出</button>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    @endauth


                    <!-- Desktop Toggle Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:flex w-full items-center justify-center p-2 rounded-xl bg-slate-50 text-slate-400 hover:text-indigo-600 transition-colors">
                        <svg :class="{ 'rotate-180': sidebarCollapsed }" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M11 19l-7-7 7-7m8 14l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 relative">
                <!-- Mobile Header -->
                @if(!request()->routeIs('note.index'))
                <header class="lg:hidden h-14 bg-white border-b border-white flex items-center justify-between px-3 z-[100] sticky top-0 shrink-0" style="padding-top: env(safe-area-inset-top, 0px); height: calc(3.5rem + env(safe-area-inset-top, 0px));">
                    <div class="flex items-center space-x-1">
                        <button @click="sidebarOpen = true" class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg active:scale-90 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        
                        <div class="inline-flex items-center relative h-12 px-1 overflow-visible">
                            <svg class="w-12 h-12 drop-shadow-sm" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Golden decorative rings -->
                                <circle cx="120" cy="120" r="114" fill="white" stroke="#eab308" stroke-width="4"/>
                                <circle cx="120" cy="120" r="108" fill="white" stroke="#eab308" stroke-width="1.5"/>
                                <circle cx="120" cy="120" r="58" fill="white" stroke="#eab308" stroke-width="1.5"/>
                                
                                <!-- Central Taiji -->
                                <g transform="translate(70, 70)">
                                    <circle cx="50" cy="50" r="49" fill="white" stroke="black" stroke-width="2"/>
                                    <mask id="taiji-mask-mobile">
                                        <rect x="50" y="-10" width="60" height="120" fill="white"></rect>
                                        <circle cx="50" cy="25.5" r="24.5" fill="white"></circle>
                                        <circle cx="50" cy="74.5" r="24.5" fill="black"></circle>
                                    </mask>
                                    <circle cx="50" cy="50" r="49" fill="black" mask="url(#taiji-mask-mobile)"></circle>
                                    <circle cx="50" cy="74.5" r="8" fill="white"/>
                                    <circle cx="50" cy="25.5" r="8" fill="black"/>
                                </g>

                                <!-- Circular Text wrapping around -->
                                <defs>
                                    <path id="badgeArcMobileLayout" d="M 71.9, 168.1 A 68,68 0 1,1 168.1, 168.1" />
                                </defs>
                                <text font-family="'BiauKai', 'DFKai-SB', 'PMingLiU', 'Noto Serif TC', serif" font-weight="900" font-size="38" fill="#dc2626">
                                    <textPath href="#badgeArcMobileLayout" startOffset="50%" text-anchor="middle" letter-spacing="10">
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
                    
                    <div class="flex items-center gap-2">
                        <!-- Placeholder to maintain flex layout -->
                    </div>
                </header>
                @endif

                <main id="app" class="flex-1 relative bg-white {{ request()->routeIs('note.index') ? '' : 'overflow-y-auto' }}">
                    @yield('content')
                </main>
            </div> <!-- End Main Content Area -->
        </div> <!-- End x-data flex wrapper -->
    </div> <!-- End #app -->

    @stack('scripts')

    <script>
    function fontSizeToggle() {
        return {
            options: [
                { key: 'font-small',  label: '小' },
                { key: 'font-medium', label: '中' },
                { key: 'font-large',  label: '大' },
            ],
            current: (function() { try { return localStorage.getItem('fabou_font_size') || 'font-medium'; } catch(e) { return 'font-medium'; } })(),
            get currentLabel() {
                return this.options.find(o => o.key === this.current)?.label || '中';
            },
            cycleSize() {
                let idx = this.options.findIndex(o => o.key === this.current);
                idx = (idx + 1) % this.options.length;
                const key = this.options[idx].key;
                const body = document.body;
                body.classList.remove('font-small', 'font-medium', 'font-large');
                body.classList.add(key);
                try { localStorage.setItem('fabou_font_size', key); } catch(e) {}
                this.current = key;
            },
            init() {
                try {
                    const saved = localStorage.getItem('fabou_font_size') || 'font-medium';
                    document.body.classList.remove('font-small', 'font-medium', 'font-large');
                    document.body.classList.add(saved);
                    this.current = saved;
                } catch(e) {
                    document.body.classList.remove('font-small', 'font-medium', 'font-large');
                    document.body.classList.add('font-medium');
                    this.current = 'font-medium';
                }
            }
        };
    }
    </script>
</body>

</html>
