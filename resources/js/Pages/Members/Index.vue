<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Object,
});

const showModal = ref(false);
const editingMember = ref(null);

const form = useForm({
    name: '',
    email: '',
    phone: '',
});

const openModal = (member = null) => {
    editingMember.value = member;
    if (member) {
        form.name = member.name;
        form.email = member.email;
        form.phone = member.phone;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    if (editingMember.value) {
        form.patch(route('members.update', editingMember.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('members.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteMember = (id) => {
    if (confirm('Supprimer ce membre ?')) {
        useForm({}).delete(route('members.destroy', id));
    }
};

const closeModal = () => {
    showModal.value = false;
    editingMember.value = null;
    form.reset();
};
</script>

<template>
    <Head title="Gestion des Membres" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">👥 Mes Membres</h2>
                <button @click="openModal()" 
                        class="bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-6 py-3 rounded-2xl text-sm font-black shadow-xl shadow-fuchsia-200 transition-all">
                    + Nouveau Membre
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-amber-100">
                    <div class="p-8 border-b border-amber-100 bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Liste des membres ({{ members.data.length }})</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-widest text-left">
                                    <th class="px-8 py-4 text-left">Nom</th>
                                    <th class="px-8 py-4 text-left">Email</th>
                                    <th class="px-8 py-4 text-left">Téléphone</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50">
                                <tr v-for="member in members.data" :key="member.id" class="group hover:bg-amber-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-fuchsia-100 rounded-full flex items-center justify-center text-fuchsia-600 font-black">
                                                {{ member.name.charAt(0).toUpperCase() }}
                                            </div>
                                            <span class="text-sm font-bold text-slate-800">{{ member.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-600">{{ member.email || '-' }}</td>
                                    <td class="px-8 py-6 text-sm text-slate-600">{{ member.phone || '-' }}</td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end space-x-2">
                                            <button @click="openModal(member)" class="p-2 text-slate-400 hover:text-fuchsia-600 hover:bg-fuchsia-50 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteMember(member.id)" class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="members.data.length === 0">
                                    <td colspan="4" class="px-8 py-12 text-center text-slate-400 italic text-sm">
                                        Aucun membre enregistré pour le moment.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Membre -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">👥</div>
                    <h3 class="text-2xl font-black relative z-10">{{ editingMember ? 'Modifier Membre' : 'Nouveau Membre' }}</h3>
                    <p class="text-slate-400 text-sm mt-1 relative z-10">Gérez vos contacts et participants</p>
                </div>
                <form @submit.prevent="submit" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <input v-model="form.name" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Nom complet" required>
                        <input v-model="form.email" type="email" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Adresse Email">
                        <input v-model="form.phone" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Téléphone">
                    </div>
                    <button type="submit" :disabled="form.processing" 
                            class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition">
                        {{ editingMember ? 'Mettre à jour' : 'Enregistrer le membre' }}
                    </button>
                    <button @click="closeModal" type="button" class="w-full text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
