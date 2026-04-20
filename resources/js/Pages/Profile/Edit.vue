<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const resetConfirming = ref(false);

const resetChallenge = () => {
    if (
        !window.confirm(
            "Are you sure you want to reset your challenge progress from day 1? All current progress will be deleted.",
        )
    ) {
        return;
    }

    router.post(
        route("challenge.reset"),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                window.location.href = route("dashboard");
            },
        },
    );
};
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl space-y-8 p-4 pb-24">
            <!-- Header Card -->
            <div
                class="rounded-2xl bg-gradient-to-br from-emerald-500/10 to-emerald-500/5 border border-emerald-500/20 p-6 sm:p-8"
            >
                <div
                    class="flex flex-col items-start gap-6 sm:flex-row sm:items-center"
                >
                    <div
                        class="h-20 w-20 rounded-full border-2 border-emerald-400/30 bg-zinc-800 flex items-center justify-center overflow-hidden flex-shrink-0"
                    >
                        <img
                            v-if="user.avatar"
                            :src="user.avatar"
                            :alt="user.name"
                            class="h-full w-full object-cover"
                        />
                        <svg
                            v-else
                            class="h-12 w-12 text-zinc-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-zinc-100">
                            {{ user.name }}
                        </h1>
                        <p class="mt-1 text-zinc-400">@{{ user.username }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span
                                v-if="user.is_public"
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300 border border-emerald-500/30"
                            >
                                <svg
                                    class="h-3 w-3"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M15 8a3 3 0 11-6 0 3 3 0 016 0z"
                                    ></path>
                                    <path
                                        fill-rule="evenodd"
                                        d="M0 8a8 8 0 1116 0A8 8 0 010 8zm16 6a8.001 8.001 0 01-7.022 7.938 7.001 7.001 0 014.513-6.226 2.005 2.005 0 100-2.712A7 7 0 0116 14z"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                                Public profile
                            </span>
                            <span
                                v-if="user.share_public_photos"
                                class="inline-flex items-center gap-1 rounded-full bg-blue-500/20 px-3 py-1 text-xs font-medium text-blue-300 border border-blue-500/30"
                            >
                                <svg
                                    class="h-3 w-3"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4.5-4.5 3 3 5-5V15z"
                                    ></path>
                                </svg>
                                Photos shared
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div
                class="rounded-2xl border border-zinc-800 bg-zinc-900/50 backdrop-blur p-6 sm:p-8"
            >
                <header class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500/10 border border-emerald-500/20"
                    >
                        <svg
                            class="h-6 w-6 text-emerald-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-100">
                        Profile information
                    </h2>
                </header>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-2xl"
                />
            </div>

            <!-- Update Password Card -->
            <div
                class="rounded-2xl border border-zinc-800 bg-zinc-900/50 backdrop-blur p-6 sm:p-8"
            >
                <header class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/10 border border-blue-500/20"
                    >
                        <svg
                            class="h-6 w-6 text-blue-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-100">
                        Update password
                    </h2>
                </header>
                <UpdatePasswordForm class="max-w-2xl" />
            </div>

            <!-- Delete Account Card -->
            <div
                class="rounded-2xl border border-red-500/20 bg-red-500/5 p-6 sm:p-8"
            >
                <header class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500/10 border border-red-500/20"
                    >
                        <svg
                            class="h-6 w-6 text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-100">
                        Delete account
                    </h2>
                </header>
                <DeleteUserForm class="max-w-2xl" />
            </div>

            <!-- Challenge Management Card -->
            <div
                class="rounded-2xl border border-orange-500/20 bg-orange-500/5 p-6 sm:p-8"
            >
                <header class="mb-6 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-500/10 border border-orange-500/20"
                    >
                        <svg
                            class="h-6 w-6 text-orange-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-100">
                        Challenge management
                    </h2>
                </header>

                <div class="space-y-4">
                    <p class="text-sm text-zinc-400">
                        Reset your current challenge and start from day 1. This
                        will delete all your current progress.
                    </p>

                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-lg bg-orange-500/90 hover:bg-orange-500 px-4 py-2 font-semibold text-white transition"
                        @click="resetChallenge"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        Reset challenge to day 1
                    </button>

                    <Link
                        :href="route('archive.index')"
                        class="inline-flex items-center gap-2 rounded-lg bg-zinc-800 hover:bg-zinc-700 px-4 py-2 font-semibold text-zinc-200 transition ml-2"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        View past challenges
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
