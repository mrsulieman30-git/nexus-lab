<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Analytics & Reporting
                </h2>

                <div class="flex items-center gap-3">
                    <label class="text-xs text-gray-300">View Unit</label>
                    <select v-model="selectedUnit" @change="onUnitChange" class="px-3 py-2 rounded-lg bg-white/5 text-sm text-white">
                        <option value="unit">Unit</option>
                        <option value="kit">Kit</option>
                        <option value="box">Box</option>
                        <option value="bottle">Bottle</option>
                    </select>

                    <a :href="route('reports.export')" target="_blank" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg transition hover:scale-105">
                        <download-icon class="w-4 h-4" /> Export Inventory CSV
                    </a>
                </div>
            </div>
        </template>

        <div class="space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-panel p-6 flex items-center justify-between bg-gradient-to-br from-blue-900/40 to-transparent relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-blue-300 font-bold uppercase tracking-wider">Est. Inventory Value</p>
                        <h3 class="text-3xl font-bold text-white mt-1">${{ stats.valuation.toLocaleString() }}</h3>
                        <p class="text-[10px] text-blue-300/60 mt-1">*Based on Cost per Unit</p>
                    </div>
                    <div class="p-3 bg-blue-500/20 rounded-xl relative z-10">
                        <circle-dollar-sign-icon class="w-8 h-8 text-blue-400" />
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Total Units in Stock</p>
                        <h3 class="text-3xl font-bold text-white mt-1">{{ stats.total_stock }}</h3>
                    </div>
                    <div class="p-3 bg-white/5 rounded-xl relative z-10">
                        <layers-icon class="w-8 h-8 text-gray-400" />
                    </div>
                </div>

                <div class="glass-panel p-6 flex items-center justify-between relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm text-gray-400 font-bold uppercase tracking-wider">Unique Reagents</p>
                        <h3 class="text-3xl font-bold text-white mt-1">{{ stats.total_items }}</h3>
                    </div>
                    <div class="p-3 bg-purple-500/20 rounded-xl relative z-10">
                        <flask-conical-icon class="w-8 h-8 text-purple-400" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <bar-chart-icon class="w-5 h-5 text-blue-400" /> Usage Trends (Last 6 Months)
                    </h3>

                    <div v-if="consumptionData.length > 0" class="h-64 flex items-end justify-between gap-3 px-2">
                        <div v-for="data in consumptionData" :key="data.period ?? data.month" class="w-full flex flex-col items-center group relative">

                            <div class="w-full bg-gradient-to-t from-blue-600/20 to-blue-500/40 hover:from-blue-500 hover:to-blue-400 rounded-t-lg transition-all duration-500 relative group-hover:shadow-[0_0_15px_rgba(59,130,246,0.5)]"
                                 :style="{ height: Math.max(15, (data.total / maxConsumption) * 100) + '%' }">
                            </div>

                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition shadow-xl whitespace-nowrap border border-white/10 z-20">
                                {{ data.total }} Units
                            </div>

                            <p class="text-[10px] text-gray-400 mt-3 font-mono">{{ data.period ?? data.month }}</p>
                        </div>
                    </div>

                    <div v-else class="h-64 flex flex-col items-center justify-center text-gray-500 italic border-2 border-dashed border-white/5 rounded-xl">
                        <bar-chart-icon class="w-10 h-10 mb-2 opacity-20" />
                        <p>Not enough usage data to generate chart.</p>
                    </div>
                </div>

                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <trending-up-icon class="w-5 h-5 text-green-400" /> Most Used Reagents
                    </h3>

                    <div class="space-y-3">
                        <div v-for="(item, index) in topItems" :key="index" class="flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 transition border border-white/5 group">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-lg"
                                     :class="index === 0 ? 'bg-yellow-500 text-black' :
                                             index === 1 ? 'bg-gray-400 text-black' :
                                             index === 2 ? 'bg-orange-700 text-white' : 'bg-gray-800 text-gray-400'">
                                    #{{ index + 1 }}
                                </div>
                                <span class="font-medium text-gray-200 group-hover:text-white transition">{{ item.name }}</span>
                            </div>

                            <div class="text-right">
                                <span class="block font-bold text-white text-lg leading-none">{{ item.display_usage }}</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ item.display_unit }}</span>
                            </div>
                        </div>

                        <div v-if="topItems.length === 0" class="text-center py-10 text-gray-500 italic">
                            No consumption recorded yet.
                        </div>
                    </div>
                </div>

            </div>

            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <pie-chart-icon class="w-5 h-5 text-purple-400" /> Inventory Distribution
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div v-for="cat in categoryData" :key="cat.name" class="p-4 rounded-xl bg-white/5 border border-white/5 hover:border-purple-500/30 transition text-center group">
                        <div class="w-2 h-2 rounded-full mx-auto mb-2 group-hover:scale-150 transition" :style="{ backgroundColor: cat.color_hex }"></div>
                        <p class="text-xl font-bold text-white">{{ cat.total }}</p>
                        <p class="text-xs text-gray-400 uppercase truncate px-1">{{ cat.name }}</p>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import {
    Download as DownloadIcon,
    BarChart as BarChartIcon,
    TrendingUp as TrendingUpIcon,
    CircleDollarSign as CircleDollarSignIcon,
    Layers as LayersIcon,
    FlaskConical as FlaskConicalIcon,
    PieChart as PieChartIcon
} from 'lucide-vue-next';

// UPDATED PROPS to match the new ReportController
const props = defineProps({
    stats: Object,
    consumptionData: Array,
    categoryData: Array,
    topItems: Array,
    unitView: { type: String, default: 'unit' }
});

const selectedUnit = ref(props.unitView || 'unit');

function onUnitChange() {
    // Navigate with a query param to request the chosen unit view.
    // Use the route helper to build the URL (Ziggy is available globally in the project)
    try {
        const url = route('reports.index', { unit_view: selectedUnit.value });
        window.location.href = url;
    } catch (e) {
        // fallback: reload preserving query
        const params = new URLSearchParams(window.location.search);
        params.set('unit_view', selectedUnit.value);
        window.location.search = params.toString();
    }
}

// Helper to scale the charts dynamically
const maxConsumption = computed(() => {
    if (!props.consumptionData || props.consumptionData.length === 0) return 100;
    return Math.max(...props.consumptionData.map(d => parseInt(d.total)));
});
</script>

<script>
export default {
    components: {
        DownloadIcon,
        BarChartIcon,
        TrendingUpIcon,
        CircleDollarSignIcon,
        LayersIcon,
        FlaskConicalIcon,
        PieChartIcon
    }
}
</script>
