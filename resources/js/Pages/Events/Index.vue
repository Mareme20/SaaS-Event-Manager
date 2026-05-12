<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    events: Object,
});

const showCreateModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    date: '',
    location: '',
    budget_estimated: 0,
    currency: 'FCFA',
});

const submit = () => {
    form.post(route('events.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

const formatDate = (date) => {
    if (!date) return 'Date à définir';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Gestion des Festivités" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Vos Célébrations</h2>
                    <p class="text-slate-500 mt-1 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-400 rounded-full"></span> 
                        Gérez l'organisation et la mémoire de vos événements
                    </p>
                </div>
                <button 
                    @click="showCreateModal = true"
                    class="group relative inline-flex items-center justify-center px-6 py-3.5 font-bold text-white transition-all duration-300 bg-gradient-to-r from-fuchsia-600 to-amber-500 rounded-2xl hover:from-fuchsia-700 hover:to-amber-600 shadow-xl shadow-fuchsia-200 hover:scale-105"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle Fête
                </button>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto">
                
                <!-- Statistiques Globales Festives -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-amber-100 flex items-center space-x-4 group hover:shadow-xl hover:border-fuchsia-100 transition">
                        <div class="p-3 bg-fuchsia-50 text-fuchsia-600 rounded-2xl group-hover:bg-fuchsia-600 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Événements Actifs</p>
                            <p class="text-2xl font-black text-slate-800">{{ events.data.length }}</p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-amber-100 flex items-center space-x-4 group hover:shadow-xl hover:border-fuchsia-100 transition">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Budget Total Estimé</p>
                            <p class="text-2xl font-black text-slate-800">{{ events.data.reduce((acc, e) => acc + (e.budget_estimated || 0), 0).toLocaleString() }} FCFA</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-fuchsia-50 to-amber-50 p-6 rounded-[2rem] border border-fuchsia-100">
                        <p class="text-sm font-bold text-fuchsia-800">✨ Prêt à faire la fête ?</p>
                        <p class="text-xs text-slate-600 mt-1">Créez un événement et partagez les souvenirs.</p>
                    </div>
                </div>

                <!-- Grid des Événements -->
                <div v-if="events.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="event in events.data" :key="event.id" 
                         class="group relative bg-white rounded-[2.5rem] p-6 border border-amber-100 transition-all duration-300 hover:shadow-2xl hover:shadow-fuchsia-100/30 hover:-translate-y-2">
                        
                        <!-- En-tête de la carte -->
                        <div class="flex justify-between items-start mb-6">
                            <div class="p-4 bg-amber-50 rounded-2xl group-hover:bg-fuchsia-50 transition-colors">
                                <svg class="w-8 h-8 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-full border"
                                  :class="{
                                    'bg-green-50 text-green-700 border-green-100': event.status === 'actif',
                                    'bg-amber-50 text-amber-700 border-amber-100': event.status === 'brouillon',
                                    'bg-slate-50 text-slate-500 border-slate-200': event.status === 'termine'
                                  }">
                                {{ event.status || 'Actif' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-800 mb-2 leading-tight group-hover:text-fuchsia-600 transition-colors">{{ event.title }}</h3>
                        <p class="text-slate-400 text-sm mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ event.location }} • {{ formatDate(event.date) }}
                        </p>

                        <!-- Jauge de Budget Premium FestiVault -->
                        <div class="bg-slate-50/50 rounded-2xl p-5 mb-6 border border-slate-100">
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">
                                <span>Utilisation Budget</span>
                                <span :class="event.budget_real > event.budget_estimated ? 'text-rose-500' : 'text-fuchsia-600'">
                                    {{ Math.round((event.budget_real / (event.budget_estimated || 1)) * 100) }}%
                                </span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full rounded-full transition-all duration-1000" 
                                    :class="event.budget_real > event.budget_estimated ? 'bg-rose-500' : 'bg-gradient-to-r from-fuchsia-500 to-amber-500'"
                                    :style="{ width: Math.min((event.budget_real / (event.budget_estimated || 1)) * 100, 100) + '%' }"
                                ></div>
                            </div>
                            <div class="flex justify-between mt-3">
                                <span class="text-sm font-black text-slate-800">{{ event.budget_real.toLocaleString() }} <span class="text-[8px] font-bold text-slate-400 uppercase">{{ event.currency }}</span></span>
                                <span class="text-sm font-black text-slate-300">{{ event.budget_estimated.toLocaleString() }} <span class="text-[8px] uppercase">{{ event.currency }}</span></span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <Link 
                                :href="route('events.show', event.id)"
                                class="flex-1 bg-slate-800 text-white text-center py-3.5 rounded-xl font-bold text-sm hover:bg-gradient-to-r hover:from-fuchsia-600 hover:to-amber-500 transition-all shadow-lg shadow-slate-100 hover:shadow-fuchsia-200"
                            >
                                Gérer l'événement
                            </Link>
                        </div>
                        
                        <!-- Petit ruban festif -->
                        <div class="absolute -top-3 -right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-3xl">🎈</span>
                        </div>
                    </div>
                </div>

                <!-- Empty State Festif -->
                <div v-else class="text-center py-24 bg-white rounded-[3rem] shadow-sm border-2 border-dashed border-amber-200">
                    <div class="mx-auto w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Prêt à lancer votre première fête ?</h3>
                    <p class="text-slate-500 mt-2 max-w-md mx-auto">Cliquez sur "Nouvelle Fête" pour commencer à planifier un événement inoubliable.</p>
                </div>
            </div>
        </div>

        <!-- Modal Création : Version FestiVault -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-r from-fuchsia-600 to-amber-500 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black relative z-10">✨ Créer une célébration</h3>
                    <p class="text-white/80 text-sm mt-1 relative z-10">Remplissez les détails pour commencer l'aventure</p>
                    <button @click="showCreateModal = false" class="absolute top-8 right-8 text-white/70 hover:text-white transition z-20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form @submit.prevent="submit" class="p-8 space-y-6 bg-white">
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Nom de l'événement</label>
                            <input v-model="form.title" type="text" placeholder="Ex: Mariage Aminata & Jean" class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Budget Prévisionnel</label>
                                <input v-model="form.budget_estimated" type="number" placeholder="0" class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Devise</label>
                                <select v-model="form.currency" class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                                    <option value="FCFA">FCFA (Franc CFA)</option>
                                    <option value="EUR">EUR (Euro €)</option>
                                    <option value="USD">USD (Dollar $)</option>
                                    <option value="NGN">NGN (Naira ₦)</option>
                                    <option value="GHS">GHS (Cedi ₵)</option>
                                    <option value="GBP">GBP (Livre £)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Lieu</label>
                                <input v-model="form.location" type="text" placeholder="Dakar, Sénégal" class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Date</label>
                                <input v-model="form.date" type="date" class="mt-1 block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                            </div>
                        </div>
                    </div>
                    <button type="submit" :disabled="form.processing" class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 transition shadow-lg shadow-fuchsia-200 text-lg">
                        🚀 Lancer la planification
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>