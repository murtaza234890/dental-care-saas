<x-layouts.auth title="Register Clinic - DentalCare SaaS" heading="Register Your Dental Clinic" subheading="Start your 30-day trial for Dental Clinic Management">
    <form class="space-y-4" action="{{ route('register.clinic') }}" method="POST">
        @csrf

        <div class="border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-2">1. Clinic Information</h3>
            <div class="space-y-3">
                <div>
                    <label for="clinic_name" class="block text-xs font-semibold text-slate-700">Clinic Name *</label>
                    <input id="clinic_name" name="clinic_name" type="text" required value="{{ old('clinic_name') }}"
                        placeholder="e.g. Shifa Dental & Aesthetic Clinic"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label for="clinic_phone" class="block text-xs font-semibold text-slate-700">Clinic Phone / WhatsApp *</label>
                        <input id="clinic_phone" name="clinic_phone" type="text" required value="{{ old('clinic_phone') }}"
                            placeholder="0300-1234567"
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                    </div>
                    <div>
                        <label for="address" class="block text-xs font-semibold text-slate-700">City / Location</label>
                        <input id="address" name="address" type="text" value="{{ old('address') }}"
                            placeholder="e.g. F-7 Markaz, Islamabad"
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-1">
            <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-800 mb-2">2. Clinic Owner / Admin Account</h3>
            <div class="space-y-3">
                <div>
                    <label for="owner_name" class="block text-xs font-semibold text-slate-700">Doctor / Owner Full Name *</label>
                    <input id="owner_name" name="owner_name" type="text" required value="{{ old('owner_name') }}"
                        placeholder="Dr. Muhammad Ali"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700">Admin Login Email *</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}"
                        placeholder="dr.ali@shifadental.pk"
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700">Password *</label>
                        <input id="password" name="password" type="password" required placeholder="Minimum 6 characters"
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Confirm Password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Repeat password"
                            class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 py-3 px-4 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition active:scale-[0.99]">
                Create Clinic & Launch Dashboard
            </button>
        </div>
    </form>

    <div class="mt-4 text-center text-xs text-slate-600">
        Already have a clinic account?
        <a href="{{ route('login') }}" class="font-semibold text-cyan-600 hover:text-cyan-700 hover:underline">
            Sign in here
        </a>
    </div>
</x-layouts.auth>
