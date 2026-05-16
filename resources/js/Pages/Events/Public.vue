<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    event: Object,
});

const selectedMedia = ref(null);

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const downloadMedia = (url, name) => {
    const link = document.createElement('a');
    link.href = url;
    link.download = name || 'souvenir';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

<template>
    <Head :title="event?.data?.title || 'Événement'" />

    <!-- Fond ultra sombre mais avec une pointe de chaleur (dégradé très subtil vers le brun/fuchsia) -->
    <div class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white font-sans">
        
        <!-- Header : Minimaliste mais avec un accent festif -->
        <div class="max-w-7xl mx-auto px-6 py-12 border-b border-slate-800/50">
            <Link :href="route('welcome')" class="inline-flex items-center text-xs font-bold uppercase tracking-widest text-fuchsia-400/70 hover:text-amber-400 transition-colors group">
                <span class="mr-2 text-lg leading-none group-hover:-translate-x-1 transition-transform">←</span> 
                Retour au catalogue
            </Link>
            
            <div class="mt-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl md:text-6xl font-black tracking-tight text-white">
                        {{ event?.data?.title }}
                    </h1>
                    <div class="flex items-center gap-3 mt-3">
                        <span class="flex items-center text-slate-400 text-sm">
                            <svg class="w-4 h-4 mr-1 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ event?.data?.location }}
                        </span>
                        <span class="w-1 h-1 bg-slate-700 rounded-full"></span>
                        <span class="text-slate-400 text-sm">{{ formatDate(event?.data?.date) }}</span>
                    </div>
                </div>
                
                <!-- Badge festif discret -->
                <div class="px-4 py-2 bg-white/5 backdrop-blur-sm rounded-full border border-fuchsia-900/30">
                    <span class="text-xs font-bold bg-gradient-to-r from-fuchsia-400 to-amber-400 text-transparent bg-clip-text uppercase tracking-widest">
                        ✦ Souvenirs exclusifs ✦
                    </span>
                </div>
            </div>
        </div>

        <!-- Galerie : Toujours épurée, mais les interactions ont la couleur de la fête -->
        <main class="max-w-7xl mx-auto px-6 pb-24 pt-12">
            <div v-if="event?.data?.media?.length > 0" class="columns-1 sm:columns-2 lg:columns-3 gap-8 space-y-8">
                <div v-for="media in event.data.media" :key="media.id" 
                     class="relative group rounded-3xl overflow-hidden bg-slate-800/30 break-inside-avoid border border-slate-800 hover:border-fuchsia-900/50 transition-all duration-500 shadow-2xl shadow-black/50">
                    
                    <!-- Overlay festif au survol -->
                    <div class="absolute inset-0 bg-gradient-to-t from-fuchsia-900/40 via-transparent to-amber-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10 pointer-events-none"></div>

                    <img v-if="media.file_type === 'image'" :src="media.file_url" :alt="media.title" 
                         @click="selectedMedia = media"
                         class="w-full h-auto cursor-zoom-in group-hover:scale-105 transition-transform duration-700 ease-out">
                    
                    <div v-else class="relative">
                        <video :src="media.file_url" class="w-full h-auto" muted></video>
                        <div @click="selectedMedia = media" class="absolute inset-0 flex items-center justify-center bg-black/30 cursor-zoom-in backdrop-blur-[2px] group-hover:backdrop-blur-0 transition-all">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-fuchsia-600/90 to-amber-500/90 flex items-center justify-center shadow-lg shadow-fuchsia-900/50 scale-90 group-hover:scale-100 transition-transform">
                                <svg class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.841z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton de téléchargement avec le dégradé festif au survol -->
                    <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-20">
                        <button @click="downloadMedia(media.file_url, media.title)" 
                                class="p-3 bg-slate-900/80 backdrop-blur-md text-white rounded-2xl hover:bg-gradient-to-r hover:from-fuchsia-600 hover:to-amber-500 hover:text-white border border-white/10 hover:border-transparent transition-all shadow-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </button>
                    </div>
                    
                    <!-- Petit badge "Coup de cœur" aléatoire pour le fun -->
                    <div v-if="media.id % 3 === 0" class="absolute bottom-4 left-4 z-20">
                        <span class="text-[10px] font-black bg-amber-400 text-slate-900 px-3 py-1.5 rounded-full shadow-lg uppercase tracking-wider flex items-center gap-1">
                            <span>🔥</span> Populaire
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- État Vide : Cohérent avec l'esprit festif -->
            <div v-else class="text-center py-32 border-2 border-dashed border-slate-800 rounded-[3rem] bg-slate-900/30">
                <div class="w-20 h-20 mx-auto bg-slate-800 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-fuchsia-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-2xl font-black text-white mb-2">L'album est encore vide</p>
                <p class="text-slate-500 font-medium max-w-md mx-auto">
                    Les souvenirs de cet événement seront bientôt disponibles. Revenez un peu plus tard !
                </p>
                <Link :href="route('welcome')" class="inline-block mt-8 text-sm font-bold text-amber-400 hover:text-fuchsia-400 transition">
                    Découvrir d'autres événements →
                </Link>
            </div>
        </main>

        <!-- Lightbox : L'expérience "Cinéma" ultime -->
        <div v-if="selectedMedia" 
             class="fixed inset-0 z-[100] bg-black/98 backdrop-blur-2xl flex items-center justify-center p-4 md:p-8"
             @click.self="selectedMedia = null">
            
            <!-- Bouton Fermer stylisé -->
            <button @click="selectedMedia = null" 
                    class="absolute top-6 right-6 z-10 p-3 bg-white/5 backdrop-blur-md rounded-full text-white/70 hover:text-white hover:bg-fuchsia-600 transition-all border border-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Indicateur de navigation (si vous en ajoutez un jour) -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/40 text-xs font-bold uppercase tracking-[0.3em]">
                Appuyez sur Échap pour fermer
            </div>

            <!-- Contenu Média -->
            <div class="relative max-w-7xl w-full h-full flex items-center justify-center">
                <img v-if="selectedMedia.file_type === 'image'" 
                     :src="selectedMedia.file_url" 
                     :alt="selectedMedia.title" 
                     class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl shadow-fuchsia-900/20 border border-white/5 object-contain">
                
                <video v-else :src="selectedMedia.file_url" controls autoplay 
                       class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl shadow-fuchsia-900/20 border border-white/5"></video>
                       
                <!-- Bouton Télécharger dans la lightbox -->
                <button @click="downloadMedia(selectedMedia.file_url, selectedMedia.title)" 
                        class="absolute bottom-4 right-4 p-4 bg-gradient-to-r from-fuchsia-600 to-amber-500 rounded-2xl text-white shadow-xl hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
            </div>
        </div>
    </div>
</template>