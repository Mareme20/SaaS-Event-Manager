<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    event: Object,
    tasks: Object,
    members: Object,
});

const showModal = ref(false);
const editingTask = ref(null);

const form = useForm({
    title: '',
    member_id: '',
    description: '',
    due_date: '',
    priority: 'medium',
    status: 'todo',
});

const openModal = (task = null) => {
    editingTask.value = task;
    if (task) {
        form.title = task.title;
        form.member_id = task.member?.id || '';
        form.description = task.description;
        form.due_date = task.due_date;
        form.priority = task.priority;
        form.status = task.status;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    if (editingTask.value) {
        form.patch(route('tasks.update', editingTask.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('tasks.store', props.event.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const updateStatus = (task, newStatus) => {
    useForm({ status: newStatus }).patch(route('tasks.update-status', task.id));
};

const deleteTask = (id) => {
    if (confirm('Supprimer cette tâche ?')) {
        useForm({}).delete(route('tasks.destroy', id));
    }
};

const closeModal = () => {
    showModal.value = false;
    editingTask.value = null;
    form.reset();
};

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'high': return 'bg-rose-100 text-rose-700';
        case 'medium': return 'bg-amber-100 text-amber-700';
        case 'low': return 'bg-emerald-100 text-emerald-700';
        default: return 'bg-slate-100 text-slate-700';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'todo': return 'À faire';
        case 'doing': return 'En cours';
        case 'done': return 'Terminé';
        default: return status;
    }
};

// Filtrage des tâches par colonnes
const todoTasks = computed(() => props.tasks.data.filter(t => t.status === 'todo'));
const doingTasks = computed(() => props.tasks.data.filter(t => t.status === 'doing'));
const doneTasks = computed(() => props.tasks.data.filter(t => t.status === 'done'));

const progress = computed(() => {
    if (props.tasks.data.length === 0) return 0;
    const done = props.tasks.data.filter(t => t.status === 'done').length;
    return Math.round((done / props.tasks.data.length) * 100);
});
</script>

<template>
    <Head :title="'Checklist - ' + event.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <Link :href="route('events.show', event.id)" 
                          class="p-2.5 bg-white border border-slate-100 rounded-xl text-slate-400 hover:text-fuchsia-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">📝 Checklist : {{ event.title }}</h2>
                        <div class="flex items-center mt-1">
                            <div class="w-32 h-2 bg-slate-100 rounded-full mr-3 overflow-hidden">
                                <div class="h-full bg-fuchsia-500 rounded-full transition-all duration-1000" :style="{ width: progress + '%' }"></div>
                            </div>
                            <span class="text-xs font-black text-fuchsia-600 uppercase tracking-widest">{{ progress }}% complété</span>
                        </div>
                    </div>
                </div>
                <button @click="openModal()" 
                        class="bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white px-8 py-3 rounded-2xl text-sm font-black shadow-xl shadow-fuchsia-200 transition-all">
                    + Nouvelle Tâche
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Colonne À FAIRE -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-4">
                        <h3 class="font-black text-slate-400 uppercase tracking-[0.2em] text-[10px] flex items-center">
                            <span class="w-2 h-2 bg-slate-300 rounded-full mr-2"></span> À FAIRE ({{ todoTasks.length }})
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div v-for="task in todoTasks" :key="task.id" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getPriorityClass(task.priority)" class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider">
                                    {{ task.priority }}
                                </span>
                                <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openModal(task)" class="p-1.5 text-slate-400 hover:text-fuchsia-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                    <button @click="deleteTask(task.id)" class="p-1.5 text-slate-400 hover:text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 leading-tight mb-2">{{ task.title }}</h4>
                            <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ task.description }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <div class="flex items-center">
                                    <div v-if="task.member" class="w-6 h-6 bg-fuchsia-100 rounded-full flex items-center justify-center text-[10px] font-black text-fuchsia-600 mr-2" :title="task.member.name">
                                        {{ task.member.name.charAt(0) }}
                                    </div>
                                    <span class="text-[10px] font-medium text-slate-400">{{ task.due_date || 'Pas de date' }}</span>
                                </div>
                                <button @click="updateStatus(task, 'doing')" class="text-[10px] font-black text-fuchsia-600 hover:text-fuchsia-700 uppercase tracking-widest">Démarrer →</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne EN COURS -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-4">
                        <h3 class="font-black text-amber-500 uppercase tracking-[0.2em] text-[10px] flex items-center">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span> EN COURS ({{ doingTasks.length }})
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div v-for="task in doingTasks" :key="task.id" class="bg-white p-6 rounded-[2rem] border border-amber-100 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getPriorityClass(task.priority)" class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider">
                                    {{ task.priority }}
                                </span>
                                <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openModal(task)" class="p-1.5 text-slate-400 hover:text-fuchsia-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                    <button @click="deleteTask(task.id)" class="p-1.5 text-slate-400 hover:text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 leading-tight mb-2">{{ task.title }}</h4>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                <button @click="updateStatus(task, 'todo')" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">← Reculer</button>
                                <button @click="updateStatus(task, 'done')" class="text-[10px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest">Terminer ✓</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne TERMINÉ -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between px-4">
                        <h3 class="font-black text-emerald-500 uppercase tracking-[0.2em] text-[10px] flex items-center">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2"></span> TERMINÉ ({{ doneTasks.length }})
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div v-for="task in doneTasks" :key="task.id" class="bg-slate-50 p-6 rounded-[2rem] border border-transparent opacity-75 grayscale hover:grayscale-0 hover:opacity-100 transition-all group">
                            <h4 class="font-bold text-slate-800 leading-tight mb-2 line-through">{{ task.title }}</h4>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                                <button @click="updateStatus(task, 'doing')" class="text-[10px] font-black text-slate-400 hover:text-fuchsia-600 uppercase tracking-widest">← Reprendre</button>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Fait
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Tâche -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-md">
            <div class="bg-white rounded-[3rem] w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100">
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white relative">
                    <div class="absolute top-0 right-0 p-4 opacity-20">📝</div>
                    <h3 class="text-2xl font-black relative z-10">{{ editingTask ? 'Modifier Tâche' : 'Nouvelle Tâche' }}</h3>
                    <p class="text-slate-400 text-sm mt-1 relative z-10">Organisez les étapes de votre événement</p>
                </div>
                <form @submit.prevent="submit" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <input v-model="form.title" type="text" 
                               class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                               placeholder="Qu'y a-t-il à faire ?" required>
                        
                        <select v-model="form.member_id" 
                                class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                            <option value="">Personne assignée (optionnel)</option>
                            <option v-for="member in members.data" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </select>

                        <div class="grid grid-cols-2 gap-4">
                            <select v-model="form.priority" 
                                    class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                                <option value="low">Priorité Basse</option>
                                <option value="medium">Priorité Moyenne</option>
                                <option value="high">Priorité Haute</option>
                            </select>
                            <input v-model="form.due_date" type="date" 
                                   class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition">
                        </div>

                        <textarea v-model="form.description" 
                                  class="block w-full bg-slate-50 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition" 
                                  placeholder="Plus de détails..." rows="3"></textarea>
                    </div>
                    <button type="submit" :disabled="form.processing" 
                            class="w-full bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 transition">
                        {{ editingTask ? 'Mettre à jour' : 'Ajouter à la liste' }}
                    </button>
                    <button @click="closeModal" type="button" class="w-full text-slate-400 text-xs font-bold uppercase tracking-widest mt-2">
                        Annuler
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
