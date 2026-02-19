<template>
    <div class="bg-white text-black p-4 w-[350px] border-2 border-black flex items-center gap-4 rounded-lg">

        <div class="shrink-0">
            <QrcodeVue :value="itemUrl" :size="100" level="H" render-as="svg" />
        </div>

        <div class="flex-1 overflow-hidden">
            <h3 class="font-bold text-lg leading-tight uppercase mb-1 truncate">{{ item.name }}</h3>

            <div class="text-xs font-mono space-y-0.5">
                <p><span class="font-bold">LOT:</span> {{ item.lot_number || 'N/A' }}</p>
                <p><span class="font-bold">EXP:</span> {{ item.expiry_date ? item.expiry_date.split('T')[0] : 'N/A' }}</p>
                <p><span class="font-bold">LOC:</span> {{ item.storage_location || 'General' }}</p>
            </div>

            <div class="mt-2 text-[10px] uppercase text-gray-500 font-bold tracking-widest border-t border-black/10 pt-1">
                Property of Nexus Lab
            </div>
        </div>

    </div>
</template>

<script setup>
import QrcodeVue from 'qrcode.vue';
import { computed } from 'vue';

const props = defineProps({
    item: Object
});

const itemUrl = computed(() => {
    // Generate the full URL to the item profile
    return `${window.location.origin}/items/${props.item.id}/profile`;
});
</script>
