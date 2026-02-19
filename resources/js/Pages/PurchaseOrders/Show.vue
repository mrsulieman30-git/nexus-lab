<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center no-print-section">
                <div class="flex items-center gap-4">
                     <Link :href="route('purchase-orders.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                        <arrow-left-icon class="w-5 h-5" />
                    </Link>
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Order Details
                    </h2>
                </div>
                <div class="flex gap-3">
                    <button v-if="order.status !== 'received'" @click="markAsReceived" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-bold shadow-lg transition flex items-center gap-2">
                        <check-circle-icon class="w-4 h-4" /> Mark Received
                    </button>

                    <button @click="printPO" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg transition flex items-center gap-2">
                        <printer-icon class="w-4 h-4" /> Print / Save PDF
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-[850px] mx-auto pb-12">

            <div id="printable-area" class="bg-white text-black p-12 shadow-xl rounded-sm relative">

                <div class="flex justify-between items-start border-b-2 border-gray-800 pb-8 mb-8">
                    <div class="flex items-center gap-5">
                        <div class="w-20 h-20 border-2 border-black flex items-center justify-center rounded-lg overflow-hidden">
                             <img v-if="lab.logo_url" :src="lab.logo_url" class="w-full h-full object-contain p-1" />
                             <flask-conical-icon v-else class="w-10 h-10 text-black" stroke-width="1.5" />
                        </div>
                        <div>
                            <h1 class="text-3xl font-black uppercase tracking-wide leading-none">{{ lab.name }}</h1>
                            <p class="text-sm text-gray-600 font-bold mt-1 tracking-wider uppercase">Purchase Order</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-800 mb-1">{{ order.po_number }}</h2>
                        <p class="text-sm text-gray-600">Date: {{ new Date(order.order_date).toLocaleDateString() }}</p>
                        <div class="mt-2 inline-block px-3 py-1 border border-black text-xs font-bold uppercase tracking-widest rounded"
                            :class="order.status === 'received' ? 'bg-black text-white' : 'bg-white text-black'">
                            {{ order.status }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-16 mb-12">
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase mb-2 border-b border-gray-300 pb-1">To (Supplier)</h3>
                        <p class="font-bold text-lg text-gray-900">{{ order.supplier.name }}</p>
                        <p v-if="order.supplier.contact_person" class="text-sm text-gray-800 mt-1">Attn: {{ order.supplier.contact_person }}</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line mt-1">{{ order.supplier.address }}</p>
                        <p v-if="order.supplier.email" class="text-sm text-gray-700 mt-2 flex items-center gap-2">
                            <mail-icon class="w-3 h-3" /> {{ order.supplier.email }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase mb-2 border-b border-gray-300 pb-1">Ship To (Laboratory)</h3>
                        <p class="font-bold text-lg text-gray-900">{{ lab.name }}</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line mt-1">{{ lab.address || 'Address not configured.' }}</p>
                        <p v-if="lab.phone" class="text-sm text-gray-700 mt-2 flex items-center gap-2">
                            <phone-icon class="w-3 h-3" /> {{ lab.phone }}
                        </p>
                         <p v-if="lab.website" class="text-sm text-gray-700 mt-1 flex items-center gap-2">
                            <globe-icon class="w-3 h-3" /> {{ lab.website }}
                        </p>
                    </div>
                </div>

                <div class="mb-12">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-black">
                                <th class="py-3 pl-2 text-sm uppercase font-bold text-gray-600 tracking-wider w-12">#</th>
                                <th class="py-3 text-sm uppercase font-bold text-gray-600 tracking-wider">Item Description</th>
                                <th class="py-3 text-sm uppercase font-bold text-gray-600 tracking-wider">Brand</th>
                                <th class="py-3 text-center text-sm uppercase font-bold text-gray-600 tracking-wider w-32">Quantity</th>
                                <th class="py-3 text-center text-sm uppercase font-bold text-gray-600 tracking-wider w-20">Rec'd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in order.items" :key="row.id" class="border-b border-gray-200">
                                <td class="py-4 pl-2 text-gray-500 font-mono text-sm">{{ index + 1 }}</td>
                                <td class="py-4 font-bold text-gray-800 text-base">{{ row.item.name }}</td>
                                <td class="py-4 text-gray-600">{{ row.item.brand || 'N/A' }}</td>
                                <td class="py-4 text-center">
                                    <span class="font-mono font-bold text-lg text-black">{{ row.quantity }}</span>
                                    <span class="text-xs font-bold text-gray-500 uppercase ml-1">{{ row.item.unit }}</span>
                                </td>
                                <td class="py-4 text-center">
                                    <div v-if="row.is_received || order.status === 'received'" class="flex justify-center">
                                        <check-circle-icon class="w-5 h-5 text-green-600" />
                                    </div>
                                    <span v-else class="text-gray-300">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="border-t-2 border-gray-800 pt-6">
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Instructions</h3>
                    <div class="text-sm text-gray-700 bg-gray-50 p-4 border border-gray-200 rounded italic min-h-[80px]">
                        {{ order.notes || 'Please provide a formal proforma invoice. Ensure minimum expiry of 12 months.' }}
                    </div>
                </div>

                <div class="mt-20 flex justify-end print:break-inside-avoid">
                    <div class="text-center w-64">
                        <div class="border-b-2 border-black h-1 mb-2"></div>
                        <p class="text-xs font-bold uppercase text-gray-600 tracking-widest">Authorized Signature</p>
                        <p class="text-xs text-gray-400 mt-1 uppercase">{{ order.user.name }}</p>
                    </div>
                </div>

                <div class="absolute bottom-4 left-0 w-full text-center">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">Generated by Nexus Lab System</p>
                </div>

            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import {
    Printer as PrinterIcon,
    FlaskConical as FlaskConicalIcon,
    Phone as PhoneIcon,
    Mail as MailIcon,
    Globe as GlobeIcon,
    ArrowLeft as ArrowLeftIcon,
    CheckCircle as CheckCircleIcon
} from 'lucide-vue-next';

const props = defineProps({
    order: Object,
    lab: Object
});

const printPO = () => {
    window.print();
};

const markAsReceived = () => {
    if(confirm('Are you sure you want to mark this ENTIRE order as received?')) {
        router.post(route('purchase-orders.receive', props.order.id));
    }
};
</script>

<style scoped>
/* STRICT PRINT CSS */
@media print {
    @page { size: A4; margin: 0; }
    body * { visibility: hidden; }
    nav, aside, header, .no-print-section { display: none !important; }

    #printable-area {
        visibility: visible;
        position: fixed;
        top: 0; left: 0;
        width: 210mm; height: 297mm;
        margin: 0; padding: 15mm;
        background-color: white !important;
        color: black !important;
        z-index: 9999;
        font-family: Arial, sans-serif;
    }
    #printable-area * { visibility: visible; }
}
</style>
