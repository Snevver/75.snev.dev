<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border border-zinc-700 bg-zinc-800 text-zinc-100 placeholder:text-zinc-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        v-model="model"
        ref="input"
    />
</template>
