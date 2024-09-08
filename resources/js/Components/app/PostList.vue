<script setup>

import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import {ref} from "vue";

defineProps({
    posts: Array,
})

const showEditModal = ref(false)
const editPost = ref({})

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

const post1 = {
    user: {
        id: 1,
        avatar: "http://localhost:8000/storage/59.jpg",
        name: 'John Smith',
    },
    group: null,
    attachments: [
        {
            id: 1,
            name: 'test.png',
            url: 'http://localhost:8000/storage/570-1000x1000.jpg',
            mime: 'image/png'
        },
        {
            id: 4,
            name: 'Document.docx',
            url: '#',
            mime: 'application/msword'
        },
    ],
    body: `
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
    Quae unde reiciendis consequatur ullam voluptatibus pariatur
    fugiat sed quisquam, aliquid, illum molestias saepe at sunt
    deserunt minus aut perferendis omnis assumenda.</p>

    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
    Quae unde reiciendis consequatur ullam voluptatibus pariatur
    fugiat sed quisquam, aliquid, illum molestias saepe at sunt
    deserunt minus aut perferendis omnis assumenda.</p>`,
    created_at: '2024-11-19 15:12'
}
const post2 = {
    user: {
        id: 5,
        avatar: "http://localhost:8000/storage/59.jpg",
        name: 'Joe March',
    },
    group: {
        id: 1,
        name: 'Laravel Developers'
    },
    attachments: [
        {
            id: 1,
            name: 'test.png',
            url: 'http://localhost:8000/storage/570-1000x1000.jpg',
            mime: 'image/png'
        },
        {
            id: 2,
            name: 'test2.png',
            url: 'http://localhost:8000/storage/570-1000x1000.jpg',
            mime: 'image/png'
        },
    ],
    body: `
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.
    Quae unde reiciendis consequatur ullam voluptatibus pariatur
    fugiat sed quisquam, aliquid, illum molestias saepe at sunt
    deserunt minus aut perferendis omnis assumenda.</p>`,
    created_at: '2023-08-05 21:39'
}

</script>

<template>
    <div class="overflow-auto">
        <PostItem v-for="post of posts" :key="post.id" :post="post" @editClick="openEditModal"/>

        <PostModal :post="editPost" v-model="showEditModal"/>
    </div>
</template>

<style scoped>

</style>
