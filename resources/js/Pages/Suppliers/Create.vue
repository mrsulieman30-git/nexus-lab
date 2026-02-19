<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Add New Supplier</h2>
        </template>

        <div class="max-w-2xl mx-auto">
            <div class="glass-panel p-8">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="text-sm font-bold text-gray-400 uppercase tracking-wider">Company Name *</label>
                        <input v-model="form.name" type="text" class="w-full glass-input mt-2" placeholder="e.g. MedLab Supplies Ltd." required />
                        <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="text-sm font-bold text-gray-400 uppercase tracking-wider">Sales Representative / Contact Person</label>
                        <input v-model="form.contact_person" type="text" class="w-full glass-input mt-2" placeholder="e.g. John Smith" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-bold text-gray-400 uppercase tracking-wider">Phone Number</label>
                            <input v-model="form.phone" type="text" class="w-full glass-input mt-2" placeholder="+123..." />
                        </div>
                        <div>
                            <label class="text-sm font-bold text-gray-400 uppercase tracking-wider">Email Address</label>
                            <input v-model="form.email" type="email" class="w-full glass-input mt-2" placeholder="sales@company.com" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-bold text-gray-400 uppercase tracking-wider">Physical Address</label>
                        <textarea v-model="form.address" rows="3" class="w-full glass-input mt-2" placeholder="Street, City, Country"></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-white/10">
                        <Link :href="route('suppliers.index')" class="px-6 py-2 text-gray-400 hover:text-white transition mr-4">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="px-8 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg transition">
                            Save Supplier
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

const form = useForm({
    name: '',
    contact_person: '',
    email: '',
    phone: '',
    address: ''
});

const submit = () => {
    form.post(route('suppliers.store'));
};
</script>
