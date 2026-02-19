<template>
    <AppLayout>
        <template #header>
            Supplier Management
        </template>

        <div v-if="$page.props.flash?.message" class="mb-6 bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl flex items-center gap-2">
            <check-circle-icon class="w-5 h-5" />
            {{ $page.props.flash.message }}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-4">

                <div class="relative mb-4">
                    <search-icon class="absolute left-3 top-3 w-4 h-4 text-gray-500" />
                    <input v-model="search" type="text" placeholder="Search suppliers..." class="w-full glass-input pl-10" />
                </div>

                <div v-for="supplier in filteredSuppliers" :key="supplier.id" class="glass-panel p-5 flex justify-between items-start group hover:bg-white/5 transition">
                    <div>
                        <Link :href="route('suppliers.show', supplier.id)" class="font-bold text-white text-lg hover:text-blue-400 hover:underline transition flex items-center gap-2">
                            {{ supplier.name }}
                        </Link>

                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-400">
                            <div v-if="supplier.contact_person" class="flex items-center gap-1">
                                <user-icon class="w-3 h-3" /> {{ supplier.contact_person }}
                            </div>
                            <div v-if="supplier.phone" class="flex items-center gap-1">
                                <phone-icon class="w-3 h-3" /> {{ supplier.phone }}
                            </div>
                        </div>
                        <div v-if="supplier.email" class="mt-1 text-sm text-blue-400">
                            {{ supplier.email }}
                        </div>
                    </div>

                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                            <button v-if="$page.props.auth.user.role !== 'lab_tech'" @click="editSupplier(supplier)" class="p-2 rounded-lg hover:bg-blue-500/20 text-blue-400">
                                <pencil-icon class="w-4 h-4" />
                            </button>
                            <button v-if="$page.props.auth.user.role !== 'lab_tech'" @click="deleteSupplier(supplier)" class="p-2 rounded-lg hover:bg-red-500/20 text-red-400">
                                <trash-icon class="w-4 h-4" />
                            </button>
                        </div>
                </div>

                <div v-if="suppliers.length === 0" class="text-center py-10 text-gray-500 italic">
                    No suppliers added yet.
                </div>
            </div>

            <div class="glass-panel p-6 h-fit">
                <h3 class="text-lg font-bold text-white mb-4 border-b border-white/10 pb-4">
                    {{ isEditing ? 'Edit Supplier' : 'Add New Vendor' }}
                </h3>
                <div v-if="$page.props.auth.user.role !== 'lab_tech'">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Company Name *</label>
                        <input v-model="form.name" type="text" class="w-full glass-input mt-1" required placeholder="e.g. Roche Diagnostics" />
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Contact Person</label>
                        <input v-model="form.contact_person" type="text" class="w-full glass-input mt-1" placeholder="Sales Rep Name" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full glass-input mt-1" />
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Email</label>
                            <input v-model="form.email" type="email" class="w-full glass-input mt-1" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase">Address</label>
                        <textarea v-model="form.address" class="w-full glass-input mt-1" rows="2"></textarea>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button v-if="isEditing" type="button" @click="cancelEdit" class="flex-1 py-2 bg-gray-700 hover:bg-gray-600 rounded-xl text-white text-sm">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition">
                            {{ isEditing ? 'Update' : 'Save Supplier' }}
                        </button>
                    </div>
                </form>
                </div>
                <div v-else class="text-sm text-gray-400">You have view-only access to suppliers. Contact an administrator to add or modify vendors.</div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router, Link } from '@inertiajs/vue3'; // Added Link import
import { ref, computed } from 'vue';
import {
    Search as SearchIcon, User as UserIcon, Phone as PhoneIcon,
    Pencil as PencilIcon, Trash as TrashIcon, CheckCircle as CheckCircleIcon
} from 'lucide-vue-next';

const props = defineProps({ suppliers: Array });

const search = ref('');
const isEditing = ref(false);
const editId = ref(null);

const form = useForm({
    name: '',
    contact_person: '',
    email: '',
    phone: '',
    address: ''
});

const filteredSuppliers = computed(() => {
    if(!search.value) return props.suppliers;
    return props.suppliers.filter(s =>
        s.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('suppliers.update', editId.value), {
            onSuccess: () => cancelEdit()
        });
    } else {
        form.post(route('suppliers.store'), {
            onSuccess: () => form.reset()
        });
    }
};

const editSupplier = (s) => {
    isEditing.value = true;
    editId.value = s.id;
    form.name = s.name;
    form.contact_person = s.contact_person;
    form.email = s.email;
    form.phone = s.phone;
    form.address = s.address;
};

const cancelEdit = () => {
    isEditing.value = false;
    editId.value = null;
    form.reset();
};

const deleteSupplier = (s) => {
    if(confirm('Are you sure?')) {
        router.delete(route('suppliers.destroy', s.id));
    }
};
</script>
