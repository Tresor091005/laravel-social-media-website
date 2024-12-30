<script setup>

import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "@/Components/app/PostModal.vue";
import AttachmentPreviewModal from "@/Components/app/AttachmentPreviewModal.vue";
import { ref } from "vue";
import { onMounted } from 'vue';
import { usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const authUser = usePage().props.auth.user;
const showEditModal = ref(false)
const editPost = ref({})
const showAttachmentsModal = ref(false)
const previewAttachmentsPost = ref({})

const props = defineProps({
    post: Object
})

function openEditModal(post) {
    editPost.value = post;
    showEditModal.value = true;
}

function openAttachmentPreviewModal(post, index) {
    previewAttachmentsPost.value = {
        post,
        index
    }
    showAttachmentsModal.value = true;
}

function onModalHide() {
    editPost.value = {
        id: null,
        body: "",
        user: authUser,
    }
}

onMounted(() => document.title = `Post - ${usePage().props.APP_NAME}`)

</script>

<template>
    <AuthenticatedLayout>

        <div class="p-8 max-w-[600px] mx-auto h-full overflow-auto">
            <PostItem :post="post"
                @editClick="openEditModal"
                @attachmentClick="openAttachmentPreviewModal"
            />

            <PostModal :post="editPost" v-model="showEditModal" @hide="onModalHide" />

            <AttachmentPreviewModal :attachments="previewAttachmentsPost.post?.attachments || []"
                v-model:index="previewAttachmentsPost.index" v-model="showAttachmentsModal" />
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
