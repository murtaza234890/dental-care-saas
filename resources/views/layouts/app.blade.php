<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'DentalCare' }} - Clinic Dashboard</title>
    <!-- Modern Google Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50">
    <div class="min-h-full flex">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0 border-r border-slate-800">
            <!-- Brand -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800/80 bg-slate-950/40">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-cyan-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold text-white tracking-tight">Dental<span class="text-cyan-400">Care</span></span>
                    <span class="block text-[10px] uppercase font-semibold text-cyan-500 tracking-wider">Multi-Clinic SaaS</span>
                </div>
            </div>

            <!-- Active Clinic Profile Banner -->
            @if(auth()->user()->clinic)
                <div class="px-5 py-3.5 bg-slate-800/50 border-b border-slate-800">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Current Clinic</span>
                    <span class="text-xs font-semibold text-white block truncate">{{ auth()->user()->clinic->name }}</span>
                    <span class="text-[11px] text-cyan-400 font-mono block">slug: /{{ auth()->user()->clinic->slug }}</span>
                </div>
            @endif

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Overview
                </a>

                <div class="pt-4 pb-1">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500">Clinic Operations</span>
                </div>

                <a href="{{ route('appointments.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('appointments.*') ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Appointments Calendar
                </a>

                <a href="{{ route('patients.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('patients.*') ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Patients Directory
                </a>

                <a href="{{ route('treatments.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('treatments.*') ? 'bg-cyan-600 text-white shadow-sm shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Clinical Treatments
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800/80 hover:text-white transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Billing & Payments
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800/80 hover:text-white transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Follow-Up Tasks
                </a>
            </nav>

            <!-- User Footer Info -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 truncate">
                        <div class="w-8 h-8 rounded-full bg-cyan-600/30 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="truncate">
                            <span class="text-xs font-semibold text-white block truncate">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-cyan-400 capitalize block">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Sign out"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between shrink-0 shadow-sm shadow-slate-100">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-bold text-slate-900">{{ $headerTitle ?? 'Dashboard' }}</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        System Active
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right text-xs">
                        <span class="font-semibold text-slate-700 block">{{ \Carbon\Carbon::now()->format('l, d F Y') }}</span>
                        <span class="text-slate-400">Asia/Karachi Timezone</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200"></div>

                    <!-- Role Badge -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-200 capitalize">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                        {{ str_replace('_', ' ', auth()->user()->role) }}
                    </span>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-xl bg-teal-50 border border-teal-200 p-4 text-sm text-teal-800 flex items-center gap-3 shadow-sm">
                        <svg class="w-5 h-5 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
