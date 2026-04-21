<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    archive: Object,
    stats: Object,
    days: Array,
});

const selectedDayNumber = ref(1);
const selectedDay = computed(() =>
    props.days.find((day) => day.day_number === selectedDayNumber.value),
);

const statusLabel = (status) =>
    ({
        perfect: "perfect",
        partial: "partial",
        missed: "missed",
        today: "today",
        upcoming: "upcoming",
    })[status] ?? status;

const normalizeNotes = (value) => {
    if (!value) return "";

    const withLineBreaks = value
        .replace(/<\s*br\s*\/?\s*>/gi, "\n")
        .replace(/<\/(p|div|li|h[1-6]|blockquote)>/gi, "\n");
    const textarea = document.createElement("textarea");
    textarea.innerHTML = withLineBreaks.replace(/<[^>]+>/g, "");

    return textarea.value.trim();
};
</script>

<template>
    <Head title="Challenge overview" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-[1600px] space-y-6 p-4 pb-24">
            <section class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5">
                <h1 class="text-2xl font-semibold text-zinc-100">
                    Challenge overview
                </h1>
                <p class="mt-1 text-sm text-zinc-400">
                    {{ archive.start_date }} to {{ archive.end_date }}
                </p>
            </section>

            <div class="grid gap-4 lg:grid-cols-7">
                <section
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-3 lg:col-span-5"
                >
                    <div
                        class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8"
                    >
                        <button
                            v-for="day in days"
                            :key="day.day_number"
                            class="min-h-11 rounded-lg border px-2 py-2 text-left text-sm"
                            :class="
                                selectedDayNumber === day.day_number
                                    ? 'border-emerald-500 bg-emerald-500/20'
                                    : 'border-zinc-700 bg-zinc-800 hover:bg-zinc-700'
                            "
                            @click="selectedDayNumber = day.day_number"
                        >
                            <p class="font-semibold">
                                Day {{ day.day_number }}
                            </p>
                            <p class="text-xs text-zinc-400">{{ day.date }}</p>
                            <p class="mt-1 text-xs">
                                {{ statusLabel(day.status) }}
                            </p>
                        </button>
                    </div>
                </section>

                <section
                    class="rounded-2xl border border-zinc-800 bg-zinc-900 p-4 lg:col-span-2"
                >
                    <template v-if="selectedDay">
                        <h2 class="text-lg font-semibold">
                            Day {{ selectedDay.day_number }}
                        </h2>
                        <p class="text-sm text-zinc-400">
                            {{ selectedDay.date }}
                        </p>
                        <p class="mt-2">
                            {{ statusLabel(selectedDay.status) }}
                        </p>

                        <div
                            v-if="selectedDay.details"
                            class="mt-4 space-y-3 text-sm"
                        >
                            <div>
                                <h3 class="font-medium">completed tasks</h3>
                                <ul
                                    class="mt-1 list-disc space-y-1 pl-5 text-zinc-300"
                                >
                                    <li
                                        v-for="task in selectedDay.details
                                            .tasks"
                                        :key="task.id"
                                    >
                                        {{ task.title }}
                                    </li>
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-medium">notes</h3>
                                <div
                                    v-if="selectedDay.details.notes"
                                    class="mt-1 whitespace-pre-wrap text-zinc-300"
                                >
                                    {{ normalizeNotes(selectedDay.details.notes) }}
                                </div>
                                <p v-else class="mt-1 text-zinc-300">
                                    No notes.
                                </p>
                            </div>

                            <div v-if="selectedDay.details.photo_url">
                                <h3 class="font-medium">progress photo</h3>
                                <a
                                    :href="selectedDay.details.photo_url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-2 block overflow-hidden rounded-lg border border-zinc-700"
                                >
                                    <img
                                        :src="selectedDay.details.photo_url"
                                        alt="Archived progress photo"
                                        class="h-56 w-full object-cover"
                                    />
                                </a>
                            </div>
                        </div>
                        <p v-else class="mt-3 text-sm text-zinc-400">
                            No details logged yet for this day.
                        </p>
                    </template>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
