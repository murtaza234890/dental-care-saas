<x-layouts.app title="Issue New Invoice - DentalCare">
    @php
        $initialItems = old('items');
        if (!$initialItems) {
            $initialItems = [
                [
                    'service_id' => $selectedAppointment->service_id ?? '',
                    'description' => $selectedAppointment->service->name ?? '',
                    'unit_price' => $selectedAppointment->service->standard_fee ?? 0,
                    'quantity' => 1,
                ]
            ];
        }
    @endphp

    <div class="max-w-4xl mx-auto" x-data="invoiceForm()">
        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 hover:text-cyan-700 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Invoices & Ledger
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Generate Patient Invoice
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Create an itemized clinical billing invoice with service catalog fees, discounts, and optional counter collection.
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold self-start sm:self-auto">
                    <span>Multi-Tenancy Protected</span>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($selectedAppointment)
            <div class="mb-6 rounded-2xl bg-teal-50 border border-teal-200 p-4 text-xs text-teal-900 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                        ✓
                    </span>
                    <div>
                        <span class="font-bold">Billing for Consultation:</span>
                        <span>{{ $selectedAppointment->patient->name }} &bull; {{ \Carbon\Carbon::parse($selectedAppointment->appointment_date)->format('M d, Y') }}</span>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-teal-100 text-teal-800 font-bold uppercase text-[10px]">
                    Appt #{{ $selectedAppointment->id }}
                </span>
            </div>
        @endif

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                @csrf

                @if($appointmentId)
                    <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
                @endif

                <!-- Section 1: Patient & Due Date -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Invoice Recipient & Due Date
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Patient Selector -->
                        <div>
                            <label for="patient_id" class="block text-xs font-semibold text-slate-700 mb-1">
                                Patient File <span class="text-rose-500">*</span>
                            </label>
                            <select id="patient_id" name="patient_id" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                                <option value="">-- Choose Patient --</option>
                                @foreach($patients as $pt)
                                    <option value="{{ $pt->id }}" {{ (old('patient_id', $patientId) == $pt->id) ? 'selected' : '' }}>
                                        {{ $pt->name }} ({{ $pt->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-xs font-semibold text-slate-700 mb-1">
                                Due Date <span class="text-slate-400 font-normal">(Default today)</span>
                            </label>
                            <input type="date" id="due_date" name="due_date"
                                value="{{ old('due_date', date('Y-m-d')) }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-600/20">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Itemized Line Items Repeater -->
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-cyan-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Invoice Line Items & Dental Procedures
                        </h3>

                        <button type="button" @click="addItem()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 text-xs font-bold hover:bg-cyan-100 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            + Add Line Item
                        </button>
                    </div>

                    <!-- Line Items Table / Repeater -->
                    <div class="space-y-3">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <!-- Service Select (optional prefill) -->
                                <div class="w-full sm:w-1/3">
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Catalog Service</label>
                                    <select :name="'items[' + idx + '][service_id]'" x-model="item.service_id" @change="onServiceChange(idx)"
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none">
                                        <option value="">-- Custom Procedure --</option>
                                        <template x-for="srv in servicesCatalog" :key="srv.id">
                                            <option :value="srv.id" x-text="srv.name + ' (Rs. ' + Number(srv.standard_fee).toLocaleString() + ')'"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="w-full sm:flex-1">
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Description *</label>
                                    <input type="text" :name="'items[' + idx + '][description]'" x-model="item.description" required
                                        placeholder="Procedure / Treatment description..."
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-cyan-600 focus:outline-none">
                                </div>

                                <!-- Unit Price -->
                                <div class="w-full sm:w-28">
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Price (Rs.) *</label>
                                    <input type="number" step="0.01" min="0" :name="'items[' + idx + '][unit_price]'" x-model.number="item.unit_price" required
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                                </div>

                                <!-- Quantity -->
                                <div class="w-full sm:w-20">
                                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Qty *</label>
                                    <input type="number" min="1" :name="'items[' + idx + '][quantity]'" x-model.number="item.quantity" required
                                        class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-cyan-600 focus:outline-none">
                                </div>

                                <!-- Line Total -->
                                <div class="w-full sm:w-28 text-right sm:self-center pt-2 sm:pt-4">
                                    <span class="block text-[10px] font-bold uppercase text-slate-400 sm:hidden">Total:</span>
                                    <span class="font-mono font-bold text-slate-900 text-xs" x-text="'Rs. ' + ((parseFloat(item.unit_price) || 0) * (parseInt(item.quantity) || 1)).toLocaleString()"></span>
                                </div>

                                <!-- Remove Row Button -->
                                <div class="self-end sm:self-center pt-2 sm:pt-4">
                                    <button type="button" @click="removeItem(idx)" :disabled="items.length <= 1"
                                        :class="items.length <= 1 ? 'opacity-30 cursor-not-allowed text-slate-400' : 'text-rose-500 hover:text-rose-700'"
                                        class="p-1 rounded-lg hover:bg-slate-200 transition" title="Remove Row">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Financial Ledger Summary Calculation -->
                    <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col items-end space-y-2 text-xs">
                        <div class="flex items-center justify-between w-64">
                            <span class="text-slate-500 font-medium">Subtotal:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="'Rs. ' + subtotal().toLocaleString()"></span>
                        </div>

                        <div class="flex items-center justify-between w-64">
                            <label for="discount_amount" class="text-slate-500 font-medium">Discount (Rs.):</label>
                            <input type="number" step="0.01" min="0" id="discount_amount" name="discount_amount" x-model.number="discount" @input="syncUpfront()"
                                class="w-28 rounded-lg border border-slate-300 px-2 py-1 text-xs text-right font-mono focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div class="pt-2 border-t border-slate-200 flex items-center justify-between w-64 text-sm font-extrabold text-cyan-900">
                            <span>Grand Total:</span>
                            <span class="font-mono text-base text-cyan-800" x-text="'Rs. ' + grandTotal().toLocaleString()"></span>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Upfront Counter Payment Collection -->
                <div class="pt-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Upfront Payment Collection
                    </h3>

                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="initial_payment" value="1" x-model="collectUpfront" @change="syncUpfront()"
                                class="rounded text-emerald-600 focus:ring-emerald-500">
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">Collect Counter Payment Right Now</span>
                                <span class="text-slate-500">Instantly issues receipt and credits patient account at the front desk.</span>
                            </div>
                        </label>

                        <div x-show="collectUpfront" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div>
                                <label for="initial_payment_amount" class="block text-[11px] font-semibold text-slate-700 mb-1">
                                    Collected Amount (Rs.) *
                                </label>
                                <input type="number" step="0.01" min="1" id="initial_payment_amount" name="initial_payment_amount"
                                    x-model.number="upfrontAmount"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-emerald-600 focus:outline-none">
                            </div>

                            <div>
                                <label for="initial_payment_method" class="block text-[11px] font-semibold text-slate-700 mb-1">
                                    Payment Method *
                                </label>
                                <select id="initial_payment_method" name="initial_payment_method"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 focus:border-emerald-600 focus:outline-none">
                                    <option value="cash">Counter Cash</option>
                                    <option value="card">POS Debit / Credit Card</option>
                                    <option value="bank_transfer">Online Bank Transfer (Raast / IBFT)</option>
                                    <option value="easypaisa_jazzcash">EasyPaisa / JazzCash</option>
                                </select>
                            </div>

                            <div>
                                <label for="initial_payment_reference" class="block text-[11px] font-semibold text-slate-700 mb-1">
                                    Reference / Txn ID <span class="text-slate-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" id="initial_payment_reference" name="initial_payment_reference"
                                    placeholder="e.g. Card slip # or Raast Ref"
                                    class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs text-slate-900 font-mono focus:border-emerald-600 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('invoices.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Cancel
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-6 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-cyan-600/25 hover:from-cyan-700 hover:to-teal-700 transition active:scale-[0.99]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Issue Clinic Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                servicesCatalog: @json($services),
                items: @json(array_values($initialItems)),
                discount: {{ (float) (old('discount_amount', 0) ?: 0) }},
                collectUpfront: {{ old('initial_payment') ? 'true' : 'false' }},
                upfrontAmount: {{ (float) (old('initial_payment_amount', 0) ?: 0) }},

                onServiceChange(index) {
                    let sId = this.items[index].service_id;
                    let found = this.servicesCatalog.find(function(s) { return s.id == sId; });
                    if (found) {
                        this.items[index].description = found.name;
                        this.items[index].unit_price = parseFloat(found.standard_fee);
                    }
                },

                addItem() {
                    this.items.push({
                        service_id: '',
                        description: '',
                        unit_price: 0,
                        quantity: 1
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                subtotal() {
                    return this.items.reduce(function(sum, item) {
                        let price = parseFloat(item.unit_price) || 0;
                        let qty = parseInt(item.quantity) || 0;
                        return sum + (price * qty);
                    }, 0);
                },

                grandTotal() {
                    let disc = parseFloat(this.discount) || 0;
                    return Math.max(0, this.subtotal() - disc);
                },

                syncUpfront() {
                    if (this.collectUpfront) {
                        this.upfrontAmount = this.grandTotal();
                    }
                }
            };
        }
    </script>
</x-layouts.app>
