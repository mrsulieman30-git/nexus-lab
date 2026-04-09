<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Financial Intelligence</h2>
        </template>

        <div class="max-w-7xl mx-auto space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="glass-panel p-6">
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Inflows</p>
                    <p class="text-3xl font-bold text-white mt-1">${{ Number(totalInflows).toLocaleString() }}</p>
                </div>
                <div class="glass-panel p-6">
                    <p class="text-xs font-bold text-gray-500 uppercase">Total Outflows</p>
                    <p class="text-3xl font-bold text-white mt-1">${{ Number(totalOutflows).toLocaleString() }}</p>
                </div>
                <div class="glass-panel p-6">
                    <p class="text-xs font-bold text-gray-500 uppercase">Net (Inflows - Outflows)</p>
                    <p class="text-3xl font-bold text-white mt-1">${{ (Number(totalInflows) - Number(totalOutflows)).toLocaleString() }}</p>
                </div>
                <div class="glass-panel p-6 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Period</p>
                        <p class="text-xl font-bold text-white">{{ period }}</p>
                    </div>
                    <div class="text-right">
                        <select v-model="localPeriod" @change="updatePeriod" class="glass-input w-36 text-sm">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-white mb-4">Supplier Scorecard</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                            <tr>
                                <th class="pb-3 pl-2">Supplier</th>
                                <th class="pb-3 text-center">Orders</th>
                                <th class="pb-3">Last Purchase</th>
                                <th class="pb-3 text-right pr-2">Total Spent (Lifetime)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <tr v-for="sup in supplierStats" :key="sup.name" class="hover:bg-white/5 transition">
                                <td class="py-4 pl-2 font-bold text-white">{{ sup.name }}</td>
                                <td class="py-4 text-center text-gray-400">{{ sup.orders_count }}</td>
                                <td class="py-4 text-gray-400">{{ new Date(sup.last_order).toLocaleDateString() }}</td>
                                <td class="py-4 text-right pr-2 font-mono text-green-400 font-bold">${{ Number(sup.total_spent).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-white mb-4">Inflows / Outflows Series</h3>
                <div class="text-sm text-gray-400 mb-3">Showing aggregated totals per <strong>{{ period }}</strong> between <strong>{{ start }}</strong> and <strong>{{ end }}</strong></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                            <tr>
                                <th class="pb-3 pl-2">Period</th>
                                <th class="pb-3 text-right pr-2">Inflows</th>
                                <th class="pb-3 text-right pr-2">Outflows</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <tr v-for="(label, idx) in seriesLabels" :key="label" class="hover:bg-white/5 transition">
                                <td class="py-3 pl-2 text-white">{{ label }}</td>
                                <td class="py-3 text-right font-mono text-green-400 pr-2">${{ Number(inflows[label] || 0).toLocaleString() }}</td>
                                <td class="py-3 text-right font-mono text-red-400 pr-2">${{ Number(outflows[label] || 0).toLocaleString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-white mb-4">Recent Price History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                            <tr>
                                <th class="pb-3 pl-2">Date</th>
                                <th class="pb-3">Item Name</th>
                                <th class="pb-3">Supplier</th>
                                <th class="pb-3 text-right">Unit Price Paid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <tr v-for="(row, i) in purchaseHistory" :key="i" class="hover:bg-white/5 transition">
                                <td class="py-3 pl-2 text-gray-400">{{ new Date(row.order_date).toLocaleDateString() }}</td>
                                <td class="py-3 font-medium text-white">{{ row.item }}</td>
                                <td class="py-3 text-gray-400">{{ row.supplier }}</td>
                                <td class="py-3 text-right font-mono text-white">${{ Number(row.unit_cost).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    inflows: Object,
    outflows: Object,
    supplierStats: Array,
    purchaseHistory: Array,
    totalInflows: [Number, String],
    totalOutflows: [Number, String],
    currentYear: [String, Number],
    period: String,
    start: String,
    end: String
});

const localPeriod = ref(props.period || 'monthly');

const seriesLabels = computed(() => {
    // Merge keys from inflows and outflows and sort
    const keys = new Set([...Object.keys(props.inflows || {}), ...Object.keys(props.outflows || {})]);
    return Array.from(keys).sort();
});

const updatePeriod = () => {
    router.get(route('reports.financial'), { period: localPeriod.value, start: props.start, end: props.end }, { preserveState: true });
};
</script>
