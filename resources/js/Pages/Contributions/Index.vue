<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    event: Object,
    contributions: Object,
    members: Object,
    filters: {
        type: Object,
        default: () => ({})
    }
});

const showModal = ref(false);
const editingContribution = ref(null);
const isProcessingPayment = ref(false);
const showFilters = ref(false);

// Filtres
const filterForm = useForm({
    search: props.filters?.search || '',
    member_id: props.filters?.member_id || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    min_amount: props.filters?.min_amount || '',
    max_amount: props.filters?.max_amount || '',
    sort_by: props.filters?.sort_by || 'date',
    sort_order: props.filters?.sort_order || 'desc',
});

const form = useForm({
    member_id: '',
    amount: '',
    description: '',
    date: new Date().toISOString().substr(0, 10),
});

// Stats calculées
const totalAmount = computed(() => {
    return props.contributions.data.reduce((acc, c) => acc + c.amount, 0);
});

const averageAmount = computed(() => {
    if (props.contributions.data.length === 0) return 0;
    return totalAmount.value / props.contributions.data.length;
});

const uniqueContributors = computed(() => {
    const members = new Set(props.contributions.data.map(c => c.member?.id));
    return members.size;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' ' + props.event.currency;
};

// Debounce pour la recherche
let searchTimeout;
const applyFilters = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = {};
        Object.keys(filterForm).forEach(key => {
            if (filterForm[key] !== '' && filterForm[key] !== null) {
                query[key] = filterForm[key];
            }
        });
        router.get(route('contributions.index', props.event.id), query, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    }, 300);
};

// Watchers pour les filtres
watch(() => filterForm.search, applyFilters);
watch(() => filterForm.member_id, applyFilters);
watch(() => filterForm.sort_by, applyFilters);
watch(() => filterForm.sort_order, applyFilters);

// Application manuelle pour les dates et montants
const applyDateFilters = () => {
    applyFilters();
};

const resetFilters = () => {
    filterForm.search = '';
    filterForm.member_id = '';
    filterForm.date_from = '';
    filterForm.date_to = '';
    filterForm.min_amount = '';
    filterForm.max_amount = '';
    filterForm.sort_by = 'date';
    filterForm.sort_order = 'desc';
    applyFilters();
};

const openModal = (contribution = null) => {
    editingContribution.value = contribution;
    if (contribution) {
        form.member_id = contribution.member.id;
        form.amount = contribution.amount;
        form.description = contribution.description;
        form.date = contribution.date;
    } else {
        form.reset();
        form.date = new Date().toISOString().substr(0, 10);
    }
    showModal.value = true;
};

const submit = () => {
    if (editingContribution.value) {
        form.patch(route('contributions.update', editingContribution.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('contributions.store', props.event.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const initiateOnlinePayment = async () => {
    if (!form.member_id || !form.amount) {
        alert('Veuillez sélectionner un membre et un montant.');
        return;
    }

    isProcessingPayment.value = true;
    try {
        const response = await fetch(route('payments.checkout', props.event.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify({
                member_id: form.member_id,
                amount: form.amount
            })
        });

        const data = await response.json();
        if (data.url) {
            window.location.href = data.url;
        }
    } catch (error) {
        console.error('Erreur de paiement', error);
        alert('Une erreur est survenue lors de l\'initialisation du paiement.');
    } finally {
        isProcessingPayment.value = false;
    }
};

const deleteContribution = (id) => {
    if (confirm('Supprimer cette cotisation ?')) {
        useForm({}).delete(route('contributions.destroy', id));
    }
};

const closeModal = () => {
    showModal.value = false;
    editingContribution.value = null;
    form.reset();
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.charAt(0).toUpperCase();
};
</script>

<template>
    <Head :title="'Cotisations - ' + event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <Link :href="route('events.show', event.id)" 
                          class="p-2.5 bg-white border border-amber-100 rounded-xl text-slate-400 hover:text-fuchsia-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span>💰</span> Cotisations : {{ event.title }}
                        </h2>
                        <p class="text-sm text-slate-500 font-medium">Gestion des versements des membres</p>
                    </div>
                </div>
                <button @click="openModal()" 
                        class="bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-8 py-3 rounded-2xl text-sm font-black shadow-xl shadow-fuchsia-200 hover:shadow-2xl transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouveau Versement
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Cartes statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Récolté</p>
                        <p class="text-2xl font-black text-emerald-600">{{ formatCurrency(totalAmount) }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Moyenne par versement</p>
                        <p class="text-2xl font-black text-amber-600">{{ formatCurrency(averageAmount) }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Contributeurs uniques</p>
                        <p class="text-2xl font-black text-fuchsia-600">{{ uniqueContributors }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total versements</p>
                        <p class="text-2xl font-black text-slate-700">{{ contributions.total }}</p>
                    </div>
                </div>

                <!-- Barre de filtres -->
                <div class="bg-white rounded-2xl shadow-sm border border-amber-100 mb-6 overflow-hidden">
                    <div class="p-4 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                        <button @click="showFilters = !showFilters" 
                                class="flex items-center justify-between w-full text-left">
                            <span class="font-black text-slate-700 flex items-center gap-2">
                                <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filtres et recherche
                            </span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    
                    <div v-show="showFilters" class="p-6 bg-slate-50/30">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <!-- Recherche -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Rechercher</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <input v-model="filterForm.search" type="text" 
                                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 focus:border-transparent text-sm"
                                           placeholder="Description, montant...">
                                </div>
                            </div>
                            
                            <!-- Filtre par membre -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Membre</label>
                                <select v-model="filterForm.member_id" 
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm">
                                    <option value="">Tous les membres</option>
                                    <option v-for="member in members.data" :key="member.id" :value="member.id">
                                        {{ member.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Tri -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Trier par</label>
                                <select v-model="filterForm.sort_by" 
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm">
                                    <option value="date">Date</option>
                                    <option value="amount">Montant</option>
                                    <option value="member">Membre</option>
                                </select>
                            </div>
                            
                            <!-- Ordre -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Ordre</label>
                                <select v-model="filterForm.sort_order" 
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm">
                                    <option value="desc">Décroissant</option>
                                    <option value="asc">Croissant</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Date début -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Date début</label>
                                <input v-model="filterForm.date_from" type="date" @change="applyDateFilters"
                                       class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm">
                            </div>
                            
                            <!-- Date fin -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Date fin</label>
                                <input v-model="filterForm.date_to" type="date" @change="applyDateFilters"
                                       class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm">
                            </div>
                            
                            <!-- Montant min -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Montant min</label>
                                <input v-model="filterForm.min_amount" type="number" @change="applyDateFilters"
                                       class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm"
                                       placeholder="0">
                            </div>
                            
                            <!-- Montant max -->
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Montant max</label>
                                <input v-model="filterForm.max_amount" type="number" @change="applyDateFilters"
                                       class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl focus:ring-2 focus:ring-fuchsia-500 text-sm"
                                       placeholder="∞">
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-amber-100">
                            <p class="text-xs text-slate-400">
                                <span v-if="contributions.total > 0">{{ contributions.total }} résultat(s) trouvé(s)</span>
                                <span v-else>Aucun résultat</span>
                            </p>
                            <button @click="resetFilters" 
                                    class="text-xs font-bold text-fuchsia-600 hover:text-amber-600 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Réinitialiser les filtres
                            </button>
                        </div>
                    </div>
                    
                    <!-- Résumé des filtres actifs -->
                    <div v-if="Object.keys(filters).length > 0" class="px-6 py-3 bg-amber-50/50 border-t border-amber-100 flex flex-wrap gap-2">
                        <span v-if="filters.search" class="inline-flex items-center px-3 py-1 bg-white rounded-full text-xs border border-amber-200">
                            🔍 "{{ filters.search }}"
                        </span>
                        <span v-if="filters.member_id" class="inline-flex items-center px-3 py-1 bg-white rounded-full text-xs border border-amber-200">
                            👤 Membre spécifique
                        </span>
                        <span v-if="filters.date_from" class="inline-flex items-center px-3 py-1 bg-white rounded-full text-xs border border-amber-200">
                            📅 Du {{ filters.date_from }}
                        </span>
                        <span v-if="filters.date_to" class="inline-flex items-center px-3 py-1 bg-white rounded-full text-xs border border-amber-200">
                            Au {{ filters.date_to }}
                        </span>
                    </div>
                </div>

                <!-- Tableau des cotisations -->
                <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-amber-100">
                    <div class="p-6 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Historique des versements</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-widest text-left border-b border-amber-100">
                                    <th class="px-6 py-4">Membre</th>
                                    <th class="px-6 py-4">Description</th>
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4 text-right">Montant</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="contribution in contributions.data" :key="contribution.id" class="group hover:bg-amber-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-9 h-9 bg-gradient-to-br from-fuchsia-100 to-amber-100 rounded-xl flex items-center justify-center text-fuchsia-600 text-xs font-black shadow-sm">
                                                {{ getInitials(contribution.member?.name) }}
                                            </div>
                                            <span class="text-sm font-bold text-slate-800">{{ contribution.member?.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600">{{ contribution.description || 'Cotisation' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500">{{ contribution.date }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-emerald-600">+ {{ formatCurrency(contribution.amount) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end space-x-1">
                                            <button @click="openModal(contribution)" 
                                                    class="p-2 text-slate-400 hover:text-fuchsia-600 hover:bg-fuchsia-50 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteContribution(contribution.id)" 
                                                    class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="contributions.data.length === 0">
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-slate-500 font-medium">Aucun versement trouvé</p>
                                        <p class="text-slate-400 text-sm mt-1">Ajustez vos filtres ou ajoutez un nouveau versement</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div v-if="contributions.links && contributions.links.length > 3" class="px-6 py-4 border-t border-amber-100 bg-slate-50/30">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-slate-400">
                                Affichage de {{ contributions.from || 0 }} à {{ contributions.to || 0 }} sur {{ contributions.total }} résultats
                            </p>
                            <div class="flex items-center gap-1">
                                <Link v-for="link in contributions.links" 
                                      :key="link.label"
                                      :href="link.url || '#'"
                                      v-html="link.label"
                                      class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all"
                                      :class="{
                                          'bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white shadow-sm': link.active,
                                          'text-slate-400 hover:bg-amber-50 hover:text-fuchsia-600': !link.active && link.url,
                                          'text-slate-300 cursor-not-allowed': !link.url
                                      }"
                                      :preserve-scroll="true"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Cotisation -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-r from-fuchsia-600 to-amber-500 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">💰</div>
                    <h3 class="text-2xl font-black relative z-10">{{ editingContribution ? 'Modifier Versement' : 'Nouveau Versement' }}</h3>
                    <p class="text-white/80 text-sm mt-1 relative z-10">Enregistrez la participation financière</p>
                </div>
                <form @submit.prevent="submit" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <select v-model="form.member_id" 
                                class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" required>
                            <option value="" disabled>Sélectionner un membre</option>
                            <option v-for="member in members.data" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </select>
                        <input v-model="form.amount" type="number" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Montant versé" required>
                        <input v-model="form.description" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Note (ex: Cotisation Mars)">
                        <input v-model="form.date" type="date" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" required>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <button type="submit" :disabled="form.processing" 
                                class="w-full bg-white border-2 border-fuchsia-600 text-fuchsia-600 py-4 rounded-2xl font-black hover:bg-fuchsia-50 transition">
                            ✅ Enregistrer (Manuel)
                        </button>
                        
                        <button type="button" @click="initiateOnlinePayment" :disabled="isProcessingPayment"
                                class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition flex items-center justify-center">
                            <span v-if="isProcessingPayment" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                Traitement...
                            </span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                💳 Payer en ligne
                            </span>
                        </button>
                    </div>

                    <button @click="closeModal" type="button" class="w-full text-slate-400 text-xs font-bold uppercase tracking-widest mt-2 hover:text-slate-600 transition">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>