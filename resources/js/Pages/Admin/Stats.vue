<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ stats: Object });
</script>
<template>
    <Head title="Admin Stats" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <h1 class="mb-4 text-xl font-semibold">Global Stats</h1>
            <div class="grid gap-3 md:grid-cols-3">
                <div class="rounded-xl bg-zinc-900 p-4">Users: {{ stats.total_users }}</div>
                <div class="rounded-xl bg-zinc-900 p-4">Active challenges: {{ stats.active_challenges }}</div>
                <div class="rounded-xl bg-zinc-900 p-4">Completed today: {{ stats.tasks_completed_today }}</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
