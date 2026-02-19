<template>
  <div class="min-h-screen w-full bg-[#0f172a] text-white font-sans selection:bg-blue-500/30">

    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f172a] via-[#1e293b] to-[#0f172a]"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 flex h-screen overflow-hidden">

    <aside class="hidden sm:flex sm:flex-col sm:shrink-0 sm:mx-4 sm:mr-0 sm:rounded-3xl sm:border sm:border-white/10 sm:bg-[#1e293b]/80 sm:backdrop-blur-md sm:shadow-xl sm:transition-all sm:duration-300 sm:w-48 md:w-56 lg:w-72">

        <div class="p-8 flex items-center gap-3 border-b border-white/5">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center shadow-lg shadow-blue-500/20 overflow-hidden">
             <img v-if="$page.props.auth.user.laboratory?.logo_url"
                  :src="$page.props.auth.user.laboratory.logo_url"
                  class="w-full h-full object-cover" />
             <flask-conical-icon v-else class="w-6 h-6 text-white" />
          </div>

          <div class="overflow-hidden">
            <h1 class="text-xl font-bold tracking-tight text-white truncate">
                {{ $page.props.auth.user.laboratory?.name || 'NEXUS LAB' }}
            </h1>
            <p class="text-[10px] uppercase tracking-wider text-blue-300 font-semibold">Inventory v1.0</p>
          </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2 custom-scrollbar">

          <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Main Menu</p>

          <Link :href="route('dashboard')"
             class="nav-item"
             :class="route().current('dashboard') ? 'active' : 'inactive'">
             <layout-dashboard-icon class="w-5 h-5" />
             <span>Dashboard</span>
          </Link>

          <Link :href="route('items.index')"
             class="nav-item"
             :class="route().current('items.*') ? 'active' : 'inactive'">
             <package-search-icon class="w-5 h-5" />
             <span>Inventory Items</span>
          </Link>

          <Link :href="route('purchase-orders.index')"
             class="nav-item"
             :class="route().current('purchase-orders.*') ? 'active' : 'inactive'">
             <shopping-cart-icon class="w-5 h-5" />
             <span>Restock / Orders</span>
          </Link>

          <Link :href="route('reports.index')"
             class="nav-item"
             :class="route().current('reports.*') ? 'active' : 'inactive'">
             <file-bar-chart-icon class="w-5 h-5" />
             <span>Reports & Analytics</span>
          </Link>

             <Link :href="route('suppliers.index')"
                 class="nav-item"
                 :class="route().current('suppliers.*') ? 'active' : 'inactive'">
                 <truck-icon class="w-5 h-5" />
                 <span>Suppliers</span>
             </Link>

          <div v-if="$page.props.auth.user.role === 'lab_manager' || $page.props.auth.user.role === 'super_admin' || $page.props.auth.user.role === 'admin'">

             <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Admin Controls</p>



                <Link :href="route('team.index')"
                 class="nav-item"
                 :class="route().current('team.*') ? 'active' : 'inactive'">
                 <users-icon class="w-5 h-5" />
                 <span>Staff Management</span>
             </Link>

                         <Link :href="route('financial.index')"
                                    class="nav-item"
                                    :class="route().current('financial.*') ? 'active' : 'inactive'">
                                    <dollar-sign-icon class="w-5 h-5" />
                                    <span>Financials</span>
                            </Link>

             <Link :href="route('settings.index')"
                 class="nav-item"
                 :class="route().current('settings.*') ? 'active' : 'inactive'">
                 <settings-icon class="w-5 h-5" />
                 <span>Lab Settings</span>
             </Link>
           </div>

        </nav>

        <div class="p-4 border-t border-white/5">
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition cursor-pointer border border-white/5">
                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center border-2 border-gray-600 font-bold text-sm overflow-hidden text-white">
                    <img v-if="$page.props.auth.user.profile_photo_url" :src="$page.props.auth.user.profile_photo_url" class="w-full h-full object-cover" />
                    <span v-else>{{ $page.props.auth.user.name.charAt(0) }}</span>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</p>
                    <p class="text-xs text-gray-400 truncate capitalize">{{ $page.props.auth.user.role.replace('_', ' ') }}</p>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="ml-auto text-gray-400 hover:text-red-400 transition" title="Logout">
                    <log-out-icon class="w-5 h-5" />
                </Link>
            </div>
        </div>
      </aside>

      <main class="flex-1 flex flex-col min-w-0 overflow-hidden relative">

        <header class="h-20 flex items-center justify-between px-8 py-4">
            <h2 class="text-2xl font-semibold text-white tracking-tight">
                <slot name="header" />
            </h2>
             <div class="flex items-center gap-4">
                <div class="relative group">
                    <input type="text" placeholder="Search..."
                           class="w-64 bg-white/5 border border-white/10 rounded-full py-2.5 pl-10 pr-4 text-sm text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-gray-500" />
                    <search-icon class="w-4 h-4 text-gray-500 absolute left-3.5 top-3" />
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto px-8 pb-8 custom-scrollbar">
            <slot />
        </div>

        <div class="border-t border-white/5 p-4 text-center select-none">
            <p class="text-[10px] uppercase tracking-widest text-gray-600 font-bold hover:text-blue-500 transition-colors cursor-default">
                &copy; 2026 Suliemani ICT Solutions. All Rights Reserved.
            </p>
        </div>

      </main>

    </div>
  </div>
</template>

<script setup>
import {
    LayoutDashboard as LayoutDashboardIcon,
    FlaskConical as FlaskConicalIcon,
    PackageSearch as PackageSearchIcon,
    Users as UsersIcon,
    Settings as SettingsIcon,
    Search as SearchIcon,
    FileBarChart as FileBarChartIcon,
    DollarSign as DollarSignIcon,
    LogOut as LogOutIcon,
    Truck as TruckIcon,
    ShoppingCart as ShoppingCartIcon
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
/* Navigation Item Styles */
.nav-item {
    @apply flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition-colors duration-200;
}
.active {
    @apply bg-blue-600 text-white shadow-lg shadow-blue-900/50;
}
.inactive {
    @apply text-gray-400 hover:bg-white/5 hover:text-white;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
</style>
