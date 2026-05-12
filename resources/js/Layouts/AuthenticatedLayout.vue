<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const isDark = ref(false);

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
        <!-- Navigation Premium FestiVault -->
        <nav class="bg-white dark:bg-slate-900 border-b border-amber-100/50 dark:border-slate-800 shadow-sm sticky top-0 z-40 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <!-- Logo FestiVault -->
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-fuchsia-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-fuchsia-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L2 12l6.857-2.143L11 3z"/></svg>
                            </div>
                            <span class="text-xl font-black text-slate-800 dark:text-white tracking-tighter">FESTI<span class="text-fuchsia-600">VAULT</span></span>
                        </div>
                        
                        <!-- Navigation principale -->
                        <div class="hidden md:flex md:ml-10 space-x-1">
                            <Link :href="route('dashboard')" :class="route().current('dashboard') ? 'bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400' : 'text-slate-500 dark:text-slate-400 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Tableau de bord
                            </Link>
                            <Link :href="route('events.index')" :class="route().current('events.index') ? 'bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400' : 'text-slate-500 dark:text-slate-400 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Événements
                            </Link>
                            <Link :href="route('members.index')" :class="route().current('members.*') ? 'bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400' : 'text-slate-500 dark:text-slate-400 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Membres
                            </Link>
                            <Link :href="route('welcome')" :class="route().current('welcome') ? 'bg-fuchsia-50 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-400' : 'text-slate-500 dark:text-slate-400 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 hover:bg-slate-50 dark:hover:bg-slate-800'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Catalogue
                            </Link>
                            <Link :href="route('pricing')" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-fuchsia-600 dark:hover:text-fuchsia-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                Tarification
                            </Link>
                        </div>
                    </div>

                    <!-- Dropdown Utilisateur & Notifications & Dark Mode -->
                    <div class="flex items-center gap-2 md:gap-4">
                        <!-- Notifications -->
                        <Dropdown align="right" width="80">
                            <template #trigger>
                                <button class="relative p-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <span v-if="$page.props.auth.user.unread_notifications_count > 0" 
                                          class="absolute top-2 right-2 flex h-3 w-3">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white dark:border-slate-900"></span>
                                    </span>
                                </button>
                            </template>
                            <template #content>
                                <div class="w-80 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                                        <span class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Notifications</span>
                                        <Link v-if="$page.props.auth.user.unread_notifications_count > 0" 
                                              :href="route('notifications.markAllAsRead')" method="post" as="button"
                                              class="text-[10px] font-bold text-fuchsia-600 hover:text-fuchsia-700">Tout marquer comme lu</Link>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <div v-if="$page.props.auth.user.unread_notifications.length === 0" class="p-8 text-center">
                                            <p class="text-xs text-slate-400 font-medium">Aucune nouvelle notification</p>
                                        </div>
                                        <div v-else v-for="notification in $page.props.auth.user.unread_notifications" :key="notification.id" 
                                             class="p-4 border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <p class="text-sm text-slate-800 dark:text-slate-200 leading-tight mb-1">{{ notification.data.message }}</p>
                                            <span class="text-[10px] text-slate-400">{{ new Date(notification.created_at).toLocaleDateString() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>

                        <button @click="toggleDarkMode" class="p-2.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-amber-400 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition shadow-sm">
                            <svg v-if="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>

                        <span class="text-sm font-medium text-slate-600 dark:text-slate-400 hidden lg:block">
                            Bienvenue, {{ $page.props.auth.user.name }}
                        </span>
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-fuchsia-600 focus:outline-none transition">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-fuchsia-500 to-amber-500 flex items-center justify-center text-white font-black shadow-md">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')" class="dark:hover:bg-slate-800 dark:text-slate-300">Profil</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="dark:hover:bg-rose-900/30 text-rose-600">Déconnexion</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- En-tête de la page -->
        <header v-if="$slots.header" class="bg-white dark:bg-slate-900 border-b border-amber-50/50 dark:border-slate-800 shadow-sm transition-colors duration-300">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Contenu Principal -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>