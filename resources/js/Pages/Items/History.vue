<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('items.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                    <arrow-left-icon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-bold leading-tight">Audit Trail</h2>
                    <p class="text-sm text-gray-400">History for {{ item.name }}</p>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto glass-panel p-8">

            <div class="flex items-center justify-between mb-8 pb-8 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="p-4 rounded-2xl bg-blue-500/20 text-blue-400">
                        <package-icon class="w-8 h-8" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Current Stock</p>
                        <h3 class="text-3xl font-bold text-white">{{ item.quantity }} <span class="text-base font-normal text-gray-500">{{ item.unit }}</span></h3>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Total Transactions</p>
                    <p class="text-xl font-bold text-white">{{ logs.total }}</p>
                </div>
            </div>

            <div class="relative pl-4">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-white/10"></div>

                <div v-for="log in logs.data" :key="log.id" class="relative pl-10 mb-8 last:mb-0 group">

                    <div class="absolute left-0 top-1.5 w-8 h-8 rounded-full border-4 border-[#0f172a] flex items-center justify-center z-10"
                         :class="log.type === 'in' ? 'bg-green-500' : 'bg-red-500'">
                        <component :is="log.type === 'in' ? 'PlusIcon' : 'MinusIcon'" class="w-4 h-4 text-white" />
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 hover:bg-white/[0.07] transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-white flex items-center gap-2">
                                    {{ log.type === 'in' ? 'Stock Added' : 'Stock Used' }}
                                    <span class="text-xs px-2 py-0.5 rounded-md bg-white/10 text-gray-400 font-normal">
                                        {{ log.quantity }} {{ item.unit }}
                                    </span>
                                </h4>
                                <p class="text-sm text-gray-400 mt-1">
                                    <span class="text-blue-400">{{ log.user.name }}</span>
                                    {{ log.type === 'in' ? 'restocked items.' : 'removed items.' }}
                                </p>
                                <p v-if="log.notes" class="text-sm text-gray-500 mt-2 italic">"{{ log.notes }}"</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ new Date(log.created_at).toLocaleDateString() }}</p>
                                <p class="text-xs text-gray-600">{{ new Date(log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div v-if="logs.data.length === 0" class="pl-10 text-gray-500 italic">
                    No history recorded for this item yet.
                </div>
            </div>

        </div>

    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
    ArrowLeft as ArrowLeftIcon,
    Package as PackageIcon,
    Plus as PlusIcon,
    Minus as MinusIcon
} from 'lucide-vue-next';

const props = defineProps({
    item: Object,
    logs: Object
});
</script>
