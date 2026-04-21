<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen relative overflow-hidden" style="background: linear-gradient(135deg, #fdf4ff 0%, #fffbeb 50%, #fef3c7 100%)">
        
        <!-- Éléments décoratifs festifs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Confettis -->
            <div class="absolute top-10 right-10 text-4xl opacity-20 animate-bounce">🎈</div>
            <div class="absolute bottom-20 left-20 text-3xl opacity-20 animate-pulse">🎉</div>
            <div class="absolute top-1/3 left-1/4 text-5xl opacity-10 animate-spin" style="animation-duration: 20s;">✨</div>
            <div class="absolute bottom-1/3 right-1/4 text-2xl opacity-20 animate-pulse">🎊</div>
            <div class="absolute top-2/3 right-10 text-4xl opacity-20 animate-bounce" style="animation-delay: 1s;">🥳</div>
            
            <!-- Cercles décoratifs -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-fuchsia-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        </div>

        <Head title="Connexion • FestiVault" />
        
        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-block mb-6">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-fuchsia-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-fuchsia-200 transform -rotate-3 hover:rotate-0 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L2 12l6.857-2.143L11 3z"/></svg>
                        </div>
                        <span class="text-2xl font-black text-slate-800 tracking-tighter">FESTI<span class="text-fuchsia-600">VAULT</span></span>
                    </div>
                </Link>
                
                <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm px-4 py-2 rounded-full border border-amber-200 shadow-sm mb-4">
                    <span class="text-lg">🔐</span>
                    <span class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">Content de vous revoir</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-3 tracking-tight">
                    Bienvenue
                </h1>
                <p class="text-slate-600 text-lg font-medium">
                    Connectez-vous pour gérer vos célébrations
                </p>
            </div>

            <div class="max-w-md mx-auto">
                <!-- Message de statut -->
                <div v-if="status" class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-emerald-500 text-xl">✓</span>
                        <p class="text-sm font-medium text-emerald-700">{{ status }}</p>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/50">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                Adresse email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-fuchsia-400">📧</span>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    class="block w-full pl-11 pr-4 py-3.5 bg-white border rounded-2xl focus:outline-none focus:ring-2 transition-all duration-200 text-slate-800 placeholder-slate-400"
                                    :class="form.errors.email 
                                        ? 'border-rose-300 focus:ring-rose-500 focus:border-rose-500' 
                                        : 'border-amber-200 focus:ring-fuchsia-500 focus:border-fuchsia-500'"
                                    placeholder="jean@exemple.com"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-sm text-rose-600 font-medium flex items-center gap-1">
                                <span>⚠️</span> {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Mot de passe -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                    Mot de passe
                                </label>
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-xs font-bold text-fuchsia-600 hover:text-amber-600 transition-colors"
                                >
                                    Mot de passe oublié ?
                                </Link>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-fuchsia-400">🔒</span>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    class="block w-full pl-11 pr-12 py-3.5 bg-white border rounded-2xl focus:outline-none focus:ring-2 transition-all duration-200 text-slate-800 placeholder-slate-400"
                                    :class="form.errors.password 
                                        ? 'border-rose-300 focus:ring-rose-500 focus:border-rose-500' 
                                        : 'border-amber-200 focus:ring-fuchsia-500 focus:border-fuchsia-500'"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-fuchsia-500 transition-colors"
                                >
                                    {{ showPassword ? '👁️' : '👁️‍🗨️' }}
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-sm text-rose-600 font-medium flex items-center gap-1">
                                <span>⚠️</span> {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Se souvenir de moi -->
                        <div class="flex items-center pt-2">
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="remember"
                                    v-model="form.remember"
                                    class="sr-only peer"
                                />
                                <div 
                                    @click="form.remember = !form.remember"
                                    class="w-5 h-5 border-2 rounded-lg flex items-center justify-center cursor-pointer transition-all"
                                    :class="form.remember 
                                        ? 'bg-gradient-to-r from-fuchsia-600 to-amber-500 border-transparent' 
                                        : 'border-slate-300 bg-white'"
                                >
                                    <svg v-if="form.remember" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <label for="remember" class="ml-3 text-sm font-medium text-slate-600 cursor-pointer" @click="form.remember = !form.remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <!-- Bouton de connexion -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="relative w-full py-4 px-4 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white font-black text-lg rounded-2xl shadow-xl shadow-fuchsia-200 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none overflow-hidden group"
                        >
                            <span v-if="!form.processing" class="relative z-10 flex items-center justify-center gap-2">
                                <span>✨</span> Se connecter <span>✨</span>
                            </span>
                            <span v-else class="relative z-10 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Connexion en cours...
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-fuchsia-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>

                        <!-- Lien d'inscription -->
                        <div class="text-center pt-4">
                            <p class="text-slate-600 font-medium">
                                Pas encore de compte ? 
                                <Link
                                    :href="route('register')"
                                    class="text-fuchsia-600 hover:text-amber-600 font-black hover:underline transition-all duration-200"
                                >
                                    Inscrivez-vous
                                </Link>
                            </p>
                        </div>

                        <!-- Séparateur -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-amber-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-3 bg-white/80 text-slate-400 font-medium">Ou continuer avec</span>
                            </div>
                        </div>

                        <!-- Boutons sociaux -->
                        <div class="grid grid-cols-2 gap-3">
                            <button
                                type="button"
                                class="flex items-center justify-center gap-2 px-4 py-3 border border-amber-200 rounded-xl hover:bg-amber-50 transition-all duration-200 group"
                            >
                                <span class="text-xl group-hover:scale-110 transition-transform">🌐</span>
                                <span class="text-sm font-medium text-slate-600">Google</span>
                            </button>
                            <button
                                type="button"
                                class="flex items-center justify-center gap-2 px-4 py-3 border border-amber-200 rounded-xl hover:bg-amber-50 transition-all duration-200 group"
                            >
                                <span class="text-xl group-hover:scale-110 transition-transform">💼</span>
                                <span class="text-sm font-medium text-slate-600">LinkedIn</span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Message de confiance -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-400 font-medium flex items-center justify-center gap-2">
                        <span>🔒</span> Connexion sécurisée 
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>✨</span> Vos souvenirs vous attendent
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>






