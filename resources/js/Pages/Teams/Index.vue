<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">
                Staff Management
            </h2>
        </template>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1">
                <div class="glass-panel p-6">
                    <div class="flex items-center gap-3 mb-6 border-b border-white/10 pb-4">
                        <div class="p-2 bg-blue-600 rounded-lg">
                            <user-plus-icon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Add New User</h3>
                            <p class="text-xs text-gray-400">Create account for staff.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Full Name</label>
                            <input v-model="form.name" type="text" class="w-full glass-input mt-1" required />
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Email Address</label>
                            <input v-model="form.email" type="email" class="w-full glass-input mt-1" required />
                            <div v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Role</label>
                            <select v-model="form.role" class="w-full glass-input mt-1 [&>option]:bg-gray-800">
                                    <option value="lab_tech">Lab Technician (Limited Access)</option>
                                    <option value="lab_manager">Lab Manager </option>
                                    <option value="admin">Admin (Full Access)</option>
                                </select>
                                <p class="text-[10px] text-gray-500 mt-1" v-if="form.role === 'lab_tech'">
                                    * Can only add/dispense items; settings and user mgmt are hidden.
                                </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Password</label>
                                <input v-model="form.password" type="password" class="w-full glass-input mt-1" required />
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Confirm</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full glass-input mt-1" required />
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg transition mt-2">
                            {{ form.processing ? 'Creating...' : 'Create Account' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="glass-panel p-6">
                    <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                        <users-icon class="w-5 h-5 text-gray-400" /> Active Staff Members
                    </h3>

                    <div class="space-y-3">
                        <div v-for="user in users" :key="user.id" class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/5 group hover:bg-white/10 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center font-bold text-white border-2 border-gray-600">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-white font-bold">{{ user.name }}</p>
                                    <p class="text-xs text-gray-400">{{ user.email }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                      :class="user.role === 'lab_manager' ? 'bg-purple-500/20 text-purple-300' : 'bg-blue-500/20 text-blue-300'">
                                    {{ user.role.replace('_', ' ') }}
                                </span>

                                <button @click="deleteUser(user)" class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition opacity-0 group-hover:opacity-100">
                                    <trash-icon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <div v-if="users.length === 0" class="text-center py-12 border-2 border-dashed border-white/10 rounded-xl text-gray-500">
                            No other staff members added yet.
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
import { Users as UsersIcon, UserPlus as UserPlusIcon, Trash as TrashIcon } from 'lucide-vue-next';

const props = defineProps({ users: Array });

const form = useForm({
    name: '',
    email: '',
    role: 'technologist',
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('team.store'), {
        onSuccess: () => form.reset()
    });
};

const deleteUser = (user) => {
    if(confirm('Are you sure you want to remove this user? They will lose access immediately.')) {
        router.delete(route('team.destroy', user.id));
    }
};
</script>
