<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('items.index')" class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 transition">
                    <arrow-left-icon class="w-5 h-5" />
                </Link>
                <h2 class="text-xl font-bold leading-tight">Add New Inventory Item</h2>
            </div>
        </template>

        <div class="max-w-4xl mx-auto">
            <div class="glass-panel p-8">

                <form @submit.prevent="submit" class="space-y-6">

                    <div v-if="Object.keys(form.errors).length" class="mb-4 p-3 bg-red-900/30 rounded">
                        <ul class="text-sm text-red-200 list-disc list-inside">
                            <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                        </ul>
                    </div>

                    <div class="border-b border-white/10 pb-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span class="bg-blue-500 w-2 h-2 rounded-full"></span> Identity
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Item Name *</label>
                                <input v-model="form.name" type="text" placeholder="e.g. Glucose Reagent" class="w-full glass-input" required />
                                <div v-if="form.errors.name" class="text-red-400 text-xs">{{ form.errors.name }}</div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Brand / Manufacturer</label>
                                <input v-model="form.brand" type="text" placeholder="e.g. Roche, Spinreact" class="w-full glass-input" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Category *</label>
                                <select v-model="form.category_id" class="w-full glass-input [&>option]:bg-gray-800" required>
                                    <option value="" disabled>Select Category...</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Supplier (Optional)</label>
                                <select v-model="form.supplier_id" class="w-full glass-input [&>option]:bg-gray-800">
                                    <option value="" disabled>Select Supplier...</option>
                                    <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.name }}</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Initial Lot Number</label>
                                <input v-model="form.lot_number" type="text" placeholder="Batch/Lot #" class="w-full glass-input" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-300">Packaging Type</label>
                            <select v-model="form.packaging_type" class="w-56 mt-2 glass-input [&>option]:bg-gray-800">
                                <option value="">Select Packaging...</option>
                                <option value="single">Single Item</option>
                                <option value="box">Box (contains kits)</option>
                                <option value="kit">Kit</option>
                                <option value="bottle">Bottle</option>
                                <option value="reagent">Reagent</option>
                            </select>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <div v-if="form.packaging_type === 'box'" class="space-y-2">
                                    <label class="text-sm font-medium text-gray-300">Kits per Box</label>
                                    <input v-model.number="form.kits_per_box" type="number" min="1" class="w-full glass-input" />
                                    <p class="text-xs text-gray-500">How many kits are inside one box?</p>
                                </div>

                                <div v-if="form.packaging_type === 'kit'" class="space-y-2">
                                    <label class="text-sm font-medium text-gray-300">Tests per Kit</label>
                                    <input v-model.number="form.tests_per_kit" type="number" min="1" class="w-full glass-input" />
                                    <p class="text-xs text-gray-500">How many tests does one kit provide?</p>
                                </div>

                                <div v-if="form.packaging_type === 'bottle' || form.packaging_type === 'reagent'" class="space-y-2">
                                    <label class="text-sm font-medium text-gray-300">Tests per Bottle / Reagent</label>
                                    <input v-model.number="form.tests_per_bottle" type="number" min="1" class="w-full glass-input" />
                                    <p class="text-xs text-gray-500">How many tests can be performed from one bottle/reagent?</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-white/10 pb-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span class="bg-purple-500 w-2 h-2 rounded-full"></span> Inventory Data
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Quantity *</label>
                                <input v-model="form.quantity" type="number" class="w-full glass-input" required min="0" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Unit</label>
                                <div class="text-sm text-gray-400">Unit is auto-set from Packaging Type: <span class="font-medium text-white">box/kit → kit, reagent → bottle</span>.</div>
                                <div v-if="false" class="flex gap-2">
                                    <!-- kept for dev: hidden editable unit if needed -->
                                    <select v-model="form.unit" class="w-full glass-input">
                                        <option v-for="u in availableUnits" :key="u" :value="u">{{ u }}</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input v-if="form.unit === 'other'" v-model="form.unit_other" type="text" placeholder="Custom unit" class="w-40 glass-input" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Min Threshold</label>
                                <div class="flex gap-2">
                                    <input v-model.number="form.min_threshold" type="number" class="w-full glass-input" min="0" />
                                    <select v-model="form.min_threshold_unit" class="w-36 glass-input">
                                        <option value="unit">Unit</option>
                                        <option value="kit">Kit</option>
                                        <option value="box">Box</option>
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500">Set threshold and whether it's measured in boxes or kits.</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Expiry Date</label>
                                <input v-model="form.expiry_date" type="date" class="w-full glass-input [color-scheme:dark]" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Storage Location</label>
                                <div class="flex gap-2">
                                    <select v-model="form.storage_location" class="w-full glass-input">
                                        <option v-for="loc in availableLocations" :key="loc" :value="loc">{{ loc }}</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input v-if="form.storage_location === 'other'" v-model="form.storage_location_other" type="text" placeholder="Custom location" class="w-40 glass-input" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-white/10 pb-6 mb-6">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span class="bg-yellow-500 w-2 h-2 rounded-full"></span> Financials
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Cost Price (Per Unit)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">$</span>
                                    <input v-model="form.cost_per_unit" type="number" step="0.01" placeholder="0.00" class="w-full glass-input pl-8" />
                                </div>
                                <p class="text-[10px] text-gray-500">The purchase cost for a single unit.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Selling Price (Optional)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">$</span>
                                    <input v-model="form.selling_price" type="number" step="0.01" placeholder="0.00" class="w-full glass-input pl-8" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pb-2">
                        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                            <span class="bg-green-500 w-2 h-2 rounded-full"></span> Scientific Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Sample Type</label>
                                <div class="flex gap-2">
                                    <select v-model="form.sample_type" class="w-full glass-input">
                                        <option value="">Select sample type...</option>
                                        <option v-for="s in availableSampleTypes" :key="s" :value="s">{{ s }}</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <input v-if="form.sample_type === 'other'" v-model="form.sample_type_other" type="text" placeholder="Custom sample type" class="w-40 glass-input" />
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Normal Range</label>
                                <input v-model="form.normal_range" type="text" placeholder="70-110 mg/dL" class="w-full glass-input" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-300">Std. Concentration</label>
                                <input v-model="form.standard_concentration" type="text" placeholder="100 mg/dL" class="w-full glass-input" />
                            </div>
                        </div>

                        <div class="mt-6 p-4 rounded-xl border border-dashed border-white/20 hover:border-blue-500/50 transition bg-white/5">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Upload Leaflet / Insert (PDF or Image)</label>
                            <input type="file" @input="form.leaflet = $event.target.files[0]" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer" />
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full h-1 mt-2"></progress>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-6 border-t border-white/10">
                        <Link :href="route('items.index')" class="px-6 py-3 rounded-xl hover:bg-white/5 text-gray-300 transition">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-105">
                            {{ form.processing ? 'Saving...' : 'Save Item' }}
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
import { ArrowLeft as ArrowLeftIcon } from 'lucide-vue-next';
import { watch, computed } from 'vue';

// Props
const props = defineProps({
    categories: Array,
    suppliers: Array,
    lab: Object
});

const form = useForm({
    // Identity
    name: '',
    brand: '',
    category_id: '',
    supplier_id: '',
    sku: '',
    lot_number: '',

    // Inventory
    unit: (props.lab?.prebuilt_units && props.lab.prebuilt_units.length) ? props.lab.prebuilt_units[0] : 'kit',
    quantity: 0,
    min_threshold: 5,
    min_threshold_unit: 'unit',
    expiry_date: '',
    storage_location: (props.lab?.storage_locations && props.lab.storage_locations.length) ? props.lab.storage_locations[0] : 'Fridge 1',
    packaging_type: '',
    kits_per_box: null,
    tests_per_kit: null,
    tests_per_bottle: null,

    // Financials
    cost_per_unit: '',
    selling_price: '',

    // Scientific
    normal_range: '',
    standard_concentration: '',
    sample_type: '',
    sample_type_other: '',
    unit_other: '',
    storage_location_other: '',
    leaflet: null
});

// Fallback presets when lab record has no presets
const fallbackUnits = ['box','kit','bottle','reagent','single'];
const fallbackLocations = ['Fridge 1','Fridge 2','Shelf 1','Shelf 2'];
const fallbackSampleTypes = ['serum','plasma','whole blood','urine','stool','csf','swab','biopsy','other'];

const availableUnits = computed(() => (props.lab && props.lab.prebuilt_units && props.lab.prebuilt_units.length) ? props.lab.prebuilt_units : fallbackUnits);
const availableLocations = computed(() => (props.lab && props.lab.storage_locations && props.lab.storage_locations.length) ? props.lab.storage_locations : fallbackLocations);
const availableSampleTypes = computed(() => (props.lab && props.lab.sample_types && props.lab.sample_types.length) ? props.lab.sample_types : fallbackSampleTypes);

// Auto-update unit when packaging_type changes
watch(() => form.packaging_type, (val) => {
    if (!val) return;
    // Map packaging to base unit: boxes/kits -> kit, reagent/bottle -> bottle
    const map = { box: 'kit', kit: 'kit', bottle: 'bottle', reagent: 'bottle', single: 'single' };
    const mapped = map[val] || val;
    if (!form.unit || availableUnits.value.indexOf(form.unit) !== -1) {
        form.unit = availableUnits.value.includes(mapped) ? mapped : availableUnits.value[0];
    }
});

const submit = () => {
    form.post(route('items.store'), {
        onError: () => {
            // simple visual cue handled by error block above
        }
    });
};
</script>
