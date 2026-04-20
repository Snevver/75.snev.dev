<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    completedChallenges: Array,
});
</script>

<template>
    <Head title="Past challenges" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-zinc-100">
                    Past challenges
                </h1>
                <p class="mt-2 text-zinc-400">
                    Your past 75-day challenge attempts are archived here. They
                    are always private and cannot be shared.
                </p>
            </div>

            <div v-if="completedChallenges.length" class="space-y-4">
                <article
                    v-for="challenge in completedChallenges"
                    :key="challenge.id"
                    class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-100">
                                Challenge from {{ challenge.start_date }}
                            </h2>
                            <p class="mt-1 text-sm text-zinc-400">
                                Ended on {{ challenge.end_date }}
                            </p>
                        </div>
                        <div
                            class="grid grid-cols-2 gap-3 text-right sm:grid-cols-4"
                        >
                            <div>
                                <p class="text-lg font-bold text-emerald-400">
                                    {{ challenge.perfect_days }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    perfect days
                                </p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-blue-400">
                                    {{ challenge.current_streak }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    current streak
                                </p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-amber-400">
                                    {{ challenge.photos_uploaded }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    photos uploaded
                                </p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-zinc-100">
                                    {{ challenge.days_remaining }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    days remaining
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <Link
                            :href="challenge.overview_url"
                            class="inline-flex min-h-11 items-center rounded-lg bg-zinc-800 px-4 py-2 text-sm font-medium text-zinc-100 hover:bg-zinc-700"
                        >
                            View challenge overview
                        </Link>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-12 text-center"
            >
                <svg
                    class="mx-auto h-16 w-16 text-zinc-700 mb-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <h3 class="text-lg font-semibold text-zinc-100">
                    No completed challenges yet
                </h3>
                <p class="mt-2 text-sm text-zinc-400">
                    When you complete a 75-day challenge, it will appear here in
                    your private archive.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
