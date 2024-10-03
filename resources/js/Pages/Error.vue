<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from '@inertiajs/vue3'

defineProps({
    title: String,
})

</script>

<template>
    <AuthenticatedLayout>
        <Head title='Error' />

        <div class="py-16 text-center">
            <h1 class="text-2xl">{{ title }}</h1>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
