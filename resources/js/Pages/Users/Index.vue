<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    users: Array,
});
</script>

<template>
    <Head title="Other users" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <h1 class="mb-4 text-2xl font-semibold">Other users</h1>

            <div v-if="users.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="user in users" :key="user.id" class="rounded-xl border border-zinc-800 bg-zinc-900 p-4">
                    <h2 class="font-semibold">{{ user.name }}</h2>
                    <p class="text-sm text-zinc-400">@{{ user.username }}</p>
                    <p class="mt-2 text-xs text-zinc-500">
                        Start: {{ user.challenge_start_date ?? 'Not set' }}
                    </p>

                    <Link
                        v-if="user.profile_url"
                        :href="user.profile_url"
                        class="mt-3 inline-block rounded-lg bg-zinc-800 px-3 py-2 text-sm"
                    >
                        View public profile
                    </Link>
                    <p v-else class="mt-3 text-xs text-zinc-500">Profile is private.</p>
                </article>
            </div>
            <p v-else class="rounded-xl border border-zinc-800 bg-zinc-900 p-5 text-zinc-400">No other users yet.</p>
        </div>
    </AuthenticatedLayout>
</template>
