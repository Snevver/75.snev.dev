<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ users: Object });
</script>
<template>
    <Head title="Admin Users" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <h1 class="mb-4 text-xl font-semibold">User Manager</h1>
            <div v-for="user in users.data" :key="user.id" class="mb-2 rounded-xl bg-zinc-900 p-3">{{ user.name }} ({{ user.email }})</div>
        </div>
    </AuthenticatedLayout>
</template>
