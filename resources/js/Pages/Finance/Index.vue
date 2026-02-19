<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Financial Dashboard
                </h2>
                <div class="flex gap-3">
                    <select v-model="selectedPeriod" @change="loadData" class="px-3 py-2 rounded-lg bg-white/5 text-sm text-white">
                        <option value="day">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                    <a :href="route('financial.report', { period: selectedPeriod })" target="_blank" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg transition hover:scale-105">
                        <download-icon class="w-4 h-4" /> Print PDF Report
                    </a>
                </div>
            </div>
        </template>

        <div class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-panel p-6 flex items-center justify-between bg-gradient-to-br from-green-900/40 to-transparent relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-green-300 font-bold uppercase tracking-wider">Total Valuation</p>
                        <h3 class="text-3xl font-bold text-white mt-1">${{ valuation.toLocaleString() }}</h3>
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-blue-300 font-bold uppercase tracking-wider">Incomes</p>
                        <h3 class="text-3xl font-bold text-white mt-1">${{ incomes.toLocaleString() }}</h3>
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-red-300 font-bold uppercase tracking-wider">Outcomes</p>
                        <h3 class="text-3xl font-bold text-white mt-1">${{ outcomes.toLocaleString() }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6">Financial Trends</h3>
                    <div class="h-64 flex items-end justify-between gap-3 px-2">
                        <div v-for="(data, date) in trends" :key="date" class="w-full flex flex-col items-center group relative">
                            <div class="w-full bg-gradient-to-t from-blue-600/20 to-blue-500/40 rounded-t-lg relative group-hover:shadow-[0_0_15px_rgba(59,130,246,0.5)]"
                                 :style="{ height: Math.max(15, (data.in - data.out) / maxTrend * 100) + '%' }">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-3 font-mono">{{ date }}</p>
                        </div>
                    </div>
                </div>

                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6">Top Expenses</h3>
                    <div class="space-y-3">
                        <div v-for="(item, index) in topExpenses" :key="index" class="flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 transition border border-white/5 group">
                            <span class="font-medium text-gray-200 group-hover:text-white transition">{{ item.name }}</span>
                            <span class="font-bold text-white">${{ item.cost.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { Download as DownloadIcon } from 'lucide-vue-next';

const props = defineProps({
    lab: Object,
    valuation: Number,
    incomes: Number,
    outcomes: Number,
    trends: Object,
    topExpenses: Array,
    period: String
});

const selectedPeriod = ref(props.period);

const maxTrend = computed(() => {
    let max = 0;
    for (let data of Object.values(props.trends)) {
        max = Math.max(max, data.in - data.out);
    }
    return max || 100;
});

function loadData() {
    window.location.href = route('financial.index', { period: selectedPeriod.value });
}
</script>
