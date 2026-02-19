<template>
    <AppLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link :href="route('items.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                        <arrow-left-icon class="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 class="text-xl font-bold leading-tight text-white">{{ item.name }}</h2>
                        <p class="text-xs text-blue-400 font-mono mt-0.5">SKU: {{ item.sku || 'N/A' }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a :href="route('items.label', item.id)" target="_blank" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-sm font-bold shadow-lg transition flex items-center gap-2">
                        <qr-code-icon class="w-4 h-4" /> Label
                    </a>

                    <Link :href="route('items.edit', item.id)" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg transition flex items-center gap-2">
                        <pencil-icon class="w-4 h-4" /> Edit
                    </Link>

                    <button @click="deleteItem" class="px-4 py-2 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white rounded-lg text-sm font-bold border border-red-600/30 transition flex items-center gap-2">
                        <trash-icon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </template>

        <div v-if="$page.props.flash?.message" class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2 animate-fade-in-down">
            <check-circle-icon class="w-5 h-5" />
            {{ $page.props.flash.message }}
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-6">

                <div class="glass-panel p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <package-icon class="w-32 h-32" />
                    </div>

                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Current Stock</p>
                    <div class="flex items-end gap-2 mb-4">
                                        <div>
                                            <span class="text-4xl font-bold text-white">{{ item.quantity }}</span>
                                            <span class="text-sm font-medium text-gray-400 mb-1.5">{{ item.unit }}</span>
                                        </div>
                                        <div class="ml-4 text-sm text-gray-300">
                                            <div v-if="item.packaging_type === 'box' && item.kits_per_box">
                                                <div class="font-medium">{{ item.boxes_remaining }} boxes</div>
                                                <div class="text-xs">+ {{ item.kits_remaining }} kits (leftover)</div>
                                                <div class="text-xs mt-1">Total kits: {{ item.total_kits }}</div>
                                            </div>
                                            <div v-else-if="item.packaging_type === 'kit'">
                                                <div class="font-medium">{{ item.total_kits }} kits</div>
                                            </div>
                                            <div v-else-if="(item.packaging_type === 'bottle' || item.packaging_type === 'reagent') && item.tests_per_bottle">
                                                <div class="font-medium">{{ item.quantity }} bottles</div>
                                                <div class="text-xs">Total tests: {{ item.total_tests }}</div>
                                            </div>
                                        </div>
                    </div>

                    <div v-if="item.quantity <= item.min_threshold" class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/20 text-red-300 rounded-lg text-xs font-bold border border-red-500/20">
                        <alert-triangle-icon class="w-3 h-3" /> Low Stock Warning
                    </div>
                    <div v-else class="inline-flex items-center gap-2 px-3 py-1 bg-green-500/20 text-green-300 rounded-lg text-xs font-bold border border-green-500/20">
                        <check-circle-icon class="w-3 h-3" /> Healthy Level
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <button @click="openStockModal('in')" class="py-2 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg text-sm transition flex justify-center items-center gap-2 shadow-lg">
                            <plus-icon class="w-4 h-4" /> Add Stock
                        </button>
                        <button @click="openStockModal('out')" class="py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg text-sm transition flex justify-center items-center gap-2 shadow-lg">
                            <minus-icon class="w-4 h-4" /> Deduct
                        </button>
                    </div>
                </div>

                <div class="glass-panel p-6 space-y-4 text-sm">
                    <h3 class="font-bold text-white border-b border-white/10 pb-2 mb-2">Item Information</h3>

                    <div class="flex justify-between">
                        <span class="text-gray-500">Category</span>
                        <span class="text-white font-medium">{{ item.category?.name || 'Uncategorized' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Location</span>
                        <span class="text-white font-medium">{{ item.storage_location || 'General Storage' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Brand</span>
                        <span class="text-white font-medium">{{ item.brand || '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Supplier</span>
                        <span class="text-blue-400 font-medium cursor-pointer hover:underline">
                            <Link v-if="item.supplier_id" :href="route('suppliers.show', item.supplier_id)">
                                {{ item.supplier?.name }}
                            </Link>
                            <span v-else>-</span>
                        </span>
                    </div>
                    <div class="border-t border-white/10 pt-2 flex justify-between">
                        <span class="text-gray-500">Cost Value</span>
                        <span class="text-green-400 font-mono font-bold">${{ ( (item.quantity * ((item.packaging_type === 'box' && item.kits_per_box) ? (item.cost_per_unit / item.kits_per_box) : (item.cost_per_unit || 0))) ).toFixed(2) }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">

                <div v-if="batches && batches.length > 0" class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <layers-icon class="w-5 h-5 text-purple-400" /> Active Batches
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                                <tr>
                                    <th class="pb-2">Batch / Lot #</th>
                                    <th class="pb-2">Expiry</th>
                                    <th class="pb-2 text-right">Quantity</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr v-for="batch in batches" :key="batch.id">
                                    <td class="py-3 text-white font-mono">{{ batch.lot_number }}</td>
                                    <td class="py-3">
                                        <span :class="isExpiring(batch.expiry_date) ? 'text-red-400 font-bold' : 'text-gray-300'">
                                            {{ new Date(batch.expiry_date).toLocaleDateString() }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right text-gray-300">{{ batch.quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <history-icon class="w-5 h-5 text-blue-400" /> Recent Activity
                    </h3>
                    <div class="space-y-4">
                        <div v-for="log in history" :key="log.id" class="flex items-start gap-4 p-3 bg-white/5 rounded-xl border border-white/5">
                            <div class="p-2 rounded-lg shrink-0"
                                :class="log.type === 'in' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'">
                                <arrow-down-icon v-if="log.type === 'in'" class="w-5 h-5" />
                                <arrow-up-icon v-else class="w-5 h-5" />
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <p class="text-white font-bold text-sm">
                                        {{ log.type === 'in' ? 'Stock Added' : 'Stock Used' }}
                                    </p>
                                    <span class="text-xs text-gray-500">{{ new Date(log.created_at).toLocaleDateString() }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ log.notes || 'No notes provided.' }}</p>
                                <p class="text-xs text-gray-500 mt-1">by {{ log.user?.name || 'Unknown' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold font-mono" :class="log.type === 'in' ? 'text-green-400' : 'text-red-400'">
                                    {{ log.type === 'in' ? '+' : '-' }}{{ log.quantity }}
                                </span>
                            </div>
                        </div>

                        <div v-if="!history || history.length === 0" class="text-center text-gray-500 py-4 italic">
                            No history found.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <StockModal
            v-if="showStockModal"
            :show="showStockModal"
            :type="modalType"
            :item="item"
            @close="showStockModal = false"
        />

    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StockModal from '@/Components/StockModal.vue';
import {
    ArrowLeft as ArrowLeftIcon,
    Pencil as PencilIcon,
    Trash as TrashIcon,
    Package as PackageIcon,
    AlertTriangle as AlertTriangleIcon,
    CheckCircle as CheckCircleIcon,
    Plus as PlusIcon,
    Minus as MinusIcon,
    Layers as LayersIcon,
    History as HistoryIcon,
    ArrowDown as ArrowDownIcon,
    ArrowUp as ArrowUpIcon,
    QrCode as QrCodeIcon
} from 'lucide-vue-next';

// Props with default values to prevent undefined errors
const props = defineProps({
    item: Object,
    batches: { type: Array, default: () => [] },
    history: { type: Array, default: () => [] }
});

// Modal State
const showStockModal = ref(false);
const modalType = ref('in');

const openStockModal = (type) => {
    modalType.value = type;
    showStockModal.value = true;
};

// Actions
const deleteItem = () => {
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        router.delete(route('items.destroy', props.item.id));
    }
};

// Helper to check expiry
const isExpiring = (dateString) => {
    if (!dateString) return false;
    const expiry = new Date(dateString);
    const today = new Date();
    const diffTime = expiry - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays < 30;
};
</script>
