<script setup>
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const plans = [
    {
        name: 'Débutant',
        icon: '🎈',
        price: '0',
        period: 'gratuit',
        description: 'Parfait pour découvrir la magie de l\'organisation.',
        features: [
            '1 Événement actif',
            '5 Membres maximum',
            'Gestion des tâches basique',
            'Partage de souvenirs',
            'Rapports PDF simples',
        ],
        buttonText: 'Commencer gratuitement',
        buttonLink: '/register',
        popular: false,
        color: 'from-amber-400 to-amber-500',
        bgColor: 'bg-amber-50',
        textColor: 'text-amber-700',
        borderColor: 'border-amber-200',
    },
    {
        name: 'Pro',
        icon: '🎉',
        price: '9 900',
        period: 'FCFA/mois',
        description: 'Pour les organisateurs qui veulent briller.',
        features: [
            '10 Événements actifs',
            'Membres illimités',
            'Dashboard Analytics avancé',
            'Vue Calendrier interactive',
            'QR Code personnalisé',
            'Support par email prioritaire',
        ],
        buttonText: 'Passer au Pro ✨',
        buttonLink: '/register?plan=pro',
        popular: true,
        color: 'from-fuchsia-600 to-fuchsia-500',
        bgColor: 'bg-fuchsia-50',
        textColor: 'text-fuchsia-700',
        borderColor: 'border-fuchsia-200',
    },
    {
        name: 'Business',
        icon: '👑',
        price: '24 900',
        period: 'FCFA/mois',
        description: 'La solution ultime pour les professionnels.',
        features: [
            'Événements illimités',
            'Membres illimités',
            'Toutes les fonctionnalités Pro',
            'Paiement en ligne intégré',
            'Support prioritaire 24/7',
            'Marque blanche (bientôt)',
            'API & Webhooks',
        ],
        buttonText: 'Contacter la vente',
        buttonLink: '/register?plan=business',
        popular: false,
        color: 'from-purple-600 to-purple-500',
        bgColor: 'bg-purple-50',
        textColor: 'text-purple-700',
        borderColor: 'border-purple-200',
    }
];
</script>

<template>
    <Head title="Tarification • FestiVault" />

    <GuestLayout>
        <!-- Section Hero -->
        <div class="relative overflow-hidden bg-gradient-to-br from-fuchsia-50 via-amber-50 to-purple-50 py-24 sm:py-32">
            <!-- Éléments décoratifs -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute top-10 left-10 text-5xl opacity-20 animate-bounce">🎈</div>
                <div class="absolute top-20 right-20 text-4xl opacity-20 animate-pulse">🎉</div>
                <div class="absolute bottom-20 left-1/4 text-6xl opacity-10 animate-spin" style="animation-duration: 20s;">✨</div>
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-fuchsia-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm px-5 py-2 rounded-full border border-amber-200 shadow-sm mb-6">
                        <span class="text-lg">💎</span>
                        <span class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">Des offres adaptées à vos besoins</span>
                    </div>
                    
                    <h2 class="text-base font-black leading-7 text-fuchsia-600 uppercase tracking-widest">
                        Tarification
                    </h2>
                    <p class="mt-4 text-4xl font-black tracking-tight text-slate-800 sm:text-5xl">
                        Investissez dans la réussite <br>
                        <span class="bg-gradient-to-r from-fuchsia-600 via-amber-500 to-purple-600 text-transparent bg-clip-text">
                            de vos événements
                        </span>
                    </p>
                </div>
                <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-slate-600 font-medium">
                    Des prix simples et transparents. Pas de frais cachés. <br class="hidden sm:block">
                    <span class="text-fuchsia-600 font-bold">Économisez 20%</span> avec un abonnement annuel.
                </p>

                <!-- Grille des forfaits -->
                <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                    <div v-for="plan in plans" :key="plan.name" 
                         class="relative flex flex-col justify-between rounded-3xl bg-white/80 backdrop-blur-sm p-8 ring-1 ring-slate-200 xl:p-10 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 group"
                         :class="{
                             'lg:z-10 lg:scale-105 ring-2 ring-fuchsia-400 shadow-2xl shadow-fuchsia-100': plan.popular,
                             'border border-slate-200': !plan.popular
                         }">
                        
                        <!-- Badge populaire -->
                        <div v-if="plan.popular" class="absolute -top-5 left-1/2 -translate-x-1/2 z-20">
                            <span class="inline-flex items-center gap-1 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-6 py-2 rounded-full text-xs font-black uppercase tracking-wider shadow-xl shadow-fuchsia-200">
                                <span>⭐</span> Plus populaire <span>⭐</span>
                            </span>
                        </div>

                        <div>
                            <!-- En-tête du plan -->
                            <div class="flex items-center justify-between gap-x-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-md"
                                         :class="plan.bgColor">
                                        {{ plan.icon }}
                                    </div>
                                    <h3 class="text-xl font-black text-slate-800">
                                        {{ plan.name }}
                                    </h3>
                                </div>
                            </div>
                            
                            <p class="text-sm leading-6 text-slate-500 font-medium">{{ plan.description }}</p>
                            
                            <!-- Prix -->
                            <div class="mt-6 flex items-baseline gap-x-2">
                                <span class="text-5xl font-black tracking-tight text-slate-800">{{ plan.price }}</span>
                                <span class="text-sm font-bold text-slate-500 uppercase">{{ plan.period }}</span>
                            </div>
                            
                            <!-- Fonctionnalités -->
                            <ul role="list" class="mt-8 space-y-4">
                                <li v-for="feature in plan.features" :key="feature" 
                                    class="flex gap-x-3 items-start group/item">
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center mt-0.5"
                                         :class="plan.bgColor">
                                        <svg class="w-3 h-3" :class="plan.textColor" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-medium text-slate-600 group-hover/item:text-slate-800 transition-colors">
                                        {{ feature }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Bouton d'action -->
                        <Link :href="plan.buttonLink" 
                              class="mt-8 block rounded-2xl py-4 px-6 text-center text-sm font-black leading-6 transition-all duration-300 shadow-lg relative overflow-hidden group/btn"
                              :class="plan.popular 
                                  ? 'bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white hover:from-fuchsia-700 hover:to-amber-600 shadow-fuchsia-200' 
                                  : 'bg-white border-2 hover:text-white hover:border-transparent ' + plan.borderColor + ' ' + plan.textColor">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                {{ plan.buttonText }}
                                <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                            <!-- Overlay de hover pour les boutons non populaires -->
                            <span v-if="!plan.popular" class="absolute inset-0 bg-gradient-to-r from-fuchsia-600 to-amber-500 opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300"></span>
                        </Link>
                    </div>
                </div>

                <!-- Section FAQ rapide -->
                <div class="mt-24 text-center">
                    <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm px-5 py-2 rounded-full border border-amber-200 shadow-sm mb-6">
                        <span class="text-lg">❓</span>
                        <span class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">Des questions ?</span>
                    </div>
                    <p class="text-slate-600 font-medium">
                        Consultez notre 
                        <Link href="/faq" class="text-fuchsia-600 font-bold hover:text-amber-600 transition-colors underline underline-offset-4">
                            FAQ
                        </Link> 
                        ou 
                        <Link href="/contact" class="text-fuchsia-600 font-bold hover:text-amber-600 transition-colors underline underline-offset-4">
                            contactez-nous
                        </Link>
                    </p>
                </div>
            </div>
        </div>

        <!-- Section Garantie -->
        <div class="bg-white py-16">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">🔄</span>
                        </div>
                        <h3 class="font-black text-slate-800 mb-2">Satisfait ou remboursé</h3>
                        <p class="text-sm text-slate-500 font-medium">30 jours pour changer d'avis. Sans conditions.</p>
                    </div>
                    <div class="p-8">
                        <div class="w-16 h-16 bg-fuchsia-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">🔒</span>
                        </div>
                        <h3 class="font-black text-slate-800 mb-2">Paiement sécurisé</h3>
                        <p class="text-sm text-slate-500 font-medium">Vos données sont protégées et cryptées.</p>
                    </div>
                    <div class="p-8">
                        <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">💬</span>
                        </div>
                        <h3 class="font-black text-slate-800 mb-2">Support réactif</h3>
                        <p class="text-sm text-slate-500 font-medium">Une équipe dédiée à votre écoute.</p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>