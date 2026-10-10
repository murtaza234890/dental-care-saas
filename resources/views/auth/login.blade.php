<x-layouts.auth title="Login - DentalCare SaaS">
    <form class="space-y-5" action="{{ route('login') }}" method="POST">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
            <div class="mt-1 relative">
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}"
                    placeholder="doctor@clinic.com"
                    class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-sm transition">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                <span class="text-xs text-slate-500">Default demo: password</span>
            </div>
            <div class="mt-1 relative">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    placeholder="••••••••"
                    class="block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20 text-sm transition">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                <label for="remember" class="ml-2 block text-xs text-slate-600">Remember session</label>
            </div>
        </div>

        <div>
            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 py-3 px-4 text-sm font-semibold text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 transition active:scale-[0.99]">
                Sign In to Dashboard
            </button>
        </div>
    </form>

    <!-- Quick Demo Accounts Switcher -->
    <div class="mt-6 pt-6 border-t border-slate-100">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 text-center">
            ⚡ Quick Demo Login (Click to Fill)
        </p>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <button type="button" onclick="fillDemo('dr.ahmed@citydental.test')"
                class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-cyan-50 hover:border-cyan-300 text-left transition group">
                <span class="font-bold text-slate-800 block group-hover:text-cyan-700">Dr. Ahmed Khan</span>
                <span class="text-slate-500 text-[11px]">Clinic Admin / Owner</span>
            </button>

            <button type="button" onclick="fillDemo('dr.sara@citydental.test')"
                class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-cyan-50 hover:border-cyan-300 text-left transition group">
                <span class="font-bold text-slate-800 block group-hover:text-cyan-700">Dr. Sara Ali</span>
                <span class="text-slate-500 text-[11px]">Associate Dentist</span>
            </button>

            <button type="button" onclick="fillDemo('receptionist@citydental.test')"
                class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-cyan-50 hover:border-cyan-300 text-left transition group">
                <span class="font-bold text-slate-800 block group-hover:text-cyan-700">Ayesha Noor</span>
                <span class="text-slate-500 text-[11px]">Front Desk Receptionist</span>
            </button>

            <button type="button" onclick="fillDemo('admin@dentalcare.test')"
                class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-cyan-50 hover:border-cyan-300 text-left transition group">
                <span class="font-bold text-slate-800 block group-hover:text-cyan-700">Platform Admin</span>
                <span class="text-slate-500 text-[11px]">SaaS Super Owner</span>
            </button>
        </div>
    </div>

    <div class="mt-6 text-center text-xs text-slate-600">
        New clinic owner?
        <a href="{{ route('register.clinic') }}" class="font-semibold text-cyan-600 hover:text-cyan-700 hover:underline">
            Register Your Clinic Here
        </a>
    </div>

    <script>
        function fillDemo(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</x-layouts.auth>
