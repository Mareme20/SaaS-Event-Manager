<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    events: Object,
});

const mobileMenuOpen = ref(false);

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};
</script>


<template>
    <Head title="Catalogue des Événements" />

    <div class="min-h-screen bg-[#fdfbf7] font-sans selection:bg-amber-400 selection:text-amber-900">
        
        <!-- Navigation : Ambiance Cocktail -->
        <nav class="sticky top-0 z-50 bg-cream/70 backdrop-blur-xl border-b border-amber-100/50 shadow-sm">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-fuchsia-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-fuchsia-200 rotate-3 transform">
                        <svg class="w-6 h-6 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L2 12l6.857-2.143L11 3z"/></svg>
                    </div>
                    <span class="text-xl font-black text-slate-800 tracking-tighter">FESTI<span class="text-fuchsia-600">VAULT</span></span>
                </div>

                <!-- Desktop links -->
                <div class="hidden md:flex items-center space-x-6">
                    <Link :href="route('pricing')" class="text-sm font-bold text-slate-600 hover:text-fuchsia-700 transition">
                        Tarification
                    </Link>
                    <div v-if="canLogin">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-bold text-slate-700 bg-amber-100/80 px-6 py-2.5 rounded-full hover:bg-amber-200 transition border border-amber-200/50">
                            Tableau de bord
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-slate-600 hover:text-fuchsia-700 transition">
                                Connexion
                            </Link>
                            <Link :href="route('register')" class="ml-6 text-sm font-bold text-white bg-gradient-to-r from-fuchsia-600 to-amber-500 px-6 py-2.5 rounded-full hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition-all duration-300 hover:scale-105">
                                Créer un compte
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Mobile burger -->
                <div class="md:hidden flex items-center gap-3">
                    <Link v-if="!$page.props.auth.user" :href="route('login')" class="text-sm font-bold text-slate-700 bg-white/70 backdrop-blur-sm px-4 py-2.5 rounded-full border border-amber-200/50 shadow-sm hover:bg-white transition">
                        Connexion
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm font-bold text-slate-700 bg-white/70 backdrop-blur-sm px-4 py-2.5 rounded-full border border-amber-200/50 shadow-sm hover:bg-white transition">
                        Tableau de bord
                    </Link>

                    <button
                        type="button"
                        class="p-2.5 rounded-xl bg-white/60 backdrop-blur-sm border border-amber-200/50 shadow-sm hover:bg-white transition"
                        @click="mobileMenuOpen = true"
                        aria-label="Ouvrir le menu"
                    >
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile menu overlay -->
        <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 md:hidden" aria-modal="true" role="dialog">
            <div class="absolute inset-0 bg-slate-900/40" @click="mobileMenuOpen = false"></div>
            <div class="absolute top-0 right-0 left-0 p-4">
                <div class="bg-white rounded-3xl shadow-2xl border border-amber-100 overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b border-amber-100 bg-cream/70">
                        <span class="text-sm font-black text-slate-800">Menu</span>
                        <button
                            type="button"
                            class="p-2.5 rounded-xl hover:bg-amber-50 transition"
                            @click="mobileMenuOpen = false"
                            aria-label="Fermer"
                        >
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-4 space-y-3">
                        <Link
                            :href="route('pricing')"
                            class="block text-sm font-bold text-slate-700 bg-amber-50 px-4 py-3 rounded-2xl border border-amber-100 hover:bg-amber-100 transition"
                            @click="mobileMenuOpen = false"
                        >
                            Tarification
                        </Link>

                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="route('dashboard')"
                                class="block text-sm font-bold text-slate-700 bg-white px-4 py-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition"
                                @click="mobileMenuOpen = false"
                            >
                                Tableau de bord
                            </Link>
                        </template>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="block text-sm font-bold text-slate-700 bg-white px-4 py-3 rounded-2xl border border-slate-100 hover:bg-slate-50 transition"
                                @click="mobileMenuOpen = false"
                            >
                                Connexion
                            </Link>
                            <Link
                                :href="route('register')"
                                class="block text-sm font-bold text-white bg-gradient-to-r from-fuchsia-600 to-amber-500 px-4 py-3 rounded-2xl hover:from-fuchsia-700 hover:to-amber-600 transition"
                                @click="mobileMenuOpen = false"
                            >
                                Créer un compte
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>


        <!-- Hero Section : Confettis & Or -->
        <header class="py-28 relative overflow-hidden bg-gradient-to-br from-amber-50 via-fuchsia-50/30 to-orange-50">
            <!-- Motifs de fête abstraits -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-fuchsia-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-orange-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
            
            <!-- Confettis SVG -->
            <div class="absolute inset-0 pointer-events-none opacity-30">
                <svg class="absolute top-20 left-10 w-8 h-8 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                <svg class="absolute bottom-32 right-20 w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><polygon points="10,2 13,9 20,10 13,11 10,18 7,11 0,10 7,9"/></svg>
                <svg class="absolute top-1/3 right-1/4 w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><rect x="2" y="2" width="16" height="16" rx="2"/></svg>
                <svg class="absolute bottom-1/4 left-1/3 w-5 h-5 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L12 8H18L13 12L15 18L10 14L5 18L7 12L2 8H8L10 2Z"/></svg>
            </div>

            <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
                <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm px-5 py-2 rounded-full border border-amber-200 shadow-sm mb-8">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-fuchsia-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-fuchsia-500"></span>
                    </span>
                    <span class="text-xs font-black text-fuchsia-800 uppercase tracking-[0.3em]">L'Art de la Célébration</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-black text-slate-800 tracking-tighter leading-tight mb-8">
                    Capturez l'éclat de <br> 
                    <span class="relative inline-block">
                        <span class="relative z-10 bg-gradient-to-r from-fuchsia-600 via-amber-500 to-orange-600 text-transparent bg-clip-text">chaque instant</span>
                        <span class="absolute -bottom-2 left-0 w-full h-4 bg-amber-200/60 -rotate-1 rounded-full blur-sm"></span>
                    </span>
                </h1>
                <p class="max-w-2xl mx-auto text-lg text-slate-600 font-medium leading-relaxed">
                    Plongez dans l'ambiance des plus belles réceptions. Retrouvez les photos, vidéos et souvenirs 
                    des événements qui font vibrer la région.
                </p>
            </div>
        </header>

        <!-- Grille du Catalogue : Cartes Festives -->
        <main class="max-w-7xl mx-auto px-6 py-24">
            <div class="flex items-center justify-between mb-16">
                <div>
                    <span class="text-fuchsia-600 text-sm font-black uppercase tracking-[0.3em] border-l-4 border-amber-400 pl-4">Explorez</span>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight mt-2">Les Événements du Moment</h2>
                </div>
                <div class="hidden md:block">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-white px-4 py-2 rounded-full shadow-sm border border-slate-100">✨ Catalogue Public</span>
                </div>
            </div>

            <div v-if="events.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <div v-for="event in events.data" :key="event.id" 
                     class="group bg-white rounded-[3rem] overflow-hidden border border-amber-100 shadow-xl shadow-amber-100/20 hover:shadow-2xl hover:shadow-fuchsia-100/30 transition-all duration-500 hover:-translate-y-2">
                    
                    <!-- En-tête de la carte avec dégradé festif -->
                    <div class="h-72 relative overflow-hidden">
                        <!-- Fond décoratif si pas d'image -->
                        <div class="absolute inset-0 bg-gradient-to-br from-fuchsia-500 via-purple-500 to-amber-400 opacity-90 group-hover:opacity-100 transition duration-700"></div>
                        
                        <img v-if="event.media && event.media.length > 0" 
                             :src="event.media[0].file_url" 
                             class="relative w-full h-full object-cover z-10 group-hover:scale-110 transition-transform duration-700">
                        
                        <!-- Élément festif si pas d'image -->
                        <div v-else class="relative z-10 w-full h-full flex items-center justify-center text-white">
                            <svg class="w-20 h-20 drop-shadow-lg opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        
                        <!-- Overlay et badge date -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent z-20"></div>
                        <div class="absolute bottom-6 left-6 z-30">
                            <span class="px-4 py-2 bg-white/30 backdrop-blur-md rounded-full text-xs font-black text-white uppercase tracking-widest border border-white/40 shadow-lg">
                                {{ formatDate(event.date) }}
                            </span>
                        </div>
                        <div class="absolute top-6 right-6 z-30">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-400 text-white shadow-lg shadow-amber-500/50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                            </span>
                        </div>
                    </div>

                    <!-- Contenu de la carte -->
                    <div class="p-8 relative bg-white">
                        <div class="flex items-center space-x-2 text-fuchsia-600 mb-3">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-xs font-bold uppercase tracking-wider">{{ event.location }}</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 mb-4 leading-tight group-hover:text-fuchsia-700 transition-colors">{{ event.title }}</h3>
                        
                        <!-- Barre de séparation décorative -->
                        <div class="w-12 h-1 bg-gradient-to-r from-amber-400 to-fuchsia-400 rounded-full mb-6"></div>

                        <div class="flex items-center justify-between">
                            <!-- Avatars festifs -->
                            <div class="flex -space-x-2 overflow-hidden">
                                <div v-for="i in 3" :key="i" class="inline-block h-8 w-8 rounded-full ring-2 ring-white shadow-md" 
                                     :class="[
                                        i === 1 ? 'bg-fuchsia-200' : (i === 2 ? 'bg-amber-200' : 'bg-purple-200')
                                     ]">
                                    <div class="w-full h-full rounded-full flex items-center justify-center text-[10px] font-black" 
                                         :class="[
                                            i === 1 ? 'text-fuchsia-700' : (i === 2 ? 'text-amber-700' : 'text-purple-700')
                                         ]">
                                        {{ String.fromCharCode(64 + i) }}
                                    </div>
                                </div>
                                <div class="flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gradient-to-br from-fuchsia-500 to-amber-500 text-[10px] text-white font-black shadow-md">
                                    +{{ Math.floor(Math.random() * 50) + 10 }}
                                </div>
                            </div>
                            
                            <Link :href="route('events.public', event.slug)" 
                                  class="flex items-center text-sm font-black text-fuchsia-600 bg-fuchsia-50 px-5 py-2.5 rounded-full group-hover:bg-fuchsia-600 group-hover:text-white transition-all duration-300 shadow-sm">
                                Voir l'album
                                <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-32 bg-white rounded-[3rem] border-2 border-dashed border-amber-200 shadow-inner">
                <div class="w-20 h-20 mx-auto bg-amber-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <p class="text-slate-500 font-bold italic text-lg">Le catalogue est en préparation... <br> <span class="text-fuchsia-600 not-italic">Revenez vite !</span></p>
            </div>
        </main>

        <!-- Section CTA : L'Appel à la Fête -->
        <section class="py-24 bg-gradient-to-r from-slate-800 via-fuchsia-900 to-slate-800 text-white relative overflow-hidden">
            <!-- Lumières de scène / spots -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-1/4 w-64 h-64 bg-amber-400 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-fuchsia-500 rounded-full blur-[120px]"></div>
            </div>
            
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <div class="inline-block mb-6">
                    <span class="text-6xl">🎉</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black tracking-tighter mb-8 text-white drop-shadow-md">Prêt à faire briller votre événement ?</h2>
                <p class="text-amber-100/90 text-lg mb-12 font-medium max-w-2xl mx-auto">
                    Rejoignez la plateforme qui sublime chaque célébration. Albums partagés, souvenirs inoubliables et gestion simplifiée.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route('register')" class="inline-block bg-white text-fuchsia-800 px-10 py-5 rounded-full font-black text-lg hover:bg-amber-400 hover:text-slate-900 transition-all shadow-2xl shadow-fuchsia-500/30 hover:scale-105 transform">
                        Commencer gratuitement ✨
                    </Link>
                    <Link :href="route('login')" class="inline-block bg-transparent border-2 border-white/30 text-white px-10 py-5 rounded-full font-bold text-lg hover:bg-white/10 backdrop-blur-sm transition-all">
                        Se connecter
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer Festif -->
        <footer class="bg-[#1e1b2e] py-12 border-t border-fuchsia-900/30 text-white/80">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-fuchsia-500 to-amber-400 rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/></svg>
                    </div>
                    <span class="text-sm font-black tracking-tighter text-white">FESTI<span class="text-amber-400">VAULT</span></span>
                </div>
                <p class="text-white/40 text-xs font-bold uppercase tracking-[0.2em]">© 2026 FestiVault • Célébré avec ❤️ au Sénégal</p>
                <div class="flex space-x-8 text-xs font-bold text-white/50 uppercase tracking-widest">
                    <a href="#" class="hover:text-amber-400 transition">Confidentialité</a>
                    <a href="#" class="hover:text-amber-400 transition">Nous contacter</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Animation pour les blobs flottants */
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob {
  animation: blob 7s infinite;
}
.animation-delay-2000 {
  animation-delay: 2s;
}
.animation-delay-4000 {
  animation-delay: 4s;
}
</style>