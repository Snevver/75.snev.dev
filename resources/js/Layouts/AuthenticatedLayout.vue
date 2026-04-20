<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();

const navItems = [
    { label: "Dashboard", route: "dashboard", match: "dashboard" },
    { label: "Overview", route: "overview.index", match: "overview.*" },
    { label: "Other users", route: "users.index", match: "users.*" },
    { label: "Photo gallery", route: "photos.index", match: "photos.*" },
    { label: "Past challenges", route: "archive.index", match: "archive.*" },
    { label: "Profile", route: "profile.edit", match: "profile.*" },
];
</script>

<template>
    <div class="min-h-screen bg-zinc-950 text-zinc-100">
        <header
            class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-900/90 px-4 py-3 backdrop-blur"
        >
            <div
                class="mx-auto flex max-w-[1800px] items-center justify-between gap-3"
            >
                <Link
                    :href="route('dashboard')"
                    class="truncate font-semibold text-emerald-400"
                    >75 hard tracker</Link
                >
                <div class="flex items-center gap-3 text-sm">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="rounded-lg bg-zinc-800 px-3 py-2"
                        >Logout</Link
                    >
                </div>
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-[1800px]">
            <aside
                class="sticky top-[65px] hidden h-[calc(100vh-65px)] w-72 self-start overflow-y-auto border-r border-zinc-800 bg-zinc-900/60 p-4 md:block"
            >
                <nav class="space-y-2 pb-4">
                    <Link
                        v-for="item in navItems"
                        :key="item.route"
                        :href="route(item.route)"
                        class="block min-h-11 rounded-lg px-3 py-3 text-sm transition"
                        :class="
                            route().current(item.match)
                                ? 'bg-emerald-500 text-zinc-950'
                                : 'bg-zinc-800 text-zinc-200 hover:bg-zinc-700'
                        "
                    >
                        {{ item.label }}
                    </Link>
                    <Link
                        v-if="page.props.auth.user?.is_admin"
                        :href="route('admin.stats')"
                        class="block min-h-11 rounded-lg px-3 py-3 text-sm transition"
                        :class="
                            route().current('admin.*')
                                ? 'bg-emerald-500 text-zinc-950'
                                : 'bg-zinc-800 text-zinc-200 hover:bg-zinc-700'
                        "
                    >
                        Admin
                    </Link>
                </nav>
            </aside>

            <main class="flex-1">
                <slot />
            </main>
        </div>

        <nav
            class="fixed bottom-0 left-0 right-0 z-40 border-t border-zinc-800 bg-zinc-900/95 p-2 pb-[calc(0.5rem+env(safe-area-inset-bottom))] md:hidden"
        >
            <div class="grid grid-cols-5 gap-2 text-center text-xs">
                <Link
                    :href="route('dashboard')"
                    class="min-h-11 rounded-lg px-2 py-3"
                    :class="
                        route().current('dashboard')
                            ? 'bg-emerald-500 text-zinc-950'
                            : 'bg-zinc-800'
                    "
                    >Home</Link
                >
                <Link
                    :href="route('overview.index')"
                    class="min-h-11 rounded-lg px-2 py-3"
                    :class="
                        route().current('overview.*')
                            ? 'bg-emerald-500 text-zinc-950'
                            : 'bg-zinc-800'
                    "
                    >Overview</Link
                >
                <Link
                    :href="route('users.index')"
                    class="min-h-11 rounded-lg px-2 py-3"
                    :class="
                        route().current('users.*')
                            ? 'bg-emerald-500 text-zinc-950'
                            : 'bg-zinc-800'
                    "
                    >Users</Link
                >
                <Link
                    :href="route('photos.index')"
                    class="min-h-11 rounded-lg px-2 py-3"
                    :class="
                        route().current('photos.*')
                            ? 'bg-emerald-500 text-zinc-950'
                            : 'bg-zinc-800'
                    "
                    >Photos</Link
                >
                <Link
                    :href="route('profile.edit')"
                    class="min-h-11 rounded-lg px-2 py-3"
                    :class="
                        route().current('profile.*')
                            ? 'bg-emerald-500 text-zinc-950'
                            : 'bg-zinc-800'
                    "
                    >Profile</Link
                >
            </div>
        </nav>
    </div>
</template>
