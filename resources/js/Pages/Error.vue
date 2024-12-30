<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {usePage} from '@inertiajs/vue3';
import { onMounted } from 'vue';
defineProps({
    title: String,
})

onMounted(() => document.title = `Error - ${usePage().props.APP_NAME}`)
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-16 text-center">
            <h1 class="text-2xl">{{ title }}</h1>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
