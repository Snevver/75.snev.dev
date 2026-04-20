<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({ photos: [Array, Object] });
const selectedPhoto = ref(null);

const photoItems = computed(() => {
    if (Array.isArray(props.photos)) {
        return props.photos;
    }

    if (Array.isArray(props.photos?.data)) {
        return props.photos.data;
    }

    return [];
});

const deletePhoto = (photoId) => {
    if (!window.confirm("Delete this progress photo?")) return;

    router.delete(route("photos.destroy", photoId), { preserveScroll: true });
};

const openPhoto = (photo) => {
    selectedPhoto.value = photo;
};

const closeModal = () => {
    selectedPhoto.value = null;
};
</script>

<template>
    <Head title="Progress photos" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-5xl p-4 pb-24">
            <h1 class="mb-4 text-xl font-semibold">Progress photos</h1>
            <div
                v-if="photoItems.length"
                class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4"
            >
                <article
                    v-for="photo in photoItems"
                    :key="photo.id"
                    class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900"
                >
                    <button
                        type="button"
                        class="block w-full cursor-pointer"
                        @click="openPhoto(photo)"
                    >
                        <img
                            :src="photo.url"
                            alt="Progress photo"
                            class="h-44 w-full object-cover transition duration-300 hover:scale-[1.03]"
                        />
                    </button>
                    <div
                        class="flex items-center justify-between gap-2 p-3 text-xs"
                    >
                        <p class="text-zinc-400">{{ photo.taken_at }}</p>
                        <button
                            type="button"
                            class="rounded-md bg-red-500/90 px-2 py-1 text-white"
                            @click="deletePhoto(photo.id)"
                        >
                            Delete
                        </button>
                    </div>
                </article>
            </div>
            <p v-else class="rounded-xl bg-zinc-900 p-5 text-zinc-400">
                No photos yet.
            </p>
        </div>

        <!-- Photo Modal -->
        <div
            v-if="selectedPhoto"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
            @click="closeModal"
        >
            <button
                type="button"
                class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-900/80 text-zinc-200 hover:bg-zinc-800"
                @click="closeModal"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
            <div
                class="relative max-h-[90vh] max-w-[90vw] overflow-hidden rounded-lg bg-zinc-900"
                @click.stop
            >
                <img
                    :src="selectedPhoto.url"
                    alt="Progress photo"
                    class="h-full w-full object-contain"
                />
                <div
                    class="absolute bottom-0 left-0 right-0 flex items-center justify-between bg-gradient-to-t from-black/80 p-4 text-sm"
                >
                    <p class="text-zinc-300">
                        {{ selectedPhoto.taken_at }}
                    </p>
                    <button
                        type="button"
                        class="rounded-md bg-red-500/90 px-3 py-2 text-white hover:bg-red-500"
                        @click.stop="
                            deletePhoto(selectedPhoto.id);
                            closeModal();
                        "
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
