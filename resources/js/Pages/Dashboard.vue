<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";
import { Head, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    currentDay: Number,
    tasks: Array,
    completedTaskIds: Array,
    weeklyProgress: Object,
    stats: Object,
    challengeFailed: Boolean,
    today: String,
    todayPhoto: Object,
    todayNotes: String,
});
const notesForm = useForm({
    notes: props.todayNotes ?? "",
    log_date: props.today,
});
const photoForm = useForm({ photo: null, log_date: props.today });
const completedIds = ref(new Set(props.completedTaskIds));
const syncingTask = ref(null);
const fileInput = ref(null);
const currentPhoto = ref(props.todayPhoto ? { ...props.todayPhoto } : null);
const previewUrl = ref(currentPhoto.value?.url ?? null);
const uploadingPhoto = ref(false);
const isCompleted = (id) => completedIds.value.has(id);
const toggleTask = async (task, event) => {
    const checked = event.target.checked;
    syncingTask.value = task.id;
    if (checked) completedIds.value.add(task.id);
    else completedIds.value.delete(task.id);
    try {
        await axios.post(route("logs.task.update"), {
            task_id: task.id,
            completed: checked,
            log_date: props.today,
        });
    } catch {
        if (checked) completedIds.value.delete(task.id);
        else completedIds.value.add(task.id);
    } finally {
        syncingTask.value = null;
    }
};
const saveNotes = async () => {
    await axios.post(route("logs.notes.update"), notesForm.data());
};
const onPhotoChange = (event) => {
    photoForm.photo = event.target.files[0];
    if (!photoForm.photo) return;

    previewUrl.value = URL.createObjectURL(photoForm.photo);
    uploadingPhoto.value = true;

    photoForm.post(route("photos.store"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (page) => {
            currentPhoto.value = page.props.todayPhoto ?? currentPhoto.value;
            previewUrl.value = currentPhoto.value?.url ?? previewUrl.value;
        },
        onFinish: () => {
            uploadingPhoto.value = false;
            photoForm.reset("photo");
            if (fileInput.value) {
                fileInput.value.value = "";
            }
        },
    });
};

const deletePhoto = () => {
    if (!currentPhoto.value?.id) return;
    if (!window.confirm("Delete this progress photo?")) return;

    router.delete(route("photos.destroy", currentPhoto.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            currentPhoto.value = null;
            previewUrl.value = null;
            if (fileInput.value) {
                fileInput.value.value = "";
            }
        },
    });
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-[1500px] space-y-6 p-4 pb-24">
            <section class="rounded-2xl bg-zinc-900 p-5">
                <p class="text-sm text-zinc-400">Day {{ currentDay }} of 75</p>
                <div class="mt-2 h-3 overflow-hidden rounded-full bg-zinc-800">
                    <div
                        class="h-full bg-emerald-500 transition-all"
                        :style="{ width: `${(currentDay / 75) * 100}%` }"
                    />
                </div>
            </section>
            <section class="grid gap-3 md:grid-cols-4">
                <div class="rounded-xl bg-zinc-900 p-4">
                    <p class="text-xs text-zinc-400">Perfect days</p>
                    <p class="text-xl">{{ stats.perfect_days }}</p>
                </div>
                <div class="rounded-xl bg-zinc-900 p-4">
                    <p class="text-xs text-zinc-400">Current streak</p>
                    <p class="text-xl">{{ stats.current_streak }}</p>
                </div>
                <div class="rounded-xl bg-zinc-900 p-4">
                    <p class="text-xs text-zinc-400">Days remaining</p>
                    <p class="text-xl">{{ stats.days_remaining }}</p>
                </div>
            </section>
            <section class="rounded-2xl bg-zinc-900 p-5">
                <h2 class="font-semibold">Today's tasks</h2>
                <div class="mt-4 space-y-2">
                    <label
                        v-for="task in tasks"
                        :key="task.id"
                        class="group flex min-h-11 cursor-pointer items-center gap-3 rounded-lg bg-zinc-800 p-3 transition-all duration-300 hover:-translate-y-0.5 hover:bg-zinc-700"
                        :class="{
                            'ring-2 ring-emerald-500/50': isCompleted(task.id),
                        }"
                    >
                        <span
                            class="relative inline-flex h-6 w-6 items-center justify-center"
                        >
                            <input
                                type="checkbox"
                                class="peer sr-only"
                                :checked="isCompleted(task.id)"
                                :disabled="syncingTask === task.id"
                                @change="toggleTask(task, $event)"
                            />
                            <span
                                class="h-6 w-6 rounded-md border border-zinc-500 bg-zinc-900 transition-all duration-300 peer-checked:scale-110 peer-checked:border-emerald-400 peer-checked:bg-emerald-500"
                            ></span>
                            <svg
                                class="pointer-events-none absolute h-4 w-4 scale-75 text-zinc-950 opacity-0 transition-all duration-200 peer-checked:scale-100 peer-checked:opacity-100"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 010 1.42l-8.5 8.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 111.414-1.42l2.793 2.8 7.793-7.8a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </span>
                        <span
                            class="transition-all"
                            :class="{
                                'text-emerald-300': isCompleted(task.id),
                            }"
                            >{{ task.icon }} {{ task.title }}</span
                        >
                    </label>
                </div>
            </section>
            <section class="rounded-2xl bg-zinc-900 p-5">
                <h2 class="font-semibold">Weightlifting this week</h2>
                <p class="text-zinc-300">
                    {{ weeklyProgress.completed }} / {{ weeklyProgress.target }}
                </p>
            </section>
            <section class="rounded-2xl bg-zinc-900 p-5">
                <h2 class="font-semibold">Notes</h2>
                <textarea
                    v-model="notesForm.notes"
                    class="mt-3 min-h-28 w-full rounded-lg bg-zinc-800 p-3"
                /><button
                    class="mt-3 min-h-11 rounded-lg bg-emerald-500 px-4 font-semibold text-zinc-950"
                    @click="saveNotes"
                >
                    Save notes
                </button>
            </section>
            <section class="rounded-2xl bg-zinc-900 p-5">
                <h2 class="font-semibold">Progress photo</h2>
                <p class="mt-1 text-sm text-zinc-400">
                    Upload today&apos;s check-in. You can replace or delete it
                    anytime.
                </p>

                <div class="mt-4 grid gap-4 md:grid-cols-[minmax(0,1fr)_220px]">
                    <label
                        class="flex min-h-32 cursor-pointer items-center justify-center rounded-xl border-2 border-dashed border-zinc-600 bg-zinc-800/70 p-4 text-center transition hover:border-emerald-400 hover:bg-zinc-800"
                    >
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="onPhotoChange"
                        />
                        <span class="text-sm text-zinc-300">
                            <strong class="font-semibold text-zinc-100"
                                >Choose a photo</strong
                            >
                            <br />
                            JPG, PNG, WEBP up to 8MB
                        </span>
                    </label>

                    <div
                        class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-800"
                    >
                        <img
                            v-if="previewUrl"
                            :src="previewUrl"
                            alt="Progress photo preview"
                            class="h-48 w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-48 items-center justify-center text-sm text-zinc-500"
                        >
                            No photo uploaded
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button
                        v-if="currentPhoto"
                        type="button"
                        class="min-h-11 rounded-lg bg-red-500 px-4 font-semibold text-white"
                        @click="deletePhoto"
                    >
                        Delete photo
                    </button>
                    <span
                        v-if="uploadingPhoto"
                        class="inline-flex min-h-11 items-center rounded-lg bg-zinc-800 px-4 text-sm text-zinc-300"
                        >Uploading...</span
                    >
                </div>
            </section>
            <section
                v-if="challengeFailed"
                class="rounded-2xl border border-red-500/40 bg-red-500/10 p-5"
            >
                <p class="font-semibold text-red-200">Challenge failed</p>
                <button
                    class="mt-3 min-h-11 rounded-lg bg-red-500 px-4 font-semibold text-white"
                    @click="router.post(route('challenge.restart'))"
                >
                    Restart challenge
                </button>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
