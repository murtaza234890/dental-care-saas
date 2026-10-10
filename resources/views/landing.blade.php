<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentalCare SaaS — The Modern Cloud Operating System for Dental Clinics</title>
    <meta name="description" content="Streamline patient dossiers, interactive FDI odontogram charting, online slot bookings, and financial invoicing with DentalCare Multi-Tenant SaaS.">
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

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-cyan-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-black tracking-tight text-slate-900">Dental<span class="text-cyan-600">Care</span></span>
                    <span class="block text-[10px] font-bold tracking-widest uppercase text-slate-400">SaaS Platform</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#features" class="hover:text-cyan-600 transition">Features</a>
                <a href="#odontogram" class="hover:text-cyan-600 transition">FDI Odontogram</a>
                <a href="#pricing" class="hover:text-cyan-600 transition">Pricing Tiers</a>
                <a href="#security" class="hover:text-cyan-600 transition">Multi-Tenancy</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 shadow-sm transition">
                        Open Dashboard
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 hover:text-cyan-600 hover:bg-slate-100 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register.clinic') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-white text-xs font-bold hover:from-cyan-700 hover:to-teal-700 shadow-md shadow-cyan-600/20 transition active:scale-95">
                        Start 14-Day Free Trial
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-24 lg:pt-28 lg:pb-32 bg-gradient-to-b from-cyan-50/40 via-white to-slate-50">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-cyan-300/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-24 w-96 h-96 bg-teal-300/20 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan-100/70 border border-cyan-200 text-cyan-800 text-xs font-bold mb-6">
                <span class="w-2 h-2 rounded-full bg-cyan-600 animate-pulse"></span>
                Next-Gen Multi-Tenant Dental Practice Management
            </div>

            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-slate-900 max-w-4xl mx-auto leading-tight">
                Run Your Dental Clinic with <span class="bg-gradient-to-r from-cyan-600 to-teal-600 bg-clip-text text-transparent">Clinical Precision</span>
            </h1>

            <p class="mt-6 text-base sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Say goodbye to fragmented paper files and spreadsheets. Seamlessly manage patient appointments, interactive FDI dental odontograms, treatment plans, counter billing, and 1-click WhatsApp follow-ups.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register.clinic') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-600 to-teal-600 text-white font-bold text-sm hover:from-cyan-700 hover:to-teal-700 shadow-xl shadow-cyan-600/30 transition transform hover:-translate-y-0.5">
                    🚀 Onboard Your Clinic (14-Day Free Trial)
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-sm hover:bg-slate-50 hover:border-slate-300 shadow-sm transition">
                    Sign In to Existing Workspace &rarr;
                </a>
            </div>

            <!-- Trust Features Row -->
            <div class="mt-12 flex flex-wrap items-center justify-center gap-8 text-xs font-semibold text-slate-500">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Zero Credit Card Required
                </span>
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Instant Clinic Web Portal (<code class="text-cyan-700 font-mono">/clinic/your-name</code>)
                </span>
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Strict Multi-Tenant Database Isolation
                </span>
            </div>
        </div>
    </section>

    <!-- Platform Modules Grid -->
    <section id="features" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-xs font-bold uppercase tracking-widest text-cyan-600">Enterprise Healthcare Architecture</h2>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">Everything Your Dental Clinic Needs to Scale</p>
                <p class="text-sm text-slate-500 mt-3">From front-desk patient intake to specialized dental charting and revenue audits.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: FDI Odontogram -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Interactive FDI Odontogram</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Full 32-tooth adult and 20-tooth pediatric dental charts. Tag tooth surfaces (Mesial, Distal, Occlusal) with condition color-coding and automated treatment plan sync.
                    </p>
                </div>

                <!-- Feature 2: Conflict-Free Appointments -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Smart Scheduling & Conflicts Engine</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Database-level locking prevents duplicate or overlapping slots. Daily and weekly timetable views with doctor filtering and 7-stage patient status flow.
                    </p>
                </div>

                <!-- Feature 3: Dynamic Invoicing -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Billing, Invoicing & Counter Receipts</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Dynamic line-item billing, upfront counter payments, real-time balance calculations, and professional printable receipts with clinic branding.
                    </p>
                </div>

                <!-- Feature 4: Patient Dossier -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Confidential Patient Dossier</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Unified patient timeline with medical alert flags (allergies, diabetes, hypertension). Confidential clinical notes masked automatically for non-doctor roles.
                    </p>
                </div>

                <!-- Feature 5: 1-Click WhatsApp -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Daily Recall & 1-Click WhatsApp</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Automated recall queue for root canal reviews, hygiene cleanings, and crown fittings. One-click instant WhatsApp message dispatch directly to patients.
                    </p>
                </div>

                <!-- Feature 6: Public Website Portal -->
                <div class="p-8 rounded-3xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-cyan-300 hover:shadow-xl hover:shadow-cyan-500/5 transition">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mt-5">Branded Public Booking Website</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Every clinic gets an instant public landing page at <code class="text-slate-800 font-mono">/clinic/{slug}</code> with your doctor profiles, procedure pricing, and self-service slot booking.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Tiers Section -->
    <section id="pricing" class="py-24 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-xs font-bold uppercase tracking-widest text-cyan-600">Predictable SaaS Pricing</h2>
                <p class="text-3xl font-extrabold text-slate-900 mt-2">Transparent Subscription Plans</p>
                <p class="text-sm text-slate-500 mt-3">Start with our 14-day full-feature trial. Upgrade or cancel anytime.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter Tier -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Starter Clinic</h3>
                        <p class="text-xs text-slate-500 mt-1">Ideal for solo dental practitioners.</p>

                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900">3,500</span>
                            <span class="text-sm font-semibold text-slate-500">PKR / month</span>
                        </div>

                        <ul class="mt-8 space-y-3 text-xs text-slate-600">
                            <li class="flex items-center gap-2 font-medium text-slate-800">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                1 Dentist / Doctor Slot
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                2 Staff Accounts
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                200 Patient Records
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Online Booking & Invoices
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('register.clinic') }}" class="block text-center py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            Start Free Trial
                        </a>
                    </div>
                </div>

                <!-- Professional Tier (Featured) -->
                <div class="bg-gradient-to-b from-slate-900 to-cyan-950 text-white rounded-3xl p-8 shadow-2xl relative flex flex-col justify-between ring-2 ring-cyan-500">
                    <div class="absolute -top-3 right-6 px-3 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-teal-400 text-slate-900 font-extrabold text-[10px] uppercase tracking-wider">
                        Most Popular
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-white">Professional Practice</h3>
                        <p class="text-xs text-slate-400 mt-1">For growing dental clinics with associates.</p>

                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-white">8,500</span>
                            <span class="text-sm font-semibold text-slate-400">PKR / month</span>
                        </div>

                        <ul class="mt-8 space-y-3 text-xs text-slate-300">
                            <li class="flex items-center gap-2 font-medium text-white">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Up to 3 Dentists / Doctors
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                6 Staff & Receptionist Seats
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                1,500 Patient Dossiers
                            </li>
                            <li class="flex items-center gap-2 text-cyan-300 font-semibold">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Interactive FDI Odontogram Included
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                1-Click WhatsApp Recall Queue
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Financial Analytics & Printable Audits
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('register.clinic') }}" class="block text-center py-3.5 rounded-xl bg-gradient-to-r from-cyan-500 to-teal-500 hover:from-cyan-400 hover:to-teal-400 text-slate-900 font-extrabold text-xs shadow-lg shadow-cyan-500/25 transition">
                            Start 14-Day Free Trial
                        </a>
                    </div>
                </div>

                <!-- Enterprise Tier -->
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Enterprise Hospital</h3>
                        <p class="text-xs text-slate-500 mt-1">Multi-chair surgical dental hospitals.</p>

                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900">18,000</span>
                            <span class="text-sm font-semibold text-slate-500">PKR / month</span>
                        </div>

                        <ul class="mt-8 space-y-3 text-xs text-slate-600">
                            <li class="flex items-center gap-2 font-medium text-slate-800">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                10 Doctors / Dental Chairs
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                25 Staff Accounts
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                10,000 Patient Records
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Dedicated Account Manager & SLA
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('register.clinic') }}" class="block text-center py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            Start Enterprise Trial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-20 bg-gradient-to-r from-cyan-600 to-teal-700 text-white text-center relative overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight">Set Up Your Clinic in 60 Seconds</h2>
            <p class="mt-4 text-sm sm:text-base text-cyan-100 max-w-xl mx-auto">
                No credit card, no complex server setup. Simply enter your clinic name and start booking patients immediately.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register.clinic') }}" class="px-8 py-4 rounded-2xl bg-white text-slate-900 font-extrabold text-sm hover:bg-slate-100 shadow-xl transition transform hover:-translate-y-0.5">
                    Register Your Clinic Workspace Now &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-cyan-600 to-teal-500 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-white">Dental<span class="text-cyan-400">Care</span> SaaS</span>
                    <span class="block text-[10px] text-slate-500">© {{ date('Y') }} All Rights Reserved.</span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="hover:text-white transition">Sign In</a>
                <a href="{{ route('register.clinic') }}" class="hover:text-white transition">Register Clinic</a>
                <a href="#features" class="hover:text-white transition">Features</a>
                <a href="#pricing" class="hover:text-white transition">Pricing</a>
            </div>
        </div>
    </footer>

</body>
</html>
