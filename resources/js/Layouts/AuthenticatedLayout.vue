<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Navigation Premium FestiVault -->
        <nav class="bg-white border-b border-amber-100/50 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <!-- Logo FestiVault -->
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-fuchsia-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-fuchsia-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L2 12l6.857-2.143L11 3z"/></svg>
                            </div>
                            <span class="text-xl font-black text-slate-800 tracking-tighter">FESTI<span class="text-fuchsia-600">VAULT</span></span>
                        </div>
                        
                        <!-- Navigation principale -->
                        <div class="hidden md:flex md:ml-10 space-x-1">
                            <Link :href="route('dashboard')" :class="route().current('dashboard') ? 'bg-fuchsia-50 text-fuchsia-700' : 'text-slate-500 hover:text-fuchsia-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Tableau de bord
                            </Link>
                            <Link :href="route('events.index')" :class="route().current('events.index') ? 'bg-fuchsia-50 text-fuchsia-700' : 'text-slate-500 hover:text-fuchsia-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Événements
                            </Link>
                            <Link :href="route('members.index')" :class="route().current('members.*') ? 'bg-fuchsia-50 text-fuchsia-700' : 'text-slate-500 hover:text-fuchsia-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Membres
                            </Link>
                            <Link :href="route('welcome')" :class="route().current('welcome') ? 'bg-fuchsia-50 text-fuchsia-700' : 'text-slate-500 hover:text-fuchsia-600 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-sm font-bold transition">
                                Catalogue
                            </Link>
                        </div>
                    </div>

                    <!-- Dropdown Utilisateur -->
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-slate-600 hidden md:block">
                            Bienvenue, {{ $page.props.auth.user.name }}
                        </span>
                        <Dropdown>
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-slate-600 hover:text-fuchsia-600 focus:outline-none transition">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-fuchsia-500 to-amber-500 flex items-center justify-center text-white font-black shadow-md">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')" class="hover:bg-fuchsia-50">Profil</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="hover:bg-rose-50 text-rose-600">Déconnexion</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- En-tête de la page -->
        <header v-if="$slots.header" class="bg-white border-b border-amber-50/50 shadow-sm">
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

<script setup>
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>