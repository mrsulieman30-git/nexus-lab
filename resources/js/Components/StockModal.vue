<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4 transition-all">
        <div class="bg-[#1e293b] w-full max-w-lg rounded-2xl border border-white/10 shadow-2xl overflow-hidden animate-fade-in-up">

            <div class="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <package-plus-icon class="w-6 h-6 text-blue-400" />
                    {{ form.type === 'in' ? 'Incoming Stock' : 'Stock Usage' }}
                </h3>
                <button @click="close" class="text-gray-400 hover:text-white transition">
                    <x-icon class="w-6 h-6" />
                </button>
            </div>

            <div class="p-6">

                <div v-if="loadingCheck" class="text-center py-8">
                    <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
                    <p class="text-gray-400 animate-pulse">Checking pending orders...</p>
                </div>

                <div v-else-if="form.type === 'in' && pendingOrders.length > 0 && !workflowComplete">
                    <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-5 mb-6">
                        <h4 class="text-blue-300 font-bold text-lg mb-2">Pending Orders Found</h4>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            We found <strong>{{ pendingOrders.length }}</strong> open order(s) for <span class="text-white font-bold">{{ item.name }}</span>.
                            Is this delivery linked to one of them?
                        </p>
                    </div>

                    <div class="space-y-3 mb-6 max-h-60 overflow-y-auto custom-scrollbar pr-2">
                        <button v-for="order in pendingOrders" :key="order.id"
                            @click="selectOrder(order)"
                            class="w-full flex items-center justify-between p-4 bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/50 rounded-xl transition group text-left">
                            <div>
                                <p class="font-bold text-white text-lg">{{ order.po_number }}</p>
                                <p class="text-xs text-gray-400">{{ order.supplier?.name }} • {{ new Date(order.order_date).toLocaleDateString() }}</p>
                            </div>
                            <div class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full group-hover:bg-blue-500 shadow-lg">
                                Link
                            </div>
                        </button>
                    </div>

                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-white/10"></div>
                        <span class="flex-shrink-0 mx-4 text-gray-500 text-xs uppercase font-bold tracking-widest">Or</span>
                        <div class="flex-grow border-t border-white/10"></div>
                    </div>

                    <button @click="selectOrder(null)" class="w-full py-3 mt-2 text-gray-400 hover:text-white font-bold text-sm hover:bg-white/5 rounded-xl transition border border-transparent hover:border-white/10">
                        No, this is a separate / new purchase
                    </button>
                </div>

                <div v-else>

                    <div v-if="selectedOrder" class="mb-6 flex items-center gap-3 p-3 bg-green-500/10 border border-green-500/30 rounded-xl">
                        <div class="p-2 bg-green-600 rounded-full shadow-lg">
                            <check-circle-icon class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <p class="text-green-400 text-xs font-bold uppercase tracking-wider">Linked to Order</p>
                            <p class="text-white font-bold text-sm">{{ selectedOrder.po_number }}</p>
                        </div>
                        <button @click="resetWorkflow" class="ml-auto text-xs text-gray-400 underline hover:text-white transition">Change</button>
                    </div>

                    <form @submit.prevent="submit" class="space-y-5">

                        <div v-if="Object.keys(form.errors).length > 0" class="bg-red-500/10 border border-red-500/30 p-4 rounded-xl">
                            <p class="text-red-400 text-sm font-bold mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-red-300 text-xs space-y-1">
                                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
                            </ul>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Quantity {{ form.type === 'in' ? 'Received' : 'Used' }}</label>
                            <div class="relative mt-1">
                                <input v-model="form.quantity" type="number" min="1" class="w-full glass-input pl-4 pr-12 text-lg font-bold" required autofocus />
                                <span class="absolute right-4 top-3 text-gray-500 text-sm font-bold">{{ item.unit }}</span>
                            </div>

                            <div class="mt-3">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ form.type === 'in' ? 'Packaging Level' : 'Deduction Unit' }}</label>
                                <select v-model="form.package_level" :disabled="form.type === 'out'" class="w-full mt-1 glass-input">
                                    <option value="box">Box (contains kits)</option>
                                    <option value="kit">Kit</option>
                                    <option value="reagent">Reagent (bottle)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">{{ form.type === 'in' ? 'Choose the packaging level for this addition (affects quantity and cost calculation).' : 'Choose the packaging level for this usage (affects quantity conversion).' }}</p>
                            </div>
                        </div>

                        <div v-if="form.type === 'in'" class="space-y-4 pt-4 border-t border-white/5">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Batch / Lot Number</label>
                                <input v-model="form.lot_number" type="text" placeholder="Optional" class="w-full glass-input mt-1" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Expiry Date</label>
                                    <input v-model="form.expiry_date" type="date" class="w-full glass-input mt-1 [color-scheme:dark]" />
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Cost Per {{ form.package_level ? form.package_level.charAt(0).toUpperCase() + form.package_level.slice(1) : 'Unit' }}</label>
                                    <div class="relative mt-1">
                                        <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                        <input v-model="form.cost_per_unit" type="number" step="0.01" class="w-full glass-input pl-7" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Notes</label>
                            <textarea v-model="form.notes" rows="2" class="w-full glass-input mt-1 text-sm" placeholder="Any additional details..."></textarea>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" @click="close" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-gray-400 font-bold rounded-xl transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="flex-1 py-3 font-bold rounded-xl shadow-lg transition text-white flex justify-center items-center gap-2"
                                :class="form.type === 'in' ? 'bg-blue-600 hover:bg-blue-500' : 'bg-red-600 hover:bg-red-500'">
                                <span v-if="form.processing" class="animate-pulse">Saving...</span>
                                <span v-else>Confirm {{ form.type === 'in' ? 'Arrival' : 'Usage' }}</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import { PackagePlus as PackagePlusIcon, X as XIcon, CheckCircle as CheckCircleIcon } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    type: String, // 'in' or 'out'
    item: Object
});

const emit = defineEmits(['close']);

const loadingCheck = ref(false);
const pendingOrders = ref([]);
const selectedOrder = ref(null);
const workflowComplete = ref(false);

const form = useForm({
    quantity: '',
    type: props.type,
    notes: '',
    lot_number: '',
    expiry_date: '',
    cost_per_unit: '',
    po_id: null,
    package_level: 'kit'
});

// --- Watcher: Added { immediate: true } to trigger on mount ---
watch(() => props.show, async (isOpen) => {
    if (isOpen && props.item) {
        form.reset();
        form.clearErrors();
        form.type = props.type;
        form.cost_per_unit = props.item.cost_per_unit;

        pendingOrders.value = [];
        selectedOrder.value = null;
        workflowComplete.value = false;

        if (props.type === 'in') {
            loadingCheck.value = true;
            try {
                const res = await axios.get(route('purchase-orders.check'), { params: { item_id: props.item.id } });
                pendingOrders.value = res.data.orders;

                if (pendingOrders.value.length === 0) {
                    workflowComplete.value = true;
                    form.notes = 'Direct Stock Entry';
                }
            } catch (error) {
                console.error("Error checking POs:", error);
                workflowComplete.value = true;
            } finally {
                loadingCheck.value = false;
            }

            if (props.item.packaging_type) {
                if (props.item.packaging_type === 'box') form.package_level = 'box';
                else if (props.item.packaging_type === 'kit') form.package_level = 'kit';
                else if (props.item.packaging_type === 'reagent') form.package_level = 'reagent';
            } else {
                if ((props.item.unit || '').includes('kit')) form.package_level = 'kit';
                else if ((props.item.unit || '').includes('bottle')) form.package_level = 'reagent';
            }

        } else {
            workflowComplete.value = true;
            form.notes = 'Routine Usage';
        }
    }
}, { immediate: true });

const selectOrder = (order) => {
    if (order) {
        selectedOrder.value = order;
        form.po_id = order.id;
        form.notes = `Received from PO #${order.po_number}`;
    } else {
        selectedOrder.value = null;
        form.po_id = null;
        form.notes = 'Direct Purchase';
    }
    workflowComplete.value = true;
};

const resetWorkflow = () => {
    workflowComplete.value = false;
    selectedOrder.value = null;
    form.po_id = null;
};

const close = () => {
    emit('close');
};

const submit = () => {
    form.clearErrors();
    form.post(route('items.transaction', props.item.id), {
        onSuccess: () => close(),
        onError: (err) => console.log('Transaction Validation Errors:', err)
    });
};
</script>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.2s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
</style>
