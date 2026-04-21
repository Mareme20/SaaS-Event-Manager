<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    message: String
});
</script>

<template>
    <Head title="Paiement Réussi" />

    <AuthenticatedLayout>
        <div class="py-20 flex flex-col items-center justify-center text-center">
            <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-8 animate-bounce">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            
            <h2 class="text-4xl font-black text-slate-800 mb-4">Paiement Validé !</h2>
            <p class="text-slate-500 max-w-md mx-auto mb-10">{{ message }}</p>
            
            <Link :href="route('dashboard')" class="bg-slate-800 text-white px-10 py-4 rounded-2xl font-black shadow-xl hover:bg-slate-900 transition-all">
                Retour au Tableau de Bord
            </Link>
        </div>
    </AuthenticatedLayout>
</template>
