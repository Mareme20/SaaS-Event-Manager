<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    events: Array
});

const currentDate = ref(new Date());

const daysInMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const date = new Date(year, month, 1);
    const days = [];
    
    // Fill in days from previous month
    const firstDayIndex = date.getDay();
    const prevMonthLastDate = new Date(year, month, 0).getDate();
    for (let i = firstDayIndex; i > 0; i--) {
        days.push({
            date: new Date(year, month - 1, prevMonthLastDate - i + 1),
            currentMonth: false
        });
    }
    
    // Fill in days from current month
    while (date.getMonth() === month) {
        days.push({
            date: new Date(date),
            currentMonth: true
        });
        date.setDate(date.getDate() + 1);
    }
    
    // Fill in days from next month
    const lastDayIndex = new Date(year, month + 1, 0).getDay();
    const nextDays = 6 - lastDayIndex;
    for (let i = 1; i <= nextDays; i++) {
        days.push({
            date: new Date(year, month + 1, i),
            currentMonth: false
        });
    }
    
    return days;
});

const monthName = computed(() => {
    return currentDate.value.toLocaleString('fr-FR', { month: 'long', year: 'numeric' });
});

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const getEventsForDay = (date) => {
    return props.events.filter(event => {
        const eventDate = new Date(event.date);
        return eventDate.getDate() === date.getDate() &&
               eventDate.getMonth() === date.getMonth() &&
               eventDate.getFullYear() === date.getFullYear();
    });
};

const isToday = (date) => {
    const today = new Date();
    return date.getDate() === today.getDate() &&
           date.getMonth() === today.getMonth() &&
           date.getFullYear() === today.getFullYear();
};
</script>

<template>
    <Head title="Calendrier des Événements" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                        Calendrier 📅
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 font-medium mt-1">
                        Visualisez tous vos événements du mois
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <Link :href="route('events.index')" 
                          class="px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm">
                        Vue Liste
                    </Link>
                    <Link :href="route('events.create')" 
                          class="px-5 py-2.5 bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white rounded-xl text-sm font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 dark:shadow-none transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nouvelle fête
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-amber-100 dark:border-slate-800 overflow-hidden">
                <!-- Calendar Header -->
                <div class="px-6 py-5 border-b border-amber-100 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-fuchsia-50/30 to-amber-50/30 dark:from-slate-800 dark:to-slate-900">
                    <h2 class="text-xl font-black text-slate-800 dark:text-white capitalize">
                        {{ monthName }}
                    </h2>
                    <div class="flex items-center space-x-2">
                        <button @click="prevMonth" class="p-2 hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="currentDate = new Date()" class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-fuchsia-600 transition-colors">
                            Aujourd'hui
                        </button>
                        <button @click="nextMonth" class="p-2 hover:bg-white dark:hover:bg-slate-800 rounded-lg transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 border-b border-amber-50 dark:border-slate-800">
                    <div v-for="day in ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']" :key="day" 
                         class="py-3 text-center text-xs font-black text-slate-400 uppercase tracking-wider">
                        {{ day }}
                    </div>
                </div>
                
                <div class="grid grid-cols-7 grid-rows-6 h-[700px]">
                    <div v-for="(day, index) in daysInMonth" :key="index" 
                         class="border-r border-b border-amber-50 dark:border-slate-800 p-2 relative group hover:bg-amber-50/20 dark:hover:bg-slate-800/30 transition-colors"
                         :class="{ 'bg-slate-50/50 dark:bg-slate-950/30': !day.currentMonth }">
                        
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-bold" 
                                  :class="[
                                      day.currentMonth ? 'text-slate-700 dark:text-slate-300' : 'text-slate-300 dark:text-slate-600',
                                      isToday(day.date) ? 'w-7 h-7 bg-fuchsia-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-fuchsia-200' : ''
                                  ]">
                                {{ day.date.getDate() }}
                            </span>
                        </div>

                        <!-- Events on this day -->
                        <div class="mt-2 space-y-1 overflow-y-auto max-h-[80px]">
                            <Link v-for="event in getEventsForDay(day.date)" :key="event.id"
                                  :href="route('events.show', event.id)"
                                  class="block px-2 py-1 text-[10px] font-black rounded-lg bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-700 dark:text-fuchsia-300 truncate hover:scale-105 transition-transform">
                                {{ event.title }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
