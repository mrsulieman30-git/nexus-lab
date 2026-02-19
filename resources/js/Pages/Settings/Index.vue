<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Lab Configuration
            </h2>
        </template>

        <div class="max-w-4xl mx-auto space-y-8">

            <div v-if="$page.props.flash?.message" class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2">
                <check-circle-icon class="w-5 h-5" />
                {{ $page.props.flash.message }}
            </div>

            <div class="glass-panel p-8">
                <div class="flex items-center gap-3 mb-6 border-b border-white/10 pb-4">
                    <div class="p-2 bg-blue-600 rounded-lg">
                        <building-icon class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">General Information</h3>
                        <p class="text-xs text-gray-400">Customize your lab's identity for reports and invoices.</p>
                    </div>
                </div>

                <form @submit.prevent="updateLab" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="col-span-2 flex items-center gap-6 mb-4">
                        <div class="w-24 h-24 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden relative group">
                            <img v-if="photoPreview || lab.logo_url" :src="photoPreview || lab.logo_url" class="w-full h-full object-cover" />
                            <div v-else class="text-gray-500 text-xs text-center px-2">No Logo</div>

                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer" @click="selectNewPhoto">
                                <camera-icon class="w-6 h-6 text-white" />
                            </div>
                        </div>

                        <div>
                            <h4 class="text-white font-bold text-sm">Lab Logo</h4>
                            <p class="text-gray-400 text-xs mb-3">Recommended size: 200x200px (PNG/JPG)</p>
                            <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview" accept="image/*" />
                            <button type="button" @click="selectNewPhoto" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition">
                                Upload New Logo
                            </button>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-300">Laboratory Name</label>
                        <input v-model="labForm.name" type="text" class="w-full glass-input mt-1" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Official Website</label>
                        <input v-model="labForm.website" type="url" placeholder="https://" class="w-full glass-input mt-1" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Phone Number</label>
                        <input v-model="labForm.phone" type="text" class="w-full glass-input mt-1" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Financial Year Start</label>
                        <input v-model="labForm.financial_year_start" type="date" class="w-full glass-input mt-1 [color-scheme:dark]" />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Official Email</label>
                        <input v-model="labForm.email" type="email" class="w-full glass-input mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-300">Address / Location</label>
                        <textarea v-model="labForm.address" rows="2" class="w-full glass-input mt-1"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-300">Prebuilt Storage Locations</label>
                        <input v-model="labForm.storage_locations_raw" type="text" placeholder="Fridge A, Shelf 1, Shelf 2" class="w-full glass-input mt-1" />
                        <p class="text-xs text-gray-500 mt-1">Comma-separated list of common storage locations.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Prebuilt Units</label>
                        <input v-model="labForm.prebuilt_units_raw" type="text" placeholder="kit, box, bottle" class="w-full glass-input mt-1" />
                        <p class="text-xs text-gray-500 mt-1">Comma-separated list used in item creation unit dropdowns.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-300">Sample Types</label>
                        <input v-model="labForm.sample_types_raw" type="text" placeholder="serum, plasma, urine, other" class="w-full glass-input mt-1" />
                        <p class="text-xs text-gray-500 mt-1">Comma-separated common sample types. Include 'other' if needed.</p>
                    </div>

                    <div class="col-span-2 flex justify-end">
                        <button type="submit" :disabled="labForm.processing" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg transition shadow-lg">
                            {{ labForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="glass-panel p-8">
                <div class="flex items-center gap-3 mb-6 border-b border-white/10 pb-4">
                    <div class="p-2 bg-purple-600 rounded-lg">
                        <tags-icon class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Category Management</h3>
                        <p class="text-xs text-gray-400">Organize your inventory (e.g., Hematology, Serology).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-1 bg-white/5 p-4 rounded-xl border border-white/5 h-fit">
                        <h4 class="text-sm font-bold text-white mb-3">Add New Category</h4>
                        <form @submit.prevent="addCategory" class="space-y-3">
                            <input v-model="catForm.name" type="text" placeholder="e.g. Microbiology" class="w-full glass-input text-sm" required />
                            <button type="submit" :disabled="catForm.processing" class="w-full py-2 bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold rounded-lg transition">
                                + Add Category
                            </button>
                        </form>
                    </div>

                    <div class="md:col-span-2 space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        <div v-for="cat in categories" :key="cat.id" class="flex items-center justify-between p-3 bg-white/5 rounded-lg border border-white/5 group hover:bg-white/10 transition">
                            <div class="flex items-center gap-3">
                                <input type="color" v-model="cat.color_hex" @change="updateCategoryColor(cat)" class="w-8 h-8 rounded border-none cursor-pointer" />
                                <span class="text-gray-300 font-medium">{{ cat.name }}</span>
                            </div>
                            <button @click="deleteCategory(cat)" class="text-gray-500 hover:text-red-400 transition opacity-50 group-hover:opacity-100 p-2">
                                <trash-icon class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    Building as BuildingIcon, Tags as TagsIcon, CheckCircle as CheckCircleIcon,
    Trash as TrashIcon, Camera as CameraIcon
} from 'lucide-vue-next';

const props = defineProps({ lab: Object, categories: Array });

// Lab Settings Form
const labForm = useForm({
    _method: 'PUT', // Needed for file upload via Inertia
    name: props.lab.name,
    email: props.lab.email || '',
    website: props.lab.website || '',
    phone: props.lab.phone || '',
    address: props.lab.address || '',
    financial_year_start: props.lab.financial_year_start || '2026-01-01',
    logo: null,
    // Presets as raw comma-separated strings for editing
    storage_locations_raw: (props.lab.storage_locations || []).join(', '),
    prebuilt_units_raw: (props.lab.prebuilt_units || []).join(', '),
    sample_types_raw: (props.lab.sample_types || []).join(', '),
});


// Photo Handling
const photoInput = ref(null);
const photoPreview = ref(null);

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    labForm.logo = photo;
    const reader = new FileReader();
    reader.onload = (e) => { photoPreview.value = e.target.result; };
    reader.readAsDataURL(photo);
};

const updateLab = () => {
    // We post to update endpoint because we are sending a file
    labForm.post(route('settings.update'), {
        preserveScroll: true,
    });
};

// Category Form (Standard)
const catForm = useForm({ name: '' });
const addCategory = () => {
    catForm.post(route('categories.store'), { onSuccess: () => catForm.reset() });
};
const updateCategoryColor = (cat) => {
    router.patch(route('categories.update', cat.id), { color_hex: cat.color_hex });
};
</script>
