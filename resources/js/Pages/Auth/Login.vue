<template>
    <Head title="Log in" />

    <div class="relative min-h-screen flex items-center justify-center bg-black overflow-hidden font-sans">

        <div class="absolute inset-0 z-0 bg-[#000000]">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[15vw] h-[70vh] bg-white rounded-full blur-[90px] opacity-90 mix-blend-screen"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40vw] h-[120vh] bg-amber-200/30 rounded-full blur-[120px] mix-blend-screen animate-pulse-slow"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[70vw] h-[100vh] bg-blue-500/10 rounded-full blur-[150px] mix-blend-screen"></div>
        </div>

        <div class="absolute inset-0 flex justify-between z-0 pointer-events-none">
            <div class="w-[42%] h-full bg-[#08080a] border-r border-white/5 shadow-[30px_0_80px_rgba(0,0,0,1)] flex items-center justify-end overflow-hidden relative">
                <div class="absolute right-8 top-10 bottom-10 w-24 border border-white/5 rounded-2xl opacity-20 shadow-inner"></div>
                <div class="absolute right-40 top-10 bottom-10 w-48 border border-white/5 rounded-2xl opacity-10"></div>
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
            </div>

            <div class="w-[42%] h-full bg-[#08080a] border-l border-white/5 shadow-[-30px_0_80px_rgba(0,0,0,1)] flex items-center justify-start overflow-hidden relative">
                 <div class="absolute left-8 top-10 bottom-10 w-24 border border-white/5 rounded-2xl opacity-20 shadow-inner"></div>
                 <div class="absolute left-40 top-10 bottom-10 w-48 border border-white/5 rounded-2xl opacity-10"></div>
                 <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-transparent via-white/20 to-transparent"></div>
            </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center z-0 pointer-events-none mix-blend-screen opacity-60">
            <div class="w-[200%] h-[15vh] bg-gradient-to-r from-transparent via-white/20 to-transparent blur-[40px] transform rotate-[20deg]"></div>
            <div class="absolute w-[200%] h-[15vh] bg-gradient-to-r from-transparent via-white/20 to-transparent blur-[40px] transform -rotate-[20deg]"></div>
        </div>

        <div class="relative z-10 w-full max-w-[420px] px-6 sm:px-0">

            <div v-if="status" class="mb-6 font-medium text-sm text-green-400 text-center bg-green-500/10 py-3 rounded-xl border border-green-500/20 backdrop-blur-md animate-fade-in-down">
                {{ status }}
            </div>

            <div class="glass-form p-8 sm:p-10 rounded-[2.5rem] relative overflow-hidden animate-fade-in-up">

                <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/10 to-transparent pointer-events-none rounded-t-[2.5rem]"></div>
                <div class="absolute inset-0 border-[1.5px] border-white/20 rounded-[2.5rem] pointer-events-none mix-blend-overlay"></div>

                <div class="text-center mb-10 relative z-20">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/10 border border-white/20 mb-6 shadow-[0_0_40px_rgba(255,255,255,0.2)] backdrop-blur-md transform transition-transform hover:scale-105">
                        <activity-icon class="w-10 h-10 text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.8)]" />
                    </div>
                    <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-300 to-white tracking-widest uppercase drop-shadow-sm">Nexus Lab</h1>
                    <p class="text-white/60 text-xs font-bold mt-3 tracking-[0.3em] uppercase">Enter The Gateway</p>
                </div>

                <form @submit.prevent="submit" class="relative z-20">
                    <div class="mb-5">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <mail-icon class="w-5 h-5 text-white/50 group-focus-within:text-white transition-colors" />
                            </div>
                            <input v-model="form.email" type="email" id="email" class="w-full bg-black/30 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-xl shadow-inner text-sm font-medium" required autofocus autocomplete="username" placeholder="Email Address" />
                        </div>
                        <div v-if="form.errors.email" class="text-red-400 text-xs mt-2 px-2 font-medium">{{ form.errors.email }}</div>
                    </div>

                    <div class="mb-6">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <lock-icon class="w-5 h-5 text-white/50 group-focus-within:text-white transition-colors" />
                            </div>
                            <input v-model="form.password" type="password" id="password" class="w-full bg-black/30 border border-white/10 rounded-2xl pl-12 pr-4 py-4 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all backdrop-blur-xl shadow-inner text-sm font-medium" required autocomplete="current-password" placeholder="Password" />
                        </div>
                        <div v-if="form.errors.password" class="text-red-400 text-xs mt-2 px-2 font-medium">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center mb-8 px-1">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 mr-2">
                                <input type="checkbox" v-model="form.remember" class="peer appearance-none w-5 h-5 border border-white/30 rounded-md bg-black/40 checked:bg-white/20 checked:border-white/60 transition-all cursor-pointer backdrop-blur-sm" />
                                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-white/60 group-hover:text-white/90 transition-colors uppercase tracking-wider">Remember me</span>
                        </label>
                    </div>

                    <button :disabled="form.processing" type="submit" class="w-full bg-white text-black font-black tracking-widest text-sm uppercase py-4 rounded-2xl hover:bg-gray-200 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-[0_0_25px_rgba(255,255,255,0.4)] flex justify-center items-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span v-if="form.processing" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Authenticating...
                        </span>
                        <span v-else>Enter System</span>
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center animate-fade-in-up" style="animation-delay: 0.2s;">
                <p class="text-white/30 text-[10px] uppercase font-bold tracking-[0.2em]">© {{ new Date().getFullYear() }} Nexus Laboratory System</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Mail as MailIcon, Lock as LockIcon, Activity as ActivityIcon } from 'lucide-vue-next';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<style scoped>
.glass-form {
    background: rgba(10, 10, 10, 0.4);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.6), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(28px);
}
.animate-fade-in-up { animation: fadeInUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.animate-fade-in-down { animation: fadeInDown 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-pulse-slow { animation: pulseSlow 4s ease-in-out infinite alternate; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulseSlow { from { opacity: 0.2; transform: translate(-50%, -50%) scale(0.95); } to { opacity: 0.5; transform: translate(-50%, -50%) scale(1.05); } }
</style>
