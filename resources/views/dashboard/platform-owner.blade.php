<x-layouts.app title="Platform Owner - DentalCare SaaS">
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
            SaaS Platform Overview 👑
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Platform Owner: <strong class="text-slate-700">{{ $user->name }}</strong> &bull; Central Tenant Control
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Registered Clinics</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ $totalClinics }}</span>
                <span class="text-xs text-slate-500">tenants</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Subscribed Clinics</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600">{{ $activeClinics }}</span>
                <span class="text-xs text-slate-500">active</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Monthly Platform Revenue</span>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-cyan-600">PKR 70,000</span>
                <span class="text-xs text-slate-500">est. recurring</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Registered Clinics Directory</h3>
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
                        <tr>
                            <td class="px-6 py-4 font-bold text-slate-900 text-xs">{{ $c->name }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-cyan-600">/clinic/{{ $c->slug }}</td>
                            <td class="px-6 py-4 text-xs">{{ $c->phone ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
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
