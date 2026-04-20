<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route("profile.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="space-y-6">
        <p class="text-sm text-zinc-400">
            Deleting your account is permanent and cannot be undone. You will
            lose all your data, progress photos, and challenge history.
        </p>

        <DangerButton @click="confirmUserDeletion" class="rounded-lg">
            <svg
                class="h-5 w-5 mr-2 inline"
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
            Delete My Account
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-500/10 flex-shrink-0"
                    >
                        <svg
                            class="h-6 w-6 text-red-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-zinc-100">
                            Delete Account?
                        </h2>

                        <p class="mt-2 text-sm text-zinc-400">
                            This action cannot be undone. All your data will be
                            permanently deleted:
                        </p>
                        <ul
                            class="mt-2 space-y-1 text-sm text-zinc-400 list-disc list-inside"
                        >
                            <li>Your account and profile</li>
                            <li>All uploaded photos</li>
                            <li>Challenge progress and history</li>
                            <li>All saved notes</li>
                        </ul>

                        <p class="mt-4 font-medium text-zinc-200">
                            Please enter your password to confirm:
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="text-zinc-100"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full bg-zinc-800/50 border-zinc-700 text-zinc-100"
                        placeholder="Enter your password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        {{ form.processing ? "Deleting..." : "Delete Account" }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
