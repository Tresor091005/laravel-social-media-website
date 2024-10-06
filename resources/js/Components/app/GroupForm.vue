<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import TextInput from "@/Components/TextInput.vue";

import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import { ClassicEditor, Bold, Italic, Underline, Link, Heading, List, BlockQuote, Essentials, Mention, Paragraph, Undo } from "ckeditor5";
import "ckeditor5/ckeditor5.css";


const editor = ClassicEditor;
const editorConfig = {
    plugins: [Heading, Bold, Italic, Underline, Link, List, BlockQuote, Undo, Essentials, Mention, Paragraph],
    toolbar: ["heading", "|", "bold", "italic", "underline", "|", "link", "undo", "redo", "|", "bulletedList", "numberedList", "blockQuote"],
};

defineProps({
    form: Object,
    formErrors: Object
})

</script>
<template>
    <div class="mb-3">
        <label>Group Name</label>
        <TextInput
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
        />

        <div v-if="formErrors.name"
            class="mt-1"
            :class="formErrors.name ? 'text-red-500' : ''"
        >
            {{ formErrors.name }}
        </div>
    </div>

    <div class="mb-3">
        <label>
            <Checkbox name="remember" v-model:checked="form.auto_approval"/>
            Enable Auto Approval
        </label>

        <div v-if="formErrors.auto_approval"
            class="mt-1"
            :class="formErrors.auto_approval ? 'text-red-500' : ''"
        >
            {{ formErrors.auto_approval }}
        </div>
    </div>

    <div class="mb-3">
        <label>About Group</label>

        <ckeditor :editor="editor" v-model="form.about" :config="editorConfig"></ckeditor>

        <div v-if="formErrors.about"
            class="mt-1"
            :class="formErrors.about ? 'text-red-500' : ''"
        >
            {{ formErrors.about }}
        </div>
    </div>

</template>
<style>

</style>
