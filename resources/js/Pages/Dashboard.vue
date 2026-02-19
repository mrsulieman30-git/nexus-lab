<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Lab Command Center
            </h2>
        </template>

        <div class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-400 text-sm font-bold uppercase tracking-wider">Total Reagents</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ stats.total_items }}</p>
                    </div>
                    <div class="p-3 bg-blue-500/20 rounded-xl text-blue-400 relative z-10">
                        <flask-conical-icon class="w-8 h-8" />
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-400 text-sm font-bold uppercase tracking-wider">Active Vendors</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ stats.total_suppliers }}</p>
                    </div>
                    <div class="p-3 bg-purple-500/20 rounded-xl text-purple-400 relative z-10">
                        <truck-icon class="w-8 h-8" />
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden border border-red-500/30 bg-red-500/5">
                    <div class="relative z-10">
                        <p class="text-red-300 text-sm font-bold uppercase tracking-wider">Critical Alerts</p>
                        <p class="text-3xl font-bold text-red-400 mt-1">{{ stats.low_stock_count + expiringBatches.length }}</p>
                    </div>
                    <div class="p-3 bg-red-500/20 rounded-xl text-red-400 relative z-10 animate-pulse">
                        <alert-triangle-icon class="w-8 h-8" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="glass-panel p-6 h-full border-l-4 border-red-500">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <arrow-down-icon class="w-5 h-5 text-red-400" /> Low Stock Warning
                    </h3>
                    <div v-if="lowStockItems.length > 0" class="space-y-3">
                        <div v-for="item in lowStockItems" :key="item.id" class="flex items-center justify-between p-3 bg-red-500/10 border border-red-500/20 rounded-xl hover:bg-red-500/20 transition cursor-pointer">
                            <div>
                                <Link :href="route('items.show', item.id)" class="font-bold text-white hover:underline">{{ item.name }}</Link>
                                <p class="text-xs text-red-300">Min Threshold: {{ item.min_threshold }} {{ item.unit }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-red-400">{{ item.quantity }}</span>
                                <span class="text-xs text-red-300 block">{{ item.unit }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500 italic flex flex-col items-center">
                        <check-circle-icon class="w-10 h-10 text-green-500/50 mb-2" />
                        <p>Stock levels are healthy.</p>
                    </div>
                </div>

                <div class="glass-panel p-6 h-full border-l-4 border-yellow-500">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <alert-triangle-icon class="w-5 h-5 text-yellow-400" />
                        Priority Usage (Expiring Soon)
                    </h3>

                    <div v-if="expiringBatches.length > 0" class="space-y-3">
                        <div v-for="batch in expiringBatches" :key="batch.id" class="relative p-4 bg-gradient-to-r from-yellow-500/10 to-transparent border border-yellow-500/20 rounded-xl">

                            <div class="absolute -top-2 -right-2 bg-yellow-500 text-black text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg animate-pulse">
                                USE FIRST
                            </div>

                            <div class="flex justify-between items-start">
                                <div>
                                    <Link :href="route('items.show', batch.item.id)" class="font-bold text-white hover:underline text-lg">
                                        {{ batch.item.name }}
                                    </Link>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 bg-white/10 rounded text-xs font-mono text-gray-300 border border-white/10">
                                            Lot: {{ batch.lot_number || 'Unknown' }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            Loc: {{ batch.storage_location || 'General' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-xl font-bold text-yellow-400">{{ getDaysLeft(batch.expiry_date) }} <span class="text-xs font-normal text-yellow-200">days left</span></div>
                                    <div class="text-xs text-gray-400 mt-1">Expires: {{ batch.expiry_date }}</div>
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-t border-white/5 flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase font-bold">Stock Remaining</span>
                                <span class="font-bold text-white">{{ batch.quantity }} {{ batch.item.unit }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-500 italic flex flex-col items-center">
                        <check-circle-icon class="w-10 h-10 text-green-500/50 mb-2" />
                        <p>No immediate expiry risks.</p>
                    </div>
                </div>

            </div>

            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <history-icon class="w-5 h-5 text-blue-400" /> Recent Lab Activity
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                            <tr>
                                <th class="pb-3 pl-2">User</th>
                                <th class="pb-3">Action</th>
                                <th class="pb-3">Item</th>
                                <th class="pb-3">Qty</th>
                                <th class="pb-3 text-right pr-2">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 text-sm">
                            <tr v-for="log in recentActivity" :key="log.id" class="hover:bg-white/5">
                                <td class="py-3 pl-2 font-medium text-white">{{ log.user.name }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase"
                                          :class="log.type === 'in' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'">
                                        {{ log.type === 'in' ? 'Restock' : 'Usage' }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-300">{{ log.item.name }}</td>
                                <td class="py-3 font-mono font-bold" :class="log.type === 'in' ? 'text-green-400' : 'text-red-400'">
                                    {{ log.type === 'in' ? '+' : '-' }}{{ log.quantity }}
                                </td>
                                <td class="py-3 text-right pr-2 text-gray-500 text-xs">
                                    {{ new Date(log.created_at).toLocaleString() }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="recentActivity.length === 0" class="text-center py-4 text-gray-500 italic">No recent activity.</div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    FlaskConical as FlaskConicalIcon,
    Truck as TruckIcon,
    AlertTriangle as AlertTriangleIcon,
    ArrowDown as ArrowDownIcon,
    Clock as ClockIcon,
    CheckCircle as CheckCircleIcon,
    History as HistoryIcon
} from 'lucide-vue-next';

// UPDATED PROPS DEFINITION
const props = defineProps({
    lowStockItems: Array,
    expiringBatches: Array, // Corrected from expiringItems
    recentActivity: Array,
    stats: Object
});

const getDaysLeft = (dateStr) => {
    const today = new Date();
    const expiry = new Date(dateStr);
    return Math.ceil((expiry - today) / (1000 * 60 * 60 * 24));
};
</script>
