<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
    expenses: Object,
});

const showModal = ref(false);
const editingExpense = ref(null);

const form = useForm({
    title: '',
    amount: '',
    category: 'Général',
    description: '',
    date: new Date().toISOString().substr(0, 10),
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' ' + props.event.currency;
};

const openModal = (expense = null) => {
    editingExpense.value = expense;
    if (expense) {
        form.title = expense.title;
        form.amount = expense.amount;
        form.category = expense.category;
        form.description = expense.description;
        form.date = expense.date;
    } else {
        form.reset();
        form.date = new Date().toISOString().substr(0, 10);
    }
    showModal.value = true;
};

const submit = () => {
    if (editingExpense.value) {
        form.patch(route('expenses.update', editingExpense.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('expenses.store', props.event.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteExpense = (id) => {
    if (confirm('Supprimer cette dépense ?')) {
        useForm({}).delete(route('expenses.destroy', id));
    }
};

const closeModal = () => {
    showModal.value = false;
    editingExpense.value = null;
    form.reset();
};
</script>

<template>
    <Head :title="'Dépenses - ' + event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <Link :href="route('events.show', event.id)" 
                          class="p-2.5 bg-white border border-amber-100 rounded-xl text-slate-400 hover:text-fuchsia-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">💰 Dépenses : {{ event.title }}</h2>
                        <p class="text-sm text-slate-500 font-medium">Gestion du budget de l'événement</p>
                    </div>
                </div>
                <button @click="openModal()" 
                        class="bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-8 py-3 rounded-2xl text-sm font-black shadow-xl shadow-fuchsia-200 transition-all">
                    + Nouvelle Dépense
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-amber-100">
                    <div class="p-8 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30 flex justify-between items-center">
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Détail des sorties d'argent</h3>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-fuchsia-500 uppercase tracking-widest">Total Dépensé</p>
                            <p class="text-2xl font-black text-slate-800">{{ formatCurrency(expenses.data.reduce((acc, e) => acc + e.amount, 0)) }}</p>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest text-left">
                                    <th class="px-8 py-4">Libellé</th>
                                    <th class="px-8 py-4">Catégorie</th>
                                    <th class="px-8 py-4">Date</th>
                                    <th class="px-8 py-4 text-right">Montant</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="expense in expenses.data" :key="expense.id" class="group hover:bg-amber-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <p class="text-sm font-bold text-slate-800">{{ expense.title }}</p>
                                        <p class="text-xs text-slate-400 truncate max-w-xs">{{ expense.description }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                            {{ expense.category }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-500">{{ expense.date }}</td>
                                    <td class="px-8 py-6 text-right text-sm font-black text-rose-500">- {{ formatCurrency(expense.amount) }}</td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end space-x-2">
                                            <button @click="openModal(expense)" class="p-2 text-slate-400 hover:text-fuchsia-600 hover:bg-fuchsia-50 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteExpense(expense.id)" class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="expenses.data.length === 0">
                                    <td colspan="5" class="px-8 py-12 text-center text-slate-400 italic text-sm">
                                        Aucune dépense enregistrée pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Dépense -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">💰</div>
                    <h3 class="text-2xl font-black relative z-10">{{ editingExpense ? 'Modifier Dépense' : 'Nouvelle Dépense' }}</h3>
                    <p class="text-slate-400 text-sm mt-1 relative z-10">Gardez le contrôle sur votre budget</p>
                </div>
                <form @submit.prevent="submit" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <input v-model="form.title" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Libellé (ex: Location sono)" required>
                        <div class="grid grid-cols-2 gap-4">
                            <input v-model="form.amount" type="number" 
                                   class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                                   placeholder="Montant" required>
                            <select v-model="form.category" 
                                    class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                                <option>Logistique</option>
                                <option>Restauration</option>
                                <option>Communication</option>
                                <option>Transport</option>
                                <option>Général</option>
                            </select>
                        </div>
                        <input v-model="form.description" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Note supplémentaire">
                        <input v-model="form.date" type="date" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" required>
                    </div>
                    <button type="submit" :disabled="form.processing" 
                            class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition">
                        {{ editingExpense ? 'Mettre à jour' : 'Enregistrer la dépense' }}
                    </button>
                    <button @click="closeModal" type="button" class="w-full text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
