<template>
    <AppLayout>
        <template #header>
                <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Inventory Catalog
                </h2>
                <Link v-if="$page.props.auth.user.role !== 'lab_tech'" :href="route('items.create')" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-xl font-medium shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-transform hover:scale-105">
                    <plus-icon class="w-5 h-5" /> Add New Item
                </Link>
            </div>
        </template>

        <div v-if="$page.props.flash?.message" class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2 animate-fade-in-down">
            <check-circle-icon class="w-5 h-5" />
            {{ $page.props.flash.message }}
        </div>

        <div class="glass-panel p-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <search-icon class="absolute left-3 top-3 w-4 h-4 text-gray-500" />
                        <input v-model="search" type="text" placeholder="Search name, brand, SKU..." class="w-full glass-input pl-10" autofocus>
                    </div>
                    <select v-model="categoryFilter" class="glass-input w-40 text-sm [&>option]:bg-gray-800">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-white/5">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-gray-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Item Details</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Stock Level</th>
                            <th class="px-6 py-4 text-center">Quick Actions</th>
                            <th class="px-6 py-4 text-right">More</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm text-gray-300">
                        <tr v-for="item in items.data" :key="item.id" class="group hover:bg-white/5 transition-colors">

                            <td class="px-6 py-4">
                                <Link :href="route('items.show', item.id)" class="font-bold text-white hover:text-blue-400 hover:underline transition text-base block mb-1">
                                    {{ item.name }}
                                </Link>
                                <div class="text-xs text-gray-500 font-mono flex items-center gap-2">
                                    <span v-if="item.brand" class="bg-white/10 px-1.5 py-0.5 rounded text-gray-300">{{ item.brand }}</span>
                                    <span v-if="item.sku" class="opacity-75">SKU: {{ item.sku }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20"
                                    :style="item.category ? { backgroundColor: item.category.color_hex + '20', color: item.category.color_hex, borderColor: item.category.color_hex + '40' } : {}">
                                    {{ item.category ? item.category.name : 'General' }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <div class="text-base font-bold font-mono" :class="(item.total_kits <= (item.threshold_kits || item.min_threshold)) ? 'text-red-400' : 'text-white'">
                                            {{ item.quantity }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ item.unit }}</div>
                                        <div v-if="item.packaging_type === 'box' && item.kits_per_box" class="text-xs text-gray-400 mt-1">
                                            {{ item.boxes_remaining }} box(es) + {{ item.kits_remaining }} kit(s)
                                            • Total kits: {{ item.total_kits }}
                                        </div>
                                    </div>
                                </div>
                                <div class="w-24 h-1.5 bg-gray-700 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        :class="item.quantity <= item.min_threshold ? 'bg-red-500 w-full animate-pulse' : 'bg-green-500'"
                                        :style="{ width: item.quantity <= item.min_threshold ? '100%' : '60%' }">
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openModal('out', item)" class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white transition flex items-center justify-center border border-red-500/20" title="Deduct Stock">
                                        <minus-icon class="w-4 h-4" />
                                    </button>

                                    <button @click="openModal('in', item)" class="w-8 h-8 rounded-lg bg-green-500/10 text-green-400 hover:bg-green-500 hover:text-white transition flex items-center justify-center border border-green-500/20" title="Add Stock">
                                        <plus-icon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Link :href="route('items.history', item.id)" class="p-2 hover:bg-purple-500/20 text-purple-400 rounded-lg transition" title="View History">
                                        <history-icon class="w-4 h-4" />
                                    </Link>

                                    <Link :href="route('items.edit', item.id)" class="p-2 hover:bg-blue-500/20 text-blue-400 rounded-lg transition" title="Edit Item">
                                        <pencil-icon class="w-4 h-4" />
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="items.data.length === 0">
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                No items found matching your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="items.total > 0" class="mt-6 flex justify-between items-center text-xs text-gray-500">
                <div>Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results</div>
                <div class="flex gap-2">
                    <Link v-for="link in items.links" :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1.5 rounded-lg border border-white/10 transition"
                        :class="{'bg-blue-600 text-white border-blue-600': link.active, 'hover:bg-white/5 hover:text-white': !link.active && link.url, 'opacity-50 cursor-not-allowed': !link.url}"
                    />
                </div>
            </div>
        </div>

        <StockModal
            v-if="modalState.show"
            :show="modalState.show"
            :type="modalState.type"
            :item="modalState.item"
            @close="closeModal"
        />

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import StockModal from '@/Components/StockModal.vue';
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import {
    Search as SearchIcon,
    Plus as PlusIcon,
    Minus as MinusIcon,
    Pencil as PencilIcon,
    CheckCircle as CheckCircleIcon,
    History as HistoryIcon
} from 'lucide-vue-next';

const props = defineProps({
    items: Object,
    categories: Array,
    filters: Object,
    lab: Object
});

// Search & Filter Logic
const search = ref(props.filters.search || '');
const categoryFilter = ref(props.filters.category || '');

const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('items.index'), {
        search: search.value,
        category: categoryFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

// Watchers to trigger search
watch(search, debouncedSearch);
watch(categoryFilter, applyFilters);

// Modal Logic
const modalState = ref({
    show: false,
    type: 'in', // Default to 'in'
    item: null
});

const openModal = (type, item) => {
    // We map 'add' -> 'in' and 'remove' -> 'out' implicitly by using 'in/out' in template
    modalState.value = {
        show: true,
        type: type,
        item: item
    };
};

const closeModal = () => {
    modalState.value.show = false;
    // We keep the item momentarily to prevent layout shift during close animation, then clear
    setTimeout(() => {
        modalState.value.item = null;
    }, 200);
};
</script>
