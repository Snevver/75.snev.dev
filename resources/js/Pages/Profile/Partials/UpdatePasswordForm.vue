<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const updatePassword = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <form @submit.prevent="updatePassword" class="space-y-6">
        <p class="text-sm text-zinc-400">
            Keep your account secure with a strong password. Use a mix of
            uppercase, lowercase, numbers, and symbols.
        </p>

        <div>
            <InputLabel for="current_password" value="Current password" />
            <TextInput
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100"
                autocomplete="current-password"
                placeholder="Enter your current password"
            />
            <InputError :message="form.errors.current_password" class="mt-2" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <InputLabel for="password" value="New password" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100"
                    autocomplete="new-password"
                    placeholder="Enter new password"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm password"
                />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100"
                    autocomplete="new-password"
                    placeholder="Confirm new password"
                />
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6 border-t border-zinc-700">
            <PrimaryButton :disabled="form.processing">
                {{ form.processing ? "Updating..." : "Update password" }}
            </PrimaryButton>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm text-emerald-400"
                >
                    ✓ Password updated successfully.
                </p>
            </Transition>
        </div>
    </form>
</template>
