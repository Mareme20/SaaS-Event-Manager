<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    event: Object,
});

const showMediaModal = ref(false);
const showQRModal = ref(false);
const selectedMedia = ref(null);
const filePreviews = ref([]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' ' + props.event.data.currency;
};

const qrCodeUrl = computed(() => {
    const publicUrl = route('events.public', props.event.data.slug);
    return `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(publicUrl)}`;
});

const mediaForm = useForm({
    files: [],
});

const uploadMedia = () => {
    mediaForm.post(route('media.store', props.event.data.id), {
        forceFormData: true,
        onSuccess: () => {
            showMediaModal.value = false;
            mediaForm.reset();
            filePreviews.value = [];
        },
    });
};

const deleteMedia = (id) => {
    if (confirm('Supprimer ce média ?')) {
        useForm({}).delete(route('media.destroy', id));
    }
};

const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    mediaForm.files = files;

    filePreviews.value = [];
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            filePreviews.value.push({
                url: e.target.result,
                type: file.type.startsWith('video') ? 'video' : 'image',
                name: file.name
            });
        };
        reader.readAsDataURL(file);
    });
};

const copyPublicLink = () => {
    const url = route('events.public', props.event.data.slug);
    navigator.clipboard.writeText(url);
    alert('✨ Lien public copié ! Partagez-le avec vos invités.');
};
</script>

<template>
    <Head :title="event.data.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center space-x-6">
                    <Link :href="route('events.index')" 
                          class="p-3 bg-white border border-amber-100 rounded-2xl text-slate-400 hover:text-fuchsia-600 hover:border-fuchsia-200 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">{{ event.data.title }}</h2>
                        <div class="flex items-center mt-1.5 space-x-3 text-sm font-medium text-slate-400">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ event.data.location }}
                            </span>
                            <span class="text-slate-200">•</span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ event.data.date || 'Date non fixée' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                    <Link :href="route('tasks.index', event.data.id)" 
                       class="flex-shrink-0 bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Checklist
                    </Link>
                    <Link :href="route('events.analytics', event.data.id)" 
                       class="flex-shrink-0 bg-white border border-slate-200 text-slate-700 px-6 py-3 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 mr-2 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zM14 20h2a2 2 0 002-2V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v15a2 2 0 002 2z"/></svg>
                        Analyses
                    </Link>
                    <a :href="route('events.report', event.data.id)" 
                       class="flex-shrink-0 bg-white border border-amber-200 text-amber-700 px-6 py-3 rounded-2xl text-sm font-bold hover:bg-amber-50 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <button @click="showQRModal = true" 
                            class="flex-shrink-0 bg-white border border-fuchsia-200 text-fuchsia-700 px-6 py-3 rounded-2xl text-sm font-bold hover:bg-fuchsia-50 transition-all flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Partager
                    </button>
                    <Link :href="route('contributions.index', event.data.id)" 
                            class="flex-shrink-0 bg-emerald-600 text-white px-8 py-3 rounded-2xl text-sm font-black hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center">
                        + Cotisations
                    </Link>
                    <Link :href="route('expenses.index', event.data.id)" 
                            class="flex-shrink-0 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-8 py-3 rounded-2xl text-sm font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-xl shadow-fuchsia-200 transition-all flex items-center justify-center">
                        + Dépenses
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-0">
            <div class="max-w-7xl mx-auto">

                <!-- Statistiques Premium -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-emerald-100 flex flex-col justify-center relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 text-4xl opacity-10 group-hover:scale-125 transition-transform">💰</div>
                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Cotisations Récoltées</p>
                        <p class="text-3xl font-black text-slate-800">{{ formatCurrency(event.data.total_contributions) }}</p>
                        <div class="mt-2 text-xs text-emerald-400 flex items-center">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span> {{ event.data.contributions.length }} versements
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-amber-100 flex flex-col justify-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Budget Prévisionnel</p>
                        <p class="text-3xl font-black text-slate-800">{{ formatCurrency(event.data.budget_estimated) }}</p>
                        <div class="mt-2 text-xs text-slate-400 flex items-center">
                            <span class="w-2 h-2 bg-fuchsia-400 rounded-full mr-2"></span> Planifié
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden flex flex-col justify-center">
                        <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-fuchsia-900/20 rounded-full blur-2xl"></div>
                        <div class="absolute top-4 right-4 text-4xl opacity-10">💎</div>
                        <p class="text-[10px] font-black text-fuchsia-400 uppercase tracking-widest mb-2">Total Dépensé</p>
                        <p class="text-3xl font-black text-white">{{ formatCurrency(event.data.budget_real) }}</p>
                        <div class="mt-4 h-2 w-full bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-fuchsia-500 to-amber-500 rounded-full transition-all duration-1000" 
                                 :style="{ width: Math.min((event.data.budget_real / event.data.budget_estimated) * 100, 100) + '%' }"></div>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-amber-100 flex flex-col justify-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Solde de Caisse</p>
                        <p class="text-3xl font-black" :class="event.data.total_contributions - event.data.budget_real < 0 ? 'text-rose-500' : 'text-emerald-500'">
                            {{ formatCurrency(event.data.total_contributions - event.data.budget_real) }}
                        </p>
                        <div class="mt-2 text-xs font-medium" :class="event.data.total_contributions - event.data.budget_real < 0 ? 'text-rose-400' : 'text-emerald-500'">
                            {{ event.data.total_contributions - event.data.budget_real < 0 ? '⚠️ Déficit' : '✅ Excédent' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Résumé Cotisations -->
                        <div class="bg-white shadow-sm rounded-[2.5rem] overflow-hidden border border-emerald-100">
                            <div class="p-8 border-b border-emerald-100 flex justify-between items-center bg-gradient-to-r from-emerald-50/30 to-teal-50/30">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                                    <span>🤝</span> Cotisations des Membres
                                </h3>
                                <Link :href="route('contributions.index', event.data.id)" class="text-xs font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest">
                                    Gérer tout →
                                </Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <tbody class="divide-y divide-emerald-50">
                                        <tr v-for="contribution in event.data.contributions.slice(0, 5)" :key="contribution.id" 
                                            class="group hover:bg-emerald-50/30 transition-colors">
                                            <td class="px-8 py-4">
                                                <span class="text-sm font-bold text-slate-800">{{ contribution.user?.name || contribution.member?.name }}</span>
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <p class="text-sm font-black text-emerald-600">+ {{ formatCurrency(contribution.amount) }}</p>
                                            </td>
                                        </tr>
                                        <tr v-if="event.data.contributions.length === 0">
                                            <td class="px-8 py-12 text-center text-slate-400 italic text-sm">
                                                Aucune cotisation enregistrée.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Résumé Dépenses -->
                        <div class="bg-white shadow-sm rounded-[2.5rem] overflow-hidden border border-amber-100">
                            <div class="p-8 border-b border-amber-100 flex justify-between items-center bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                                    <span>💰</span> Suivi des Dépenses
                                </h3>
                                <Link :href="route('expenses.index', event.data.id)" class="text-xs font-black text-fuchsia-600 hover:text-fuchsia-700 uppercase tracking-widest">
                                    Gérer tout →
                                </Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <tbody class="divide-y divide-amber-50">
                                        <tr v-for="expense in event.data.expenses.slice(0, 5)" :key="expense.id" 
                                            class="group hover:bg-amber-50/30 transition-colors">
                                            <td class="px-8 py-4">
                                                <p class="text-sm font-bold text-slate-800">{{ expense.title }}</p>
                                            </td>
                                            <td class="px-8 py-4 text-right">
                                                <p class="text-sm font-black text-slate-800">{{ formatCurrency(expense.amount) }}</p>
                                            </td>
                                        </tr>
                                        <tr v-if="event.data.expenses.length === 0">
                                            <td class="px-8 py-12 text-center text-slate-400 italic text-sm">
                                                Aucune dépense enregistrée.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Galerie Latérale -->
                    <div class="space-y-8">
                        <div class="bg-white shadow-sm rounded-[2.5rem] overflow-hidden border border-amber-100 p-8">
                            <div class="flex justify-between items-center mb-8">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                                    <span>📸</span> Souvenirs
                                </h3>
                                <button @click="showMediaModal = true" 
                                        class="p-2.5 bg-gradient-to-r from-fuchsia-100 to-amber-100 text-fuchsia-600 rounded-xl hover:from-fuchsia-600 hover:to-amber-500 hover:text-white transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            <div v-if="event.data.media.length > 0" class="grid grid-cols-2 gap-4">
                                <div v-for="media in event.data.media" :key="media.id" 
                                     @click="selectedMedia = media"
                                     class="relative group aspect-square rounded-[1.5rem] overflow-hidden bg-slate-100 border-2 border-white shadow-md transition-all duration-500 hover:scale-105 hover:shadow-xl hover:border-fuchsia-200 cursor-pointer">
                                    <img v-if="media.file_type === 'image'" :src="media.file_url" :alt="media.title" class="object-cover w-full h-full">
                                    <video v-else :src="media.file_url" class="object-cover w-full h-full" muted></video>
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button @click.stop="deleteMedia(media.id)" 
                                                class="p-2.5 bg-rose-500 text-white rounded-full hover:scale-110 shadow-lg transition-transform">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal QR Code -->
        <div v-if="showQRModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-sm shadow-2xl overflow-hidden p-10 text-center animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="w-20 h-20 bg-gradient-to-br from-fuchsia-100 to-amber-100 text-fuchsia-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">✨ Partager</h3>
                <div class="bg-gradient-to-br from-fuchsia-50 to-amber-50 p-6 rounded-[2rem] flex justify-center mb-8">
                    <img :src="qrCodeUrl" class="w-48 h-48 rounded-2xl shadow-lg border-8 border-white bg-white">
                </div>
                <div class="space-y-4">
                    <button @click="copyPublicLink" 
                            class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 transition shadow-xl shadow-fuchsia-200">
                        📋 Copier le Lien Public
                    </button>
                    <button @click="showQRModal = false" 
                            class="text-slate-400 hover:text-slate-600 text-xs font-black uppercase tracking-widest transition">
                        Fermer
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Média -->
        <div v-if="showMediaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-r from-fuchsia-600 to-amber-500 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">📸</div>
                    <h3 class="text-2xl font-black relative z-10">Souvenirs</h3>
                </div>
                <form @submit.prevent="uploadMedia" class="p-8 space-y-6">
                    <div class="border-4 border-dashed border-amber-200 rounded-[2rem] p-8 text-center hover:bg-amber-50/30 transition-colors cursor-pointer relative group">
                        <input @change="handleFileChange" type="file" multiple class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*,video/*">
                        <div v-if="filePreviews.length === 0" class="py-4">
                            <p class="text-slate-500 font-medium">Ajoutez vos photos et vidéos</p>
                        </div>
                        <div v-else class="grid grid-cols-4 gap-2 py-4">
                            <div v-for="(preview, index) in filePreviews.slice(0, 8)" :key="index" 
                                 class="aspect-square rounded-xl overflow-hidden shadow-md">
                                <img v-if="preview.type === 'image'" :src="preview.url" class="object-cover w-full h-full">
                            </div>
                        </div>
                    </div>
                    <button type="submit" :disabled="mediaForm.processing || mediaForm.files.length === 0" 
                            class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black shadow-lg shadow-fuchsia-200 transition">
                        📤 Publier
                    </button>
                </form>
            </div>
        </div>

        <!-- Lightbox -->
        <div v-if="selectedMedia" 
             class="fixed inset-0 z-[100] bg-slate-900/95 backdrop-blur-2xl flex items-center justify-center p-4 md:p-8"
             @click.self="selectedMedia = null">
            
            <button @click="selectedMedia = null" 
                    class="absolute top-6 right-6 z-10 p-3 bg-white/10 backdrop-blur-md rounded-full text-white/70 hover:text-white hover:bg-fuchsia-600 transition-all border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="relative max-w-7xl w-full h-full flex items-center justify-center">
                <img v-if="selectedMedia.file_type === 'image'" 
                     :src="selectedMedia.file_url" 
                     :alt="selectedMedia.title" 
                     class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border border-white/10 object-contain">
                
                <video v-else :src="selectedMedia.file_url" controls autoplay 
                       class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl border border-white/10"></video>
            </div>
        </div>
    </AuthenticatedLayout>
</template>