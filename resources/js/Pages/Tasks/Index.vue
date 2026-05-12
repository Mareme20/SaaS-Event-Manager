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

const commentForm = useForm({
    content: '',
    commentable_id: null,
    commentable_type: 'App\\Models\\Task',
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
        commentForm.commentable_id = task.id;
    } else {
        form.reset();
        commentForm.reset();
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

const submitComment = () => {
    commentForm.post(route('comments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset('content');
            // Refresh editingTask with new comments from props
            const updatedTask = props.tasks.data.find(t => t.id === editingTask.value.id);
            if (updatedTask) editingTask.value = updatedTask;
        }
    });
};

const deleteComment = (id) => {
    if (confirm('Supprimer ce commentaire ?')) {
        useForm({}).delete(route('comments.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                const updatedTask = props.tasks.data.find(t => t.id === editingTask.value.id);
                if (updatedTask) editingTask.value = updatedTask;
            }
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
                        <h3 class="font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] text-[10px] flex items-center">
                            <span class="w-2 h-2 bg-slate-300 dark:bg-slate-700 rounded-full mr-2"></span> À FAIRE ({{ todoTasks.length }})
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div v-for="task in todoTasks" :key="task.id" class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getPriorityClass(task.priority)" class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider">
                                    {{ task.priority }}
                                </span>
                                <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openModal(task)" class="p-1.5 text-slate-400 hover:text-fuchsia-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                    <button @click="deleteTask(task.id)" class="p-1.5 text-slate-400 hover:text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white leading-tight mb-2">{{ task.title }}</h4>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">{{ task.description }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-800">
                                <div class="flex items-center space-x-3">
                                    <div v-if="task.member" class="w-6 h-6 bg-fuchsia-100 dark:bg-fuchsia-900/30 rounded-full flex items-center justify-center text-[10px] font-black text-fuchsia-600 dark:text-fuchsia-400" :title="task.member.name">
                                        {{ task.member.name.charAt(0) }}
                                    </div>
                                    <div v-if="task.comments_count > 0" class="flex items-center text-[10px] font-bold text-slate-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        {{ task.comments_count }}
                                    </div>
                                </div>
                                <button @click="updateStatus(task, 'doing')" class="text-[10px] font-black text-fuchsia-600 dark:text-fuchsia-400 hover:text-fuchsia-700 dark:hover:text-fuchsia-300 uppercase tracking-widest">Démarrer →</button>
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
                        <div v-for="task in doingTasks" :key="task.id" class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] border border-amber-100 dark:border-amber-900/30 shadow-sm hover:shadow-md transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getPriorityClass(task.priority)" class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider">
                                    {{ task.priority }}
                                </span>
                                <div class="flex opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openModal(task)" class="p-1.5 text-slate-400 hover:text-fuchsia-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                                    <button @click="deleteTask(task.id)" class="p-1.5 text-slate-400 hover:text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white leading-tight mb-2">{{ task.title }}</h4>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-50 dark:border-slate-800">
                                <div class="flex items-center space-x-3">
                                    <div v-if="task.member" class="w-6 h-6 bg-fuchsia-100 dark:bg-fuchsia-900/30 rounded-full flex items-center justify-center text-[10px] font-black text-fuchsia-600 dark:text-fuchsia-400" :title="task.member.name">
                                        {{ task.member.name.charAt(0) }}
                                    </div>
                                    <div v-if="task.comments_count > 0" class="flex items-center text-[10px] font-bold text-slate-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                        {{ task.comments_count }}
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="updateStatus(task, 'todo')" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">← Reculer</button>
                                    <button @click="updateStatus(task, 'done')" class="text-[10px] font-black text-emerald-600 hover:text-emerald-700 uppercase tracking-widest">Terminer ✓</button>
                                </div>
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
                        <div v-for="task in doneTasks" :key="task.id" class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-[2rem] border border-transparent opacity-75 grayscale hover:grayscale-0 hover:opacity-100 transition-all group">
                            <h4 class="font-bold text-slate-800 dark:text-slate-300 leading-tight mb-2 line-through">{{ task.title }}</h4>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
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
            <div class="bg-white dark:bg-slate-900 rounded-[3rem] w-full max-w-4xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300 border border-fuchsia-100 dark:border-slate-800 flex flex-col md:flex-row h-[90vh] md:h-auto max-h-[90vh]">
                
                <!-- Partie Gauche : Détails -->
                <div class="flex-1 flex flex-col border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-8 text-white relative">
                        <div class="absolute top-0 right-0 p-4 opacity-20">📝</div>
                        <h3 class="text-2xl font-black relative z-10">{{ editingTask ? 'Détails de la Tâche' : 'Nouvelle Tâche' }}</h3>
                        <p class="text-slate-400 text-sm mt-1 relative z-10">Gérez les étapes de votre événement</p>
                    </div>
                    
                    <form @submit.prevent="submit" class="p-8 space-y-6 overflow-y-auto">
                        <div class="space-y-4">
                            <input v-model="form.title" type="text" 
                                   class="block w-full bg-slate-50 dark:bg-slate-800 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition dark:text-white" 
                                   placeholder="Qu'y a-t-il à faire ?" required>
                            
                            <select v-model="form.member_id" 
                                    class="block w-full bg-slate-50 dark:bg-slate-800 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition dark:text-white">
                                <option value="">Personne assignée (optionnel)</option>
                                <option v-for="member in members.data" :key="member.id" :value="member.id">
                                    {{ member.name }}
                                </option>
                            </select>

                            <div class="grid grid-cols-2 gap-4">
                                <select v-model="form.priority" 
                                        class="block w-full bg-slate-50 dark:bg-slate-800 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition dark:text-white">
                                    <option value="low">Priorité Basse</option>
                                    <option value="medium">Priorité Moyenne</option>
                                    <option value="high">Priorité Haute</option>
                                </select>
                                <input v-model="form.due_date" type="date" 
                                       class="block w-full bg-slate-50 dark:bg-slate-800 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition dark:text-white">
                            </div>

                            <textarea v-model="form.description" 
                                      class="block w-full bg-slate-50 dark:bg-slate-800 border-0 rounded-2xl py-4 px-6 focus:ring-2 focus:ring-fuchsia-500 transition dark:text-white" 
                                      placeholder="Plus de détails..." rows="3"></textarea>
                        </div>
                        <div class="flex gap-4">
                            <button @click="closeModal" type="button" class="flex-1 px-6 py-4 rounded-2xl font-black text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                Annuler
                            </button>
                            <button type="submit" :disabled="form.processing" 
                                    class="flex-[2] bg-gradient-to-r from-fuchsia-600 to-amber-500 text-white py-4 rounded-2xl font-black hover:from-fuchsia-700 hover:to-amber-600 shadow-lg shadow-fuchsia-200 dark:shadow-none transition">
                                {{ editingTask ? 'Mettre à jour' : 'Ajouter' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Partie Droite : Commentaires (seulement si modification) -->
                <div v-if="editingTask" class="flex-1 flex flex-col bg-slate-50 dark:bg-slate-950 p-8">
                    <h4 class="font-black text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                        <span>💬</span> Discussion ({{ editingTask.comments?.length || 0 }})
                    </h4>
                    
                    <!-- Fil de discussion -->
                    <div class="flex-1 overflow-y-auto space-y-4 mb-6 pr-2 custom-scrollbar">
                        <div v-for="comment in editingTask.comments" :key="comment.id" class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm relative group">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-black text-fuchsia-600 dark:text-fuchsia-400 uppercase tracking-wider">{{ comment.user_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ comment.created_at }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ comment.content }}</p>
                            
                            <button v-if="comment.user_id === $page.props.auth.user.id"
                                    @click="deleteComment(comment.id)"
                                    class="absolute top-2 right-2 p-1 text-slate-300 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        
                        <div v-if="!editingTask.comments?.length" class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">Aucun commentaire pour le moment</p>
                        </div>
                    </div>

                    <!-- Input Commentaire -->
                    <form @submit.prevent="submitComment" class="relative">
                        <textarea v-model="commentForm.content" 
                                  class="block w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 rounded-2xl py-3 px-4 pr-12 focus:ring-2 focus:ring-fuchsia-500 transition text-sm dark:text-white" 
                                  placeholder="Écrire un message..." rows="2" required></textarea>
                        <button type="submit" :disabled="commentForm.processing"
                                class="absolute bottom-3 right-3 p-2 bg-fuchsia-600 text-white rounded-xl hover:bg-fuchsia-700 transition shadow-lg shadow-fuchsia-200 dark:shadow-none disabled:opacity-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
