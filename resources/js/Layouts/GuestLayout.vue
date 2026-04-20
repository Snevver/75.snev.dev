<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-zinc-950 p-4">
        <div class="w-full max-w-md">
            <Link href="/" class="mb-5 block text-center text-2xl font-semibold text-emerald-400">
                75 hard tracker
            </Link>

            <div class="w-full overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 px-6 py-5 shadow-xl">
                <slot />
            </div>
        </div>
    </div>
</template>
