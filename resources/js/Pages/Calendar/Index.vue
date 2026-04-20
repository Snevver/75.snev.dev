<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ month: String, logs: Array });
</script>

<template>
    <Head title="Calendar" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <h1 class="mb-4 text-xl font-semibold">Monthly Overview ({{ month }})</h1>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <article v-for="log in logs" :key="log.id" class="rounded-xl bg-zinc-900 p-3">
                    <p class="text-sm text-zinc-400">Day {{ log.day_number }}</p>
                    <p class="mt-2 text-lg">{{ log.is_complete ? '✅ Perfect' : '⚠️ Partial' }}</p>
                    <p class="text-xs text-zinc-500">{{ log.log_date }}</p>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
