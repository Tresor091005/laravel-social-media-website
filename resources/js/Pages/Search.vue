<script setup>
import { usePage } from '@inertiajs/vue3';
import UserListItem from "@/Components/app/UserListItem.vue";
import GroupItem from "@/Components/app/GroupItem.vue";
import PostList from "@/Components/app/PostList.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { onMounted } from 'vue';

const props = defineProps({
    search: String,
    users: Array,
    groups: Array,
    posts: Object
})

onMounted(() => document.title = `Rechercher - ${usePage().props.APP_NAME}`)

</script>

<template>

    <AuthenticatedLayout>
        <div class="p-4 lg:h-[65vh]">
            <div v-if="!search.startsWith('#')" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-4 bg-white dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 shadow sm:rounded-lg">
                    <h2 class="text-lg font-bold dark:text-gray-100">Users</h2>
                    <div class="grid-cols-2">
                        <UserListItem :dark-shadow="false" v-if="users.length" v-for="user of users" :user="user"/>
                        <div v-else class="py-8 text-center text-gray-500">
                            No users were found.
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-white dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100 shadow sm:rounded-lg">
                    <h2 class="text-lg font-bold dark:text-gray-100">Groups</h2>
                    <div class="grid-cols-2">
                        <GroupItem v-if="groups.length" v-for="group of groups" :group="group"/>
                        <div v-else class="py-8 text-center text-gray-500">
                            No groups were found.
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-full overflow-hidden flex flex-col">
                <h2 class="text-lg font-bold dark:text-gray-100">Posts</h2>
                <PostList v-if="posts.data.length" :posts="posts" class="flex-1"/>
                <div v-else class="py-8 text-center text-gray-500 dark:text-gray-300">
                    No posts were found.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
