<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Bar, Line, Doughnut } from 'vue-chartjs';
import { 
    Chart as ChartJS, 
    Title, Tooltip, Legend, 
    BarElement, CategoryScale, LinearScale, 
    PointElement, LineElement, ArcElement 
} from 'chart.js';

ChartJS.register(
    Title, Tooltip, Legend, 
    BarElement, CategoryScale, LinearScale, 
    PointElement, LineElement, ArcElement
);

const props = defineProps({
    event: Object,
    stats: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('fr-FR').format(value) + ' ' + props.event.currency;
};

// 1. Graphique Comparatif (Prévisionnel vs Réel vs Cotisations)
const comparisonData = computed(() => ({
    labels: ['Finances de l\'événement'],
    datasets: [
        {
            label: 'Budget Prévisionnel',
            backgroundColor: '#94a3b8',
            data: [props.stats.budget_estimated]
        },
        {
            label: 'Cotisations Récoltées',
            backgroundColor: '#10b981',
            data: [props.stats.total_contributions]
        },
        {
            label: 'Dépenses Réelles',
            backgroundColor: '#f43f5e',
            data: [props.stats.total_expenses]
        }
    ]
}));

// 2. Graphique Tendances (Cotisations par mois)
const trendData = computed(() => ({
    labels: props.stats.contribution_trends.map(t => t.month),
    datasets: [{
        label: 'Montant des cotisations',
        borderColor: '#059669',
        backgroundColor: '#ecfdf5',
        fill: true,
        tension: 0.4,
        data: props.stats.contribution_trends.map(t => t.total)
    }]
}));

// 3. Répartition des dépenses
const distributionData = computed(() => ({
    labels: props.stats.expense_distribution.map(d => d.category || 'Général'),
    datasets: [{
        backgroundColor: ['#c026d3', '#fbbf24', '#14b8a6', '#f43f5e', '#6366f1'],
        data: props.stats.expense_distribution.map(d => d.total)
    }]
}));

const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { font: { family: 'Outfit', weight: 'bold' } }
        }
    }
};
</script>

<template>
    <Head :title="'Rapports - ' + event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <Link :href="route('events.show', event.id)" 
                          class="p-2.5 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-fuchsia-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">📊 Analyse Financière</h2>
                        <p class="text-sm text-slate-500 font-medium">{{ event.title }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a :href="route('events.export-csv', event.id)" 
                       class="bg-slate-800 text-white px-6 py-3 rounded-2xl text-sm font-black hover:bg-slate-900 transition-all flex items-center shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Exporter CSV
                    </a>
                </div>
            </div>
        </template>

        <div class="py-12">
            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white p-8 rounded-[2.5rem] border border-emerald-100 shadow-sm">
                    <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-2">Taux de Recouvrement</p>
                    <p class="text-4xl font-black text-slate-800">
                        {{ Math.round((stats.total_contributions / stats.budget_estimated) * 100) || 0 }}%
                    </p>
                    <p class="text-xs text-slate-400 mt-2">Par rapport au budget prévu</p>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-amber-100 shadow-sm">
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-2">Marge de Sécurité</p>
                    <p class="text-4xl font-black text-slate-800">
                        {{ formatCurrency(stats.total_contributions - stats.total_expenses) }}
                    </p>
                    <p class="text-xs text-slate-400 mt-2">Argent disponible en caisse</p>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] border border-fuchsia-100 shadow-sm">
                    <p class="text-[10px] font-black text-fuchsia-500 uppercase tracking-widest mb-2">Consommation Budget</p>
                    <p class="text-4xl font-black text-slate-800">
                        {{ Math.round((stats.total_expenses / stats.budget_estimated) * 100) || 0 }}%
                    </p>
                    <p class="text-xs text-slate-400 mt-2">Dépenses vs Prévisions</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Comparaison Globale -->
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm min-h-[400px]">
                    <h3 class="text-lg font-black text-slate-800 mb-8">⚖️ Vue d'ensemble des flux</h3>
                    <div class="h-64">
                        <Bar :data="comparisonData" :options="commonOptions" />
                    </div>
                </div>

                <!-- Tendances Temporelles -->
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm min-h-[400px]">
                    <h3 class="text-lg font-black text-slate-800 mb-8">📈 Évolution des Cotisations</h3>
                    <div class="h-64">
                        <Line :data="trendData" :options="commonOptions" />
                    </div>
                </div>

                <!-- Répartition Dépenses -->
                <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm min-h-[400px]">
                    <h3 class="text-lg font-black text-slate-800 mb-8">🍰 Répartition des Dépenses</h3>
                    <div class="h-64">
                        <Doughnut :data="distributionData" :options="commonOptions" />
                    </div>
                </div>

                <!-- Détails et Conseils -->
                <div class="bg-gradient-to-br from-fuchsia-600 to-amber-500 p-10 rounded-[3rem] text-white shadow-xl flex flex-col justify-center">
                    <h3 class="text-2xl font-black mb-4">💡 État de santé financier</h3>
                    <div v-if="stats.total_contributions >= stats.total_expenses" class="space-y-4">
                        <p class="text-fuchsia-50 font-medium">Félicitations ! Votre événement est **excédentaire**. Les cotisations couvrent actuellement toutes les dépenses engagées.</p>
                        <div class="p-4 bg-white/10 rounded-2xl border border-white/20 text-sm">
                            Vous disposez d'un surplus de **{{ formatCurrency(stats.total_contributions - stats.total_expenses) }}**.
                        </div>
                    </div>
                    <div v-else class="space-y-4">
                        <p class="text-rose-100 font-medium">Attention ! Votre événement est en **déficit**. Les dépenses dépassent les cotisations récoltées.</p>
                        <div class="p-4 bg-white/10 rounded-2xl border border-white/20 text-sm">
                            Il manque **{{ formatCurrency(stats.total_expenses - stats.total_contributions) }}** pour équilibrer les comptes.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
