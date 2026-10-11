<x-layouts.app title="Platform Owner - DentalCare SaaS">
    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-slate-900">
            SaaS Platform Overview
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Platform Super Owner: <strong class="text-slate-700">{{ $user->name }}</strong> &bull; Central Multi-Tenant Telemetry
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Clinics</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-2 block">{{ $totalClinics }}</span>
                <span class="text-xs text-slate-500 mt-1 block">Registered workspaces</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Active Subscriptions</span>
                <span class="text-3xl font-extrabold text-emerald-600 mt-2 block">{{ $activeClinics }}</span>
                <span class="text-xs text-slate-500 mt-1 block">Subscribed tenants</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Estimated MRR</span>
                <span class="text-3xl font-extrabold text-cyan-700 mt-2 block">PKR 70,000</span>
                <span class="text-xs text-slate-500 mt-1 block">Monthly recurring rate</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Clinics Registry Directory</h3>
                <p class="text-xs text-slate-500 mt-0.5">Most recently provisioned tenant clinics</p>
            </div>
            <a href="{{ route('platform.clinics.index') }}" class="text-xs font-bold text-cyan-600 hover:text-cyan-700 transition inline-flex items-center gap-1">
                <span>View Full Registry</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Clinic Name</th>
                        <th class="px-6 py-3.5">Slug URL</th>
                        <th class="px-6 py-3.5">Contact Phone</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentClinics as $c)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 text-xs">{{ $c->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-cyan-600">/clinic/{{ $c->slug }}</td>
                            <td class="px-6 py-4 text-xs">{{ $c->phone ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                    {{ $c->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
