<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('purchase-orders.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                    <arrow-left-icon class="w-5 h-5" />
                </Link>
                <h2 class="font-semibold text-xl text-white leading-tight">Create Order Request</h2>
            </div>
        </template>

        <div class="max-w-4xl mx-auto">
            <div class="glass-panel p-8">

                <form @submit.prevent="submit" class="space-y-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-b border-white/10 pb-8">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Select Supplier *</label>
                            <div class="relative">
                                <select v-model="form.supplier_id" class="w-full glass-input pl-10 [&>option]:bg-gray-800" required>
                                    <option value="" disabled>Choose Vendor...</option>
                                    <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
                                </select>
                                <truck-icon class="w-4 h-4 text-gray-500 absolute left-3.5 top-3.5" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Request Date</label>
                            <input v-model="form.order_date" type="date" class="w-full glass-input [color-scheme:dark]" required />
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-4">
                            <h3 class="font-bold text-white text-lg flex items-center gap-2">
                                <package-plus-icon class="w-5 h-5 text-blue-400" /> Items Required
                            </h3>
                            <span class="text-xs text-gray-500">{{ form.items.length }} items added</span>
                        </div>

                        <div class="space-y-3">
                            <div v-for="(row, index) in form.items" :key="index" class="flex flex-col md:flex-row items-start md:items-end gap-3 p-4 bg-white/5 rounded-xl border border-white/5 group hover:border-white/10 transition">

                                <div class="w-full md:flex-1">
                                    <label class="text-[10px] text-gray-500 uppercase mb-1 block font-bold">Item Description</label>
                                    <select v-model="row.id" class="w-full glass-input text-sm py-2.5 [&>option]:bg-gray-800" required>
                                        <option value="" disabled>Select Item...</option>
                                        <option v-for="item in items" :key="item.id" :value="item.id">
                                            {{ item.name }} ({{ item.unit }})
                                        </option>
                                    </select>
                                </div>

                                <div class="w-full md:w-32">
                                    <label class="text-[10px] text-gray-500 uppercase mb-1 block font-bold">Qty Needed</label>
                                    <input v-model="row.quantity" type="number" min="1" class="w-full glass-input text-sm py-2.5" placeholder="1" required />
                                </div>

                                <button type="button" @click="removeItem(index)" class="self-end md:mb-1 p-2 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Remove Item">
                                    <trash-icon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <button type="button" @click="addItem" class="mt-4 px-4 py-2 bg-white/5 hover:bg-white/10 border border-dashed border-white/20 rounded-lg text-sm text-blue-400 font-bold flex items-center gap-2 transition w-full justify-center hover:border-blue-500/30">
                            <plus-icon class="w-4 h-4" /> Add Another Item
                        </button>
                    </div>

                    <div class="border-t border-white/10 pt-6">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Notes to Supplier</label>
                        <textarea v-model="form.notes" placeholder="E.g., Please provide your best quote for these items. Ensure expiry dates are > 1 year." class="glass-input w-full text-sm min-h-[100px]"></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <Link :href="route('purchase-orders.index')" class="px-6 py-3 rounded-xl hover:bg-white/5 text-gray-300 transition mr-4 font-medium">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-105 flex items-center gap-2">
                            <check-circle-icon class="w-5 h-5" />
                            {{ form.processing ? 'Generating...' : 'Generate Request' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import {
    Trash as TrashIcon,
    Truck as TruckIcon,
    Plus as PlusIcon,
    PackagePlus as PackagePlusIcon,
    ArrowLeft as ArrowLeftIcon,
    CheckCircle as CheckCircleIcon
} from 'lucide-vue-next';

const props = defineProps({
    suppliers: Array,
    items: Array
});

const form = useForm({
    supplier_id: '',
    order_date: new Date().toISOString().substr(0, 10),
    notes: '',
    items: [
        { id: '', quantity: 1 }
    ]
});

const addItem = () => {
    form.items.push({ id: '', quantity: 1 });
};

const removeItem = (index) => {
    // Prevent removing the last row to ensure at least one item is sent
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('purchase-orders.store'));
};
</script>
