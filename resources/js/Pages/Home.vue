<script setup>
import { usePage } from '@inertiajs/vue3';
import GroupList from "@/Components/app/GroupList.vue";
import FollowingList from "@/Components/app/FollowingList.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted } from 'vue';

defineProps({
    posts: Object,
    groups: Array,
    followings: Array
});

onMounted(() => document.title = `Acceuil - ${usePage().props.APP_NAME}`)
</script>

<template>

    <AuthenticatedLayout>
        <div class="grid lg:grid-cols-12 gap-3 p-4 h-full">
            <div class="lg:col-span-3 lg:order-1 h-full overflow-hidden">
                <GroupList :groups="groups" />
            </div>
            <div class="lg:col-span-3 lg:order-3 h-full overflow-hidden">
                <FollowingList :users="followings" />
            </div>
            <div class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col">
                <CreatePost />
                <PostList :posts="posts" class="flex-1" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
