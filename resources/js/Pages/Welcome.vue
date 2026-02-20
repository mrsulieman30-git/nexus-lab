<template>
    <Head title="Welcome to Nexus Lab" />

    <div class="min-h-screen bg-[#050505] text-gray-200 font-sans selection:bg-blue-500/30 selection:text-blue-200 overflow-hidden relative">

        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[50vw] h-[50vw] bg-blue-600/20 rounded-full blur-[120px] mix-blend-screen animate-pulse-slow"></div>
            <div class="absolute top-[20%] -right-[20%] w-[60vw] h-[60vw] bg-purple-600/10 rounded-full blur-[150px] mix-blend-screen"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-30"></div>
        </div>

        <nav class="fixed top-0 inset-x-0 z-50 glass-nav border-b border-white/5">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 p-[1px] shadow-[0_0_20px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_30px_rgba(59,130,246,0.5)] transition-shadow">
                        <div class="w-full h-full bg-[#0a0a0a] rounded-xl flex items-center justify-center">
                            <activity-icon class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <span class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400 tracking-widest uppercase">Nexus Lab</span>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm bg-white text-black hover:bg-gray-200 transition-all transform hover:scale-105"
                    >
                        Enter Dashboard <arrow-right-icon class="w-4 h-4 ml-2" />
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm bg-blue-600 hover:bg-blue-500 text-white transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(59,130,246,0.4)] border border-blue-400/30"
                    >
                        Log in
                    </Link>
                </div>
            </div>
        </nav>

        <main class="relative z-10 pt-40 pb-20 px-6 max-w-7xl mx-auto flex flex-col items-center justify-center min-h-screen text-center">

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-8 animate-fade-in-up">
                <zap-icon class="w-3.5 h-3.5" /> Next-Generation System
            </div>

            <h1 class="text-5xl sm:text-6xl md:text-7xl font-black text-white tracking-tight leading-[1.1] mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
                Precision Intelligence for <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-blue-400">Modern Laboratories.</span>
            </h1>

            <p class="mt-4 text-lg sm:text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed font-medium animate-fade-in-up" style="animation-delay: 0.2s;">
                Nexus Lab streamlines your inventory, automates procurement, and provides real-time financial insights. Built for speed, designed for accuracy, and engineered to scale with your healthcare infrastructure.
            </p>

            <div class="mt-12 flex items-center gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                <Link
                    :href="$page.props.auth.user ? route('dashboard') : route('login')"
                    class="px-8 py-4 rounded-2xl font-black text-base bg-white text-black hover:bg-gray-200 transition-all transform hover:scale-105 shadow-[0_0_30px_rgba(255,255,255,0.3)] flex items-center gap-2"
                >
                    Access System <arrow-right-icon class="w-5 h-5" />
                </Link>

                <a href="#features" class="px-8 py-4 rounded-2xl font-bold text-base bg-white/5 text-white hover:bg-white/10 border border-white/10 transition-all backdrop-blur-md">
                    Explore Features
                </a>
            </div>
        </main>

        <section id="features" class="relative z-10 py-24 bg-black/50 border-t border-white/5 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-black text-white mb-4">Engineered for Excellence</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto">Everything you need to manage your laboratory's operations from a single command center.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(feature, i) in features" :key="i" class="glass-card p-8 rounded-[2rem] border border-white/5 hover:border-blue-500/50 transition-all duration-300 group hover:-translate-y-2">
                        <div class="w-14 h-14 rounded-2xl bg-blue-500/10 flex items-center justify-center mb-6 border border-blue-500/20">
                            <component :is="feature.icon" class="w-7 h-7 text-blue-400" />
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">{{ feature.title }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ feature.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="relative z-10 border-t border-white/10 bg-[#050505] pt-12 pb-8">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <activity-icon class="w-5 h-5 text-gray-400" />
                    <span class="text-lg font-black text-white tracking-widest uppercase">Nexus Lab</span>
                </div>
                <p class="text-gray-500 text-sm font-medium">&copy; {{ new Date().getFullYear() }} Nexus Laboratory System. All rights reserved.</p>
                <div class="flex gap-4 text-sm font-medium text-gray-500">
                    <span>Laravel v{{ laravelVersion }}</span>
                    <span>PHP v{{ phpVersion }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    Activity as ActivityIcon, ArrowRight as ArrowRightIcon, Zap as ZapIcon,
    Database as DatabaseIcon, PieChart as PieChartIcon, ShoppingCart as ShoppingCartIcon, ShieldCheck as ShieldCheckIcon
} from 'lucide-vue-next';

defineProps({
    laravelVersion: String,
    phpVersion: String,
});

const features = [
    { title: 'Smart Inventory', icon: DatabaseIcon, desc: 'Automated tracking, FIFO batch management, and predictive low-stock alerts.' },
    { title: 'Financial Analytics', icon: PieChartIcon, desc: 'Real-time revenue tracking, expense categorization, and PDF reporting.' },
    { title: 'Procurement Hub', icon: ShoppingCartIcon, desc: 'Seamless purchase order generation and automated stock reconciliation.' },
    { title: 'Bank-Grade Security', icon: ShieldCheckIcon, desc: 'Strict role-based access control and end-to-end audit logging.' }
];
</script>

<style scoped>
.glass-nav { background: rgba(5, 5, 5, 0.7); backdrop-filter: blur(20px); }
.glass-card { background: linear-gradient(145deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%); }
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-pulse-slow { animation: pulseSlow 6s ease-in-out infinite alternate; }
@keyframes pulseSlow { from { opacity: 0.3; transform: scale(0.95); } to { opacity: 0.6; transform: scale(1.05); } }
html { scroll-behavior: smooth; }
</style>
