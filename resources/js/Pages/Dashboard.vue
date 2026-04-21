<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
};

const formatDate = (date) => {
    if (!date) return 'Non fixée';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long'
    });
};

const getProgressColor = (percentage) => {
    if (percentage < 50) return 'bg-emerald-500';
    if (percentage < 80) return 'bg-amber-500';
    return 'bg-rose-500';
};

// Message de bienvenue dynamique selon l'heure
const getWelcomeMessage = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Bonjour';
    if (hour < 18) return 'Bon après-midi';
    return 'Bonsoir';
};
</script>

<template>
    <Head title="Tableau de Bord" />

    <AuthenticatedLayout>
        <!-- Header avec bienvenue -->
        <template #header>
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-3xl">🎉</span>
                        <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                            {{ getWelcomeMessage() }}, {{ $page.props.auth.user.name }} !
                        </h1>
                    </div>
                    <p class="text-slate-500 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 bg-fuchsia-500 rounded-full animate-pulse"></span>
                        Voici l'aperçu de vos célébrations
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-4 py-2 text-xs font-black bg-gradient-to-r from-fuchsia-50 to-amber-50 text-fuchsia-700 rounded-full border border-fuchsia-200 shadow-sm">
                        📅 {{ new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </span>
                    <Link :href="route('events.create')" 
                          class="px-5 py-2.5 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white rounded-full text-sm font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nouvelle fête
                    </Link>
                </div>
            </div>
        </template>

        <!-- Contenu principal -->
        <div class="py-6">
            <!-- Cartes statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Budget total -->
                <div class="bg-gradient-to-br from-fuchsia-600 via-fuchsia-500 to-amber-500 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute top-4 right-4 text-6xl opacity-20">💎</div>
                    <div class="relative z-10">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-white/80 mb-1">Budget total</p>
                                <p class="text-3xl font-black">{{ formatCurrency(stats.total_budget) }}</p>
                                <p class="text-xs text-white/60 mt-2">Tous vos événements</p>
                            </div>
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget consommé -->
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-500 mb-1">Budget consommé</p>
                            <p class="text-3xl font-black text-slate-800">{{ formatCurrency(stats.total_spent) }}</p>
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="text-slate-400 font-medium">Progression</span>
                                    <span class="font-black" :class="{
                                        'text-emerald-600': (stats.total_spent / stats.total_budget) * 100 < 50,
                                        'text-amber-600': (stats.total_spent / stats.total_budget) * 100 >= 50 && (stats.total_spent / stats.total_budget) * 100 < 80,
                                        'text-rose-600': (stats.total_spent / stats.total_budget) * 100 >= 80
                                    }">{{ Math.round((stats.total_spent / stats.total_budget) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div 
                                        class="h-2.5 rounded-full transition-all duration-700"
                                        :class="getProgressColor((stats.total_spent / stats.total_budget) * 100)"
                                        :style="{ width: Math.min((stats.total_spent / stats.total_budget) * 100, 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zM14 20h2a2 2 0 002-2V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v15a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total événements -->
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Événements</p>
                            <p class="text-3xl font-black text-slate-800">{{ stats.total_events }}</p>
                            <p class="text-xs text-slate-400 mt-2">
                                <span v-if="stats.upcoming && stats.upcoming.length > 0">
                                    {{ stats.upcoming.length }} à venir
                                </span>
                                <span v-else>Aucun à venir</span>
                            </p>
                            <Link :href="route('events.index')" 
                                  class="inline-flex items-center text-xs font-bold text-fuchsia-600 hover:text-amber-600 mt-3 transition-colors">
                                Voir tous
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                        </div>
                        <div class="p-3 bg-fuchsia-50 rounded-xl">
                            <svg class="w-6 h-6 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section principale -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Prochains événements -->
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                        <div class="flex justify-between items-center">
                            <h3 class="font-black text-slate-800 flex items-center gap-2">
                                <span>📅</span> Prochains événements
                            </h3>
                            <Link :href="route('events.index')" 
                                  class="text-xs font-bold text-fuchsia-600 hover:text-amber-600 transition-colors">
                                Tout voir →
                            </Link>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-amber-50">
                        <div v-if="stats.upcoming && stats.upcoming.length > 0">
                            <Link 
                                v-for="event in stats.upcoming.slice(0, 5)" 
                                :key="event.id" 
                                :href="route('events.show', event.id)"
                                class="block p-5 hover:bg-amber-50/30 transition-colors group"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-14 h-14 bg-gradient-to-br from-fuchsia-100 to-amber-100 rounded-xl flex flex-col items-center justify-center group-hover:scale-105 transition-transform shadow-sm">
                                            <span class="text-xs font-black text-fuchsia-600 uppercase">
                                                {{ new Date(event.date).toLocaleDateString('fr-FR', { month: 'short' }) }}
                                            </span>
                                            <span class="text-xl font-black text-fuchsia-700 leading-none">
                                                {{ new Date(event.date).getDate() }}
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 group-hover:text-fuchsia-600 transition-colors">{{ event.title }}</h4>
                                            <p class="text-sm text-slate-400 flex items-center gap-1 mt-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ event.location }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-slate-300 group-hover:text-fuchsia-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </Link>
                        </div>
                        
                        <div v-else class="p-8 text-center">
                            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-slate-500 font-medium">Aucun événement à venir</p>
                            <Link :href="route('events.create')" 
                                  class="inline-flex items-center text-sm font-bold text-fuchsia-600 hover:text-amber-600 mt-3 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Créer votre première fête
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800 rounded-2xl shadow-xl p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-fuchsia-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute top-4 right-4 text-5xl opacity-10">⚡</div>
                    
                    <div class="relative z-10">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-black mb-1 flex items-center gap-2">
                                    <span>🚀</span> Actions rapides
                                </h3>
                                <p class="text-slate-400 text-sm">Gérez votre activité efficacement</p>
                            </div>
                            <div class="p-2.5 bg-white/10 rounded-xl backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <Link 
                                :href="route('events.create')" 
                                class="flex items-center justify-between bg-gradient-to-r from-fuchsia-600 to-fuchsia-500 hover:from-fuchsia-500 hover:to-fuchsia-400 rounded-xl p-4 transition-all group"
                            >
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="font-bold">Créer un événement</span>
                                </div>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>

                            <Link 
                                :href="route('profile.edit')" 
                                class="flex items-center justify-between bg-slate-700/50 hover:bg-slate-600/50 backdrop-blur-sm rounded-xl p-4 transition-all group border border-white/5"
                            >
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-medium">Mon profil</span>
                                </div>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>

                            <Link 
                                :href="route('events.index')" 
                                class="flex items-center justify-between bg-slate-700/50 hover:bg-slate-600/50 backdrop-blur-sm rounded-xl p-4 transition-all group border border-white/5"
                            >
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="font-medium">Tous mes événements</span>
                                </div>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Événements récents -->
            <div v-if="stats.recent_events && stats.recent_events.length > 0" class="mt-6">
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                        <h3 class="font-black text-slate-800 flex items-center gap-2">
                            <span>📋</span> Événements récents
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-amber-50/30 border-b border-amber-100">
                                <tr>
                                    <th class="text-left px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-wider">Événement</th>
                                    <th class="text-left px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-wider">Date</th>
                                    <th class="text-left px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-wider">Lieu</th>
                                    <th class="text-left px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-wider">Budget</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="event in stats.recent_events.slice(0, 5)" :key="event.id" class="hover:bg-amber-50/20 transition-colors">
                                    <td class="px-6 py-4">
                                        <Link :href="route('events.show', event.id)" 
                                              class="font-bold text-slate-800 hover:text-fuchsia-600 transition-colors">
                                            {{ event.title }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(event.date) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ event.location }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ formatCurrency(event.budget) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>