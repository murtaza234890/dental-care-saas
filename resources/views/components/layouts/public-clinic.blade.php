<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? $clinic->name }} - Dental Care & Implant Clinic</title>
    <!-- Google Typography -->
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
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-cyan-500 selection:text-white">
    <!-- Top Contact Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-8 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-4">
                @if($clinic->phone)
                    <a href="tel:{{ $clinic->phone }}" class="flex items-center gap-1.5 hover:text-cyan-400 transition">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $clinic->phone }}</span>
                    </a>
                @endif
                <span class="text-slate-600 hidden sm:inline">&bull;</span>
                <span class="text-slate-400 hidden sm:inline">{{ $clinic->address ?? 'F-7 Markaz, Islamabad' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    Appointments Available Today
                </span>
                <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition ml-3 pl-3 border-l border-slate-700">Staff Portal</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('clinic.show', $clinic->slug) }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-cyan-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight block">{{ $clinic->name }}</span>
                    <span class="text-[11px] font-semibold text-cyan-600 uppercase tracking-wider block">Dental Care & Implantology</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#services" class="hover:text-cyan-600 transition">Services & Treatments</a>
                <a href="#doctors" class="hover:text-cyan-600 transition">Our Specialists</a>
                <a href="#timings" class="hover:text-cyan-600 transition">Clinic Hours</a>
                <a href="#booking-section"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition">
                    Book Appointment
                </a>
            </nav>
        </div>
    </header>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Clinic Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-12 border-b border-slate-800">
                <div>
                    <span class="text-lg font-bold text-white block mb-2">{{ $clinic->name }}</span>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Delivering sterile, compassionate and comprehensive dental care with state-of-the-art diagnostic instruments and certified specialists.
                    </p>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $clinic->address ?? 'Islamabad' }}
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-white block mb-4">Patient Direct Access</span>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#services" class="hover:text-white transition">Dental Treatments & Fee Schedule</a></li>
                        <li><a href="#doctors" class="hover:text-white transition">Meet Doctors & Specialists</a></li>
                        <li><a href="#booking-section" class="hover:text-white transition">Online Appointment Booking</a></li>
                    </ul>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-white block mb-4">Helpline & Hours</span>
                    <p class="text-xs text-slate-300 mb-2">Phone / WhatsApp: <strong>{{ $clinic->phone ?? '0300-1234567' }}</strong></p>
                    <p class="text-xs text-slate-400">Monday &ndash; Saturday: 10:00 AM &ndash; 08:00 PM</p>
                    <p class="text-xs text-slate-500 mt-1">Sunday: Emergency On-Call</p>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <span>&copy; {{ date('Y') }} {{ $clinic->name }}. All rights reserved.</span>
                <span class="flex items-center gap-1">
                    Powered by <strong class="text-slate-300">DentalCare SaaS</strong> Platform
                </span>
            </div>
        </div>
    </footer>
</body>
</html>
