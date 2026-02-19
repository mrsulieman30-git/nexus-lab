<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Purchase Orders
                </h2>
                <Link :href="route('purchase-orders.create')" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg transition flex items-center gap-2">
                    <plus-icon class="w-4 h-4" /> New Order Request
                </Link>
            </div>
        </template>

        <div v-if="$page.props.flash?.message" class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <check-circle-icon class="w-5 h-5" />
            {{ $page.props.flash.message }}
        </div>

        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex items-center gap-4 bg-white/5 p-4 rounded-xl border border-white/10">
                <div class="relative flex-1">
                    <search-icon class="absolute left-3 top-3 w-4 h-4 text-gray-500" />
                    <input v-model="search" type="text" placeholder="Search PO Number or Supplier..."
                        class="w-full glass-input pl-10" />
                </div>
                <select v-model="statusFilter" class="glass-input w-48 [&>option]:bg-gray-800">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="ordered">Partial / Ordered</option>
                    <option value="received">Received</option>
                </select>
            </div>

            <div class="glass-panel p-6">
                <table class="w-full text-left">
                    <thead class="text-xs text-gray-500 uppercase border-b border-white/10">
                        <tr>
                            <th class="pb-3 pl-2">PO Number</th>
                            <th class="pb-3">Supplier</th>
                            <th class="pb-3">Order Date</th>
                            <th class="pb-3 text-center">Items</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-right pr-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-sm">
                        <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-white/5 transition group">

                            <td class="py-4 pl-2 font-mono font-bold text-blue-400">
                                <Link :href="route('purchase-orders.show', order.id)" class="hover:underline">
                                    {{ order.po_number }}
                                </Link>
                            </td>

                            <td class="py-4 font-medium text-white">{{ order.supplier?.name || 'Unknown' }}</td>

                            <td class="py-4 text-gray-400">
                                {{ new Date(order.order_date).toLocaleDateString() }}
                            </td>

                            <td class="py-4 text-center text-gray-300">
                                {{ order.items?.length || 0 }}
                            </td>

                            <td class="py-4 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border tracking-wider"
                                    :class="{
                                        'bg-yellow-500/10 text-yellow-400 border-yellow-500/20': order.status === 'pending',
                                        'bg-blue-500/10 text-blue-400 border-blue-500/20': order.status === 'ordered',
                                        'bg-green-500/10 text-green-400 border-green-500/20': order.status === 'received',
                                        'bg-red-500/10 text-red-400 border-red-500/20': order.status === 'cancelled'
                                    }">
                                    {{ order.status }}
                                </span>
                            </td>

                            <td class="py-4 text-right pr-2 flex justify-end gap-2">

                                <Link :href="route('purchase-orders.show', order.id)" class="p-2 text-gray-500 hover:text-white transition bg-white/5 rounded-lg hover:bg-white/10" title="View Order">
                                    <eye-icon class="w-4 h-4" />
                                </Link>

                                <button v-if="order.status !== 'received'" @click="markReceived(order)"
                                    class="p-2 text-gray-500 hover:text-green-400 transition bg-white/5 rounded-lg hover:bg-white/10"
                                    title="Mark as Fully Received">
                                    <check-square-icon class="w-4 h-4" />
                                </button>

                                <button v-if="order.status === 'pending'" @click="deleteOrder(order)"
                                    class="p-2 text-gray-500 hover:text-red-400 transition bg-white/5 rounded-lg hover:bg-white/10"
                                    title="Delete Order">
                                    <trash-icon class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>

                        <tr v-if="filteredOrders.length === 0">
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                <shopping-cart-icon class="w-12 h-12 mx-auto mb-3 opacity-20" />
                                No orders found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="orders.links && orders.links.length > 3" class="mt-4 flex justify-end gap-1">
                    <Link v-for="(link, key) in orders.links" :key="key"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 rounded text-xs border border-white/10"
                        :class="link.active ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-white/5'"
                    />
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Plus as PlusIcon,
    Search as SearchIcon,
    Eye as EyeIcon,
    CheckSquare as CheckSquareIcon,
    Trash as TrashIcon,
    ShoppingCart as ShoppingCartIcon,
    CheckCircle as CheckCircleIcon
} from 'lucide-vue-next';

const props = defineProps({ orders: Object });

const search = ref('');
const statusFilter = ref('');

// Client-side filtering
const filteredOrders = computed(() => {
    if (!props.orders || !props.orders.data) return [];

    return props.orders.data.filter(order => {
        const matchesSearch = order.po_number.toLowerCase().includes(search.value.toLowerCase()) ||
                              (order.supplier?.name || '').toLowerCase().includes(search.value.toLowerCase());
        const matchesStatus = statusFilter.value ? order.status === statusFilter.value : true;

        return matchesSearch && matchesStatus;
    });
});

const markReceived = (order) => {
    if(confirm(`Mark ${order.po_number} as FULLY received? This will close the order.`)) {
        router.post(route('purchase-orders.receive', order.id));
    }
};

const deleteOrder = (order) => {
    if(confirm(`Are you sure you want to delete ${order.po_number}?`)) {
        router.delete(route('purchase-orders.destroy', order.id));
    }
};
</script>
