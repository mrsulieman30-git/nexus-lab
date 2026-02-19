<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('suppliers.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                    <arrow-left-icon class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-bold leading-tight">Vendor Profile</h2>
            </div>
        </template>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="space-y-6">
                <div class="glass-panel p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                        <truck-icon class="w-48 h-48" />
                    </div>

                    <h1 class="text-2xl font-bold text-white mb-2 relative z-10">{{ supplier.name }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/20 inline-block mb-6 relative z-10">
                        Active Supplier
                    </span>

                    <div class="space-y-4 relative z-10 border-t border-white/10 pt-6">
                        <div v-if="supplier.contact_person" class="flex items-start gap-3">
                            <user-icon class="w-5 h-5 text-gray-400 mt-0.5" />
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Contact Person</p>
                                <p class="text-white">{{ supplier.contact_person }}</p>
                            </div>
                        </div>
                        <div v-if="supplier.phone" class="flex items-start gap-3">
                            <phone-icon class="w-5 h-5 text-gray-400 mt-0.5" />
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Phone</p>
                                <p class="text-white font-mono">{{ supplier.phone }}</p>
                            </div>
                        </div>
                        <div v-if="supplier.email" class="flex items-start gap-3">
                            <mail-icon class="w-5 h-5 text-gray-400 mt-0.5" />
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Email</p>
                                <a :href="'mailto:' + supplier.email" class="text-blue-400 hover:underline break-all">{{ supplier.email }}</a>
                            </div>
                        </div>
                        <div v-if="supplier.address" class="flex items-start gap-3">
                            <map-pin-icon class="w-5 h-5 text-gray-400 mt-0.5" />
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Address</p>
                                <p class="text-gray-300 whitespace-pre-line">{{ supplier.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <package-icon class="w-5 h-5 text-blue-400" /> Supplied Items
                    </h3>

                    <div v-if="items.length > 0" class="overflow-hidden rounded-xl border border-white/10">
                        <table class="w-full text-left">
                            <thead class="bg-white/5 text-gray-400 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3">Item Name</th>
                                    <th class="px-4 py-3">Current Stock</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm text-gray-300">
                                <tr v-for="item in items" :key="item.id" class="hover:bg-white/5 transition">
                                    <td class="px-4 py-3 font-medium text-white">
                                        {{ item.name }}
                                        <span class="block text-xs text-gray-500 font-normal">{{ item.brand }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span :class="item.quantity <= item.min_threshold ? 'text-red-400 font-bold' : 'text-green-400'">
                                            {{ item.quantity }} {{ item.unit }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Link :href="route('items.show', item.id)" class="text-blue-400 hover:text-blue-300 hover:underline text-xs font-bold">
                                            View Item
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="text-center py-12 border-2 border-dashed border-white/10 rounded-xl text-gray-500">
                        <package-icon class="w-10 h-10 mx-auto mb-2 opacity-50" />
                        <p>No items currently linked to this supplier.</p>
                        <Link :href="route('items.index')" class="text-blue-400 hover:underline mt-2 inline-block text-sm font-bold">
                            Go to Inventory to link items
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    Truck as TruckIcon,
    User as UserIcon,
    Phone as PhoneIcon,
    Mail as MailIcon,
    MapPin as MapPinIcon,
    ArrowLeft as ArrowLeftIcon,
    Package as PackageIcon
} from 'lucide-vue-next';

defineProps({
    supplier: Object,
    items: Array
});
</script>
