<script setup>
import Editor from "@tinymce/tinymce-vue";
import "tinymce/tinymce";
import "tinymce/models/dom";
import "tinymce/icons/default";
import "tinymce/themes/silver";
import "tinymce/plugins/autolink";
import "tinymce/plugins/link";
import "tinymce/plugins/lists";
import "tinymce/plugins/wordcount";
import "tinymce/skins/ui/oxide-dark/skin.min.css";
import "tinymce/skins/content/dark/content.min.css";
import { computed } from "vue";

const model = defineModel({
    type: String,
    default: "",
});

const props = defineProps({
    placeholder: {
        type: String,
        default: "Write your notes...",
    },
    minHeight: {
        type: Number,
        default: 240,
    },
});

const editorInit = computed(() => ({
    height: props.minHeight,
    menubar: false,
    branding: false,
    promotion: false,
    statusbar: false,
    skin: "oxide-dark",
    content_css: "dark",
    toolbar_sticky: false,
    toolbar_mode: "sliding",
    plugins: "autolink link lists wordcount",
    toolbar:
        "undo redo | blocks | bold italic underline strikethrough | bullist numlist | alignleft aligncenter alignright | blockquote link | removeformat",
    block_formats:
        "Paragraph=p;Heading 2=h2;Heading 3=h3;Blockquote=blockquote",
    placeholder: props.placeholder,
    content_style:
        "body { font-family: Figtree, sans-serif; font-size: 14px; line-height: 1.6; background: #09090b; color: #f4f4f5; } p { margin: 0 0 0.75rem; } ul, ol { margin: 0 0 0.75rem; padding-left: 1.5rem; } a { color: #34d399; } blockquote { border-left: 3px solid #10b981; margin: 0 0 0.75rem; padding-left: 0.9rem; color: #d4d4d8; }",
}));
</script>

<template>
    <div
        class="overflow-hidden rounded-xl border border-zinc-700 bg-zinc-950/80 shadow-2xl shadow-black/20"
    >
        <Editor v-model="model" license-key="gpl" :init="editorInit" />
    </div>
</template>
