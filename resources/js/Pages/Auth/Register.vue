<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
const acceptTerms = ref(false);

const submit = () => {
    if (!acceptTerms.value) return;
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return 0;
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
});

const passwordStrengthText = computed(() => {
    const strength = passwordStrength.value;
    if (strength === 0) return '';
    if (strength <= 2) return 'Faible';
    if (strength === 3) return 'Moyen';
    return 'Fort';
});

const passwordStrengthColor = computed(() => {
    const strength = passwordStrength.value;
    if (strength <= 2) return '#ef4444';
    if (strength === 3) return '#f59e0b';
    return '#10b981';
});

const getStrengthBarWidth = computed(() => {
    return `${(passwordStrength.value / 4) * 100}%`;
});
</script>

<template>
    <div class="min-h-screen relative overflow-hidden" style="background: linear-gradient(135deg, #fdf4ff 0%, #fffbeb 50%, #fef3c7 100%)">
        
        <!-- Éléments décoratifs festifs -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Confettis -->
            <div class="absolute top-10 left-10 text-4xl opacity-20 animate-bounce">🎈</div>
            <div class="absolute top-20 right-20 text-3xl opacity-20 animate-pulse">🎉</div>
            <div class="absolute bottom-20 left-1/4 text-5xl opacity-10 animate-spin" style="animation-duration: 20s;">✨</div>
            <div class="absolute top-1/3 right-1/4 text-2xl opacity-20 animate-pulse">🎊</div>
            <div class="absolute bottom-40 right-10 text-4xl opacity-20 animate-bounce" style="animation-delay: 1s;">🥳</div>
            
            <!-- Cercles décoratifs -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-fuchsia-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-amber-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        </div>

        <Head title="Inscription • FestiVault" />
        
        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-block mb-6">
                    <div class="inline-flex items-center space-x-2">
                        <div class="w-12 h-12 bg-gradient-to-br from-fuchsia-600 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-fuchsia-200 transform rotate-3 hover:rotate-0 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.143-7.714L2 12l6.857-2.143L11 3z"/></svg>
                        </div>
                        <span class="text-2xl font-black text-slate-800 tracking-tighter">FESTI<span class="text-fuchsia-600">VAULT</span></span>
                    </div>
                </Link>
                
                <div class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-sm px-4 py-2 rounded-full border border-amber-200 shadow-sm mb-4">
                    <span class="text-lg">🎪</span>
                    <span class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">Rejoignez l'aventure</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black text-slate-800 mb-3 tracking-tight">
                    Créez votre compte
                </h1>
                <p class="text-slate-600 text-lg font-medium">
                    Et commencez à créer des souvenirs inoubliables
                </p>
            </div>

            <div class="max-w-md mx-auto">
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/50">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nom -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                Nom complet
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-fuchsia-400">👤</span>
                                </div>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    class="block w-full pl-11 pr-4 py-3.5 bg-white border rounded-2xl focus:outline-none focus:ring-2 transition-all duration-200 text-slate-800 placeholder-slate-400"
                                    :class="form.errors.name 
                                        ? 'border-rose-300 focus:ring-rose-500 focus:border-rose-500' 
                                        : 'border-amber-200 focus:ring-fuchsia-500 focus:border-fuchsia-500'"
                                    placeholder="Jean Dupont"
                                />
                            </div>
                            <p v-if="form.errors.name" class="text-sm text-rose-600 font-medium flex items-center gap-1">
                                <span>⚠️</span> {{ form.errors.name }}
                            </p>
                        </div>

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
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                Mot de passe
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-fuchsia-400">🔒</span>
                                </div>
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
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
                            
                            <!-- Force du mot de passe -->
                            <div v-if="form.password" class="mt-3 space-y-2">
                                <div class="flex gap-1.5">
                                    <div 
                                        v-for="i in 4" 
                                        :key="i"
                                        class="h-2 flex-1 rounded-full transition-all duration-300"
                                        :style="{ 
                                            backgroundColor: i <= passwordStrength 
                                                ? passwordStrengthColor 
                                                : '#e2e8f0'
                                        }"
                                    ></div>
                                </div>
                                <p class="text-xs font-medium" :style="{ color: passwordStrengthColor }">
                                    <span class="font-black">{{ passwordStrengthText }}</span>
                                    <span v-if="passwordStrength < 4" class="text-slate-500 ml-1">
                                        — min. 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial
                                    </span>
                                    <span v-else class="text-emerald-600 ml-1">— Parfait ! 🎉</span>
                                </p>
                            </div>
                            
                            <p v-if="form.errors.password" class="text-sm text-rose-600 font-medium flex items-center gap-1">
                                <span>⚠️</span> {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider">
                                Confirmer le mot de passe
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-fuchsia-400">🔒</span>
                                </div>
                                <input
                                    :type="showConfirmPassword ? 'text' : 'password'"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    class="block w-full pl-11 pr-12 py-3.5 bg-white border rounded-2xl focus:outline-none focus:ring-2 transition-all duration-200 text-slate-800 placeholder-slate-400"
                                    :class="form.errors.password_confirmation 
                                        ? 'border-rose-300 focus:ring-rose-500 focus:border-rose-500' 
                                        : 'border-amber-200 focus:ring-fuchsia-500 focus:border-fuchsia-500'"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-fuchsia-500 transition-colors"
                                >
                                    {{ showConfirmPassword ? '👁️' : '👁️‍🗨️' }}
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" class="text-sm text-rose-600 font-medium flex items-center gap-1">
                                <span>⚠️</span> {{ form.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Conditions d'utilisation -->
                        <div class="flex items-start space-x-3 pt-2">
                            <div class="relative">
                                <input
                                    type="checkbox"
                                    id="terms"
                                    v-model="acceptTerms"
                                    class="sr-only peer"
                                />
                                <div 
                                    @click="acceptTerms = !acceptTerms"
                                    class="w-5 h-5 border-2 rounded-lg flex items-center justify-center cursor-pointer transition-all"
                                    :class="acceptTerms 
                                        ? 'bg-gradient-to-r from-fuchsia-600 to-amber-500 border-transparent' 
                                        : 'border-slate-300 bg-white'"
                                >
                                    <svg v-if="acceptTerms" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                            <label for="terms" class="text-sm text-slate-600 font-medium cursor-pointer" @click="acceptTerms = !acceptTerms">
                                J'accepte les 
                                <a href="#" class="text-fuchsia-600 hover:text-amber-600 font-bold transition-colors">conditions d'utilisation</a> 
                                et la 
                                <a href="#" class="text-fuchsia-600 hover:text-amber-600 font-bold transition-colors">politique de confidentialité</a>
                            </label>
                        </div>

                        <!-- Bouton d'inscription -->
                        <button
                            type="submit"
                            :disabled="form.processing || !acceptTerms"
                            class="relative w-full py-4 px-4 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white font-black text-lg rounded-2xl shadow-xl shadow-fuchsia-200 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none overflow-hidden group"
                        >
                            <span v-if="!form.processing" class="relative z-10 flex items-center justify-center gap-2">
                                <span>🎉</span> Créer mon compte <span>🎉</span>
                            </span>
                            <span v-else class="relative z-10 flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Création de votre compte...
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-500 to-fuchsia-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>

                        <!-- Lien de connexion -->
                        <div class="text-center pt-4">
                            <p class="text-slate-600 font-medium">
                                Vous avez déjà un compte ? 
                                <Link
                                    :href="route('login')"
                                    class="text-fuchsia-600 hover:text-amber-600 font-black hover:underline transition-all duration-200"
                                >
                                    Connectez-vous
                                </Link>
                            </p>
                        </div>
                    </form>
                </div>
                
                <!-- Message de confiance -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-slate-400 font-medium flex items-center justify-center gap-2">
                        <span>🔒</span> Vos données sont sécurisées 
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>✨</span> Inscription gratuite
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>