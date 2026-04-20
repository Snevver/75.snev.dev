<script setup>
import { Head } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({ profile: Object, days: Array, today: String });

const selectedDayNumber = ref(
    props.days.find((day) => day.date === props.today)?.day_number ?? 1,
);
const selectedDay = computed(() =>
    props.days.find((day) => day.day_number === selectedDayNumber.value),
);

const statusLabel = (status) =>
    ({
        perfect: "Perfect",
        partial: "Partial",
        missed: "Missed",
        today: "Today",
        upcoming: "Upcoming",
    })[status] ?? status;
</script>

<template>
    <Head :title="`${profile.name} - Overview`" />
    <div class="min-h-screen bg-zinc-950 p-4 text-zinc-100">
        <div class="mx-auto max-w-6xl space-y-4 pb-16">
            <div>
                <h1 class="text-2xl font-semibold">{{ profile.name }}</h1>
                <p class="text-zinc-400">@{{ profile.username }}</p>
                <p class="text-sm text-zinc-500">
                    Challenge start:
                    {{ profile.challenge_start_date ?? "Not set" }}
                </p>
            </div>

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
                                <h3 class="font-medium">Completed tasks</h3>
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

                            <a
                                v-if="selectedDay.details.photo_url"
                                :href="selectedDay.details.photo_url"
                                target="_blank"
                                class="inline-block rounded-lg bg-zinc-800 px-3 py-2"
                            >
                                View progress photo
                            </a>
                        </div>
                        <p v-else class="mt-3 text-sm text-zinc-400">
                            No details logged yet for this day.
                        </p>
                    </template>
                </section>
            </div>
        </div>
    </div>
</template>
