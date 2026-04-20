<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ tasks: Array });

const createForm = useForm({
    title: '',
    description: '',
    frequency: 'daily',
    icon: '✅',
    sort_order: 0,
    is_active: true,
});

const editForm = useForm({
    id: null,
    title: '',
    description: '',
    frequency: 'daily',
    icon: '✅',
    sort_order: 0,
    is_active: true,
});

const dailyTasks = computed(() => props.tasks.filter((task) => task.frequency === 'daily'));
const weeklyTasks = computed(() => props.tasks.filter((task) => task.frequency === 'weekly'));

const submitCreate = () => createForm.post(route('admin.tasks.store'), {
    preserveScroll: true,
    onSuccess: () => createForm.reset('title', 'description', 'icon', 'sort_order'),
});

const startEdit = (task) => {
    editForm.id = task.id;
    editForm.title = task.title;
    editForm.description = task.description ?? '';
    editForm.frequency = task.frequency;
    editForm.icon = task.icon ?? '✅';
    editForm.sort_order = task.sort_order ?? 0;
    editForm.is_active = !!task.is_active;
};

const submitEdit = () => editForm.patch(route('admin.tasks.update', editForm.id), { preserveScroll: true });
const removeTask = (id) => editForm.delete(route('admin.tasks.destroy', id), { preserveScroll: true });
</script>
<template>
    <Head title="Admin tasks" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-[1400px] space-y-6 p-4 pb-24">
            <h1 class="text-2xl font-semibold">Task manager</h1>

            <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
                <h2 class="mb-3 text-lg font-semibold">Add task</h2>
                <div class="grid gap-3 md:grid-cols-2">
                    <input v-model="createForm.title" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3" placeholder="Title">
                    <input v-model="createForm.icon" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3" placeholder="Icon (emoji)">
                    <select v-model="createForm.frequency" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3">
                        <option value="daily">daily</option>
                        <option value="weekly">weekly</option>
                    </select>
                    <input v-model.number="createForm.sort_order" type="number" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3" placeholder="Sort order">
                    <textarea v-model="createForm.description" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3 md:col-span-2" placeholder="Description"></textarea>
                </div>
                <button class="mt-3 min-h-11 rounded-lg bg-emerald-500 px-4 font-semibold text-zinc-950" @click="submitCreate">Add task</button>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
                    <h2 class="mb-3 text-lg font-semibold">Daily tasks</h2>
                    <div class="space-y-2">
                        <button v-for="task in dailyTasks" :key="task.id" class="block w-full rounded-lg bg-zinc-800 p-3 text-left hover:bg-zinc-700" @click="startEdit(task)">
                            {{ task.icon }} {{ task.title }}
                        </button>
                    </div>
                </div>
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
                    <h2 class="mb-3 text-lg font-semibold">Weekly tasks</h2>
                    <div class="space-y-2">
                        <button v-for="task in weeklyTasks" :key="task.id" class="block w-full rounded-lg bg-zinc-800 p-3 text-left hover:bg-zinc-700" @click="startEdit(task)">
                            {{ task.icon }} {{ task.title }}
                        </button>
                    </div>
                </div>
            </section>

            <section v-if="editForm.id" class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4">
                <h2 class="mb-3 text-lg font-semibold">Edit task</h2>
                <div class="grid gap-3 md:grid-cols-2">
                    <input v-model="editForm.title" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3">
                    <input v-model="editForm.icon" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3">
                    <select v-model="editForm.frequency" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3">
                        <option value="daily">daily</option>
                        <option value="weekly">weekly</option>
                    </select>
                    <input v-model.number="editForm.sort_order" type="number" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3">
                    <textarea v-model="editForm.description" class="rounded-lg border border-zinc-700 bg-zinc-800 p-3 md:col-span-2"></textarea>
                </div>
                <div class="mt-3 flex gap-2">
                    <button class="min-h-11 rounded-lg bg-emerald-500 px-4 font-semibold text-zinc-950" @click="submitEdit">Save</button>
                    <button class="min-h-11 rounded-lg bg-red-500 px-4 font-semibold text-white" @click="removeTask(editForm.id)">Archive</button>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
