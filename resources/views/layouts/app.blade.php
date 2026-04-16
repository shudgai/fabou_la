<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>皇恩筆記本</title>

    <!-- Fonts: Inter & Outfit for a premium feel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.4.21/mammoth.browser.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
        }

        h1,
        h2,
        h3,
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
        /* Diagnostic: Force a red line at the very top */
    </style>
    @stack('styles')
</head>

<body class="antialiased text-slate-900 bg-white">
    <div class="min-h-screen flex flex-col">
        <!-- Retractable Sidebar -->
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex-1 flex overflow-hidden">
            <!-- Sidebar backdrop for mobile -->
            <div x-show="sidebarOpen" x-cloak
                @click="sidebarOpen = false" 
                class="fixed inset-0 z-[150] bg-slate-900/40 backdrop-blur-sm lg:hidden"
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
                class="fixed inset-y-0 left-0 z-[200] bg-white border-r border-slate-200 shadow-2xl transition-all duration-300 lg:static lg:translate-x-0 flex flex-col">
                
                <!-- Sidebar Header -->
                <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 shrink-0 overflow-hidden">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 transition-transform active:scale-95">
                        <div class="w-8 h-8 bg-slate-900 rounded-full flex items-center justify-center shadow-lg shrink-0 overflow-hidden border border-slate-800">
                            <!-- Taiji SVG (Scaled) -->
                            <svg class="w-full h-full p-0.5" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="50" cy="50" r="50" fill="white"/>
                                <path d="M50 0C22.3858 0 0 22.3858 0 50C0 77.6142 22.3858 100 50 100C50 75 50 75 50 50C50 25 50 25 50 0Z" fill="white"/>
                                <path d="M50 0C77.6142 0 100 22.3858 100 50C100 77.6142 77.6142 100 50 100V50V0Z" fill="black"/>
                                <path d="M50 100C36.1929 100 25 88.8071 25 75C25 61.1929 36.1929 50 50 50V100Z" fill="black"/>
                                <path d="M50 50C63.8071 50 75 38.8071 75 25C75 11.1929 63.8071 0 50 0V50Z" fill="white"/>
                                <circle cx="50" cy="75" r="8" fill="white"/>
                                <circle cx="50" cy="25" r="8" fill="black"/>
                            </svg>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-xl font-bold font-outfit tracking-tight text-slate-800 whitespace-nowrap" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            皇恩筆記本
                        </span>
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
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">首頁</span>
                        </a>

                        <a href="{{ route('note.index') }}"
                            class="flex items-center px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('note.index') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="w-8 h-8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">我的筆記本</span>
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
                                <span x-show="!sidebarCollapsed" class="ml-3 font-semibold text-sm" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">系統管理</span>
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Sidebar Footer (User Info & Collapse Toggle) -->
                <div class="p-4 border-t border-slate-100 space-y-4">
                    @auth
                        <div class="flex items-center" :class="{ 'justify-center': sidebarCollapsed, 'space-x-3': !sidebarCollapsed }">
                            <div class="w-9 h-9 border-2 border-slate-200 rounded-full flex items-center justify-center bg-slate-50 text-slate-500 font-bold text-xs shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div x-show="!sidebarCollapsed" class="min-w-0 flex-1 overflow-hidden" x-transition:enter="delay-100 transition-opacity" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <span class="font-outfit font-bold text-slate-800">皇恩筆記本</span>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->name }}</p>
                                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-[10px] text-red-500 font-medium hover:underline">登出</button>
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
                <!-- Mobile Header (Hidden on Note pages as they provide their own optimized headers) -->
                @if(!request()->routeIs('note.index'))
                <header class="lg:hidden h-14 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-4 z-[100] sticky top-0">
                    <button @click="sidebarOpen = true" class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg active:scale-90 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="w-8 h-8 bg-slate-900 rounded-full flex items-center justify-center shadow-lg shrink-0 overflow-hidden border border-slate-800">
                        <svg class="w-full h-full p-0.5" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="50" cy="50" r="50" fill="white"/>
                            <path d="M50 0C22.3858 0 0 22.3858 0 50C0 77.6142 22.3858 100 50 100C50 75 50 75 50 50C50 25 50 25 50 0Z" fill="white"/>
                            <path d="M50 0C77.6142 0 100 22.3858 100 50C100 77.6142 77.6142 100 50 100V50V0Z" fill="black"/>
                            <path d="M50 100C36.1929 100 25 88.8071 25 75C25 61.1929 36.1929 50 50 50V100Z" fill="black"/>
                            <path d="M50 50C63.8071 50 75 38.8071 75 25C75 11.1929 63.8071 0 50 0V50Z" fill="white"/>
                            <circle cx="50" cy="75" r="8" fill="white"/>
                            <circle cx="50" cy="25" r="8" fill="black"/>
                        </svg>
                    </div>
                </header>
                @endif

                <main id="app" class="flex-1 overflow-y-auto relative bg-white">
                    @yield('content')
                </main>
            </div> <!-- End Main Content Area -->
        </div> <!-- End x-data flex wrapper -->
    </div> <!-- End #app -->
</body>

</html>