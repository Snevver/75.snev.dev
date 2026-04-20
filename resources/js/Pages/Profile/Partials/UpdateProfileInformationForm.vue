<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
    timezone: user.timezone ?? "UTC",
    challenge_start_date: user.challenge_start_date,
    is_public: user.is_public,
    share_public_photos: user.share_public_photos,
});

const submit = () =>
    form.patch(route("profile.update"), {
        preserveScroll: true,
    });
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <!-- Basic Information Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <InputLabel for="name" value="Full name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100 placeholder-zinc-500"
                    v-model="form.name"
                    required
                    placeholder="Your full name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="username" value="Username" />
                <TextInput
                    id="username"
                    type="text"
                    class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100 placeholder-zinc-500"
                    v-model="form.username"
                    required
                    placeholder="@username"
                />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="md:col-span-2">
                <InputLabel for="email" value="Email address" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100 placeholder-zinc-500"
                    v-model="form.email"
                    required
                    placeholder="your@email.com"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
        </div>

        <!-- Privacy Settings -->
        <div class="border-t border-zinc-700 pt-6 space-y-4">
            <h3 class="font-medium text-zinc-100">Privacy & sharing</h3>

            <label
                class="flex items-center gap-3 rounded-lg hover:bg-zinc-800/50 p-3 cursor-pointer transition"
            >
                <input
                    type="checkbox"
                    v-model="form.is_public"
                    class="w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-emerald-500 focus:ring-emerald-500"
                />
                <div>
                    <p class="font-medium text-zinc-100">Make profile public</p>
                    <p class="text-xs text-zinc-400">
                        Allow others to view your profile and challenge progress
                    </p>
                </div>
            </label>

            <label
                class="flex items-center gap-3 rounded-lg hover:bg-zinc-800/50 p-3 cursor-pointer transition"
            >
                <input
                    type="checkbox"
                    v-model="form.share_public_photos"
                    class="w-4 h-4 rounded border-zinc-600 bg-zinc-700 text-blue-500 focus:ring-blue-500"
                />
                <div>
                    <p class="font-medium text-zinc-100">
                        Share photos publicly
                    </p>
                    <p class="text-xs text-zinc-400">
                        Display your progress photos on your public profile
                    </p>
                </div>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center gap-4 pt-6 border-t border-zinc-700">
            <PrimaryButton :disabled="form.processing">
                {{ form.processing ? "Saving..." : "Save Changes" }}
            </PrimaryButton>
        </div>
    </form>
</template>
