<script setup>
import { computed, ref, onMounted } from 'vue';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { XMarkIcon, CheckCircleIcon, CameraIcon } from "@heroicons/vue/24/solid";
import CreatePost from "@/Components/app/CreatePost.vue";
import PostList from "@/Components/app/PostList.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import TextInput from "@/Components/TextInput.vue";
import TabPhotos from "@/Pages/Profile/TabPhotos.vue";

const imagesForm = useForm({
    cover: null,
    avatar: null,
})

const showNotification = ref(true)
const triggerNotification = () => {
    showNotification.value = true
    setTimeout(() => {
        showNotification.value = false
    }, 3000)
}

const props = defineProps({
    errors: Object,
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    notification: {
        type: String,
    },
    isCurrentUserFollower: Boolean,
    followerCount: Number,
    user: {
        type: Object
    },
    posts: Object,
    followers: Array,
    followings: Array,
    photos: Array
});

const authUser = usePage().props.auth.user;
const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const coverImageSrc = ref('')
const avatarImageSrc = ref('')
const searchFollowersKeyword = ref('')
const searchFollowingsKeyword = ref('')

function onCoverChange(event) {
    imagesForm.cover = event.target.files[0]
    if (imagesForm.cover) {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result
        }
        reader.readAsDataURL(imagesForm.cover)
    }
}

function onAvatarChange(event) {
    imagesForm.avatar = event.target.files[0]
    if (imagesForm.avatar) {
        const reader = new FileReader()
        reader.onload = () => {
            avatarImageSrc.value = reader.result
        }
        reader.readAsDataURL(imagesForm.avatar)
    }
}

function resetCoverImage() {
    coverImageSrc.value = null
    imagesForm.cover = null
}

function resetAvatarImage() {
    avatarImageSrc.value = null
    imagesForm.avatar = null
}

function submitCoverImage() {
    imagesForm.post(route('profile.updateImages'), {
        preserveScroll: true,
        onSuccess: () => {
            resetCoverImage()
            triggerNotification()
        },
        onError: () => {
            resetCoverImage()
            triggerNotification()
        }
    })
}

function submitAvatarImage() {
    imagesForm.post(route('profile.updateImages'), {
        preserveScroll: true,
        onSuccess: () => {
            resetAvatarImage()
            triggerNotification()
        },
        onError: () => {
            resetAvatarImage()
            triggerNotification()
        }
    })
}

function followUser() {
    const form = useForm({
        follow: !props.isCurrentUserFollower
    })

    form.post(route('user.follow', props.user.id), {
        preserveScroll: true
    })
}

onMounted(() => document.title = `Profile - ${usePage().props.APP_NAME}`)

</script>

<template>

    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto h-full overflow-auto">
            <div class="px-4">
                <div v-show="showNotification && props.notification"
                    class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white">
                    {{ props.notification }}
                </div>
                <div v-if="showNotification && errors.cover"
                    class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white">
                    {{ errors.cover }}
                </div>
                <div v-if="showNotification && errors.avatar"
                    class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white">
                    {{ errors.avatar }}
                </div>

                <div class="relative bg-white group dark:bg-slate-950 dark:text-gray-100">
                    <img :src="coverImageSrc || props.user.cover_url || '/img/default_cover.jpg'"
                        class="w-full h-[200px] object-cover" alt="cover-image">
                    <div v-if="isMyProfile" class="absolute top-2 right-2">
                        <button v-if="!coverImageSrc"
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100">
                            <CameraIcon class="w-3 h-3 mr-2" />

                            Update Cover image
                            <input type="file" accept="image/*"
                                class="absolute top-0 left-0 bottom-0 right-0 cursor-pointer opacity-0"
                                @change="onCoverChange" />
                        </button>
                        <div v-else class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100">
                            <button class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center"
                                @click="resetCoverImage">
                                <XMarkIcon class="h-3 w-3 mr-1" />
                                Cancel
                            </button>
                            <button class="bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-xs flex items-center"
                                @click="submitCoverImage">
                                <CheckCircleIcon class="h-3 w-3 mr-1" />
                                Submit
                            </button>
                        </div>
                    </div>
                    <div class="flex">
                        <div
                            class="flex items-center justify-center relative group/avatar ml-[48px] -mt-[64px] w-[128px] h-[128px]">
                            <img :src="avatarImageSrc || props.user.avatar_url || '/img/default_avatar.webp'"
                                class="w-full h-full object-cover rounded-full" alt="avatar-image">
                            <template v-if="isMyProfile">
                                <button v-if="!avatarImageSrc"
                                    class="rounded-full absolute left-0 right-0 top-0 bottom-0 bg-black/50 text-gray-200 opacity-0 flex items-center justify-center group-hover/avatar:opacity-100">
                                    <CameraIcon class="w-8 h-8" />
                                    <input type="file" accept="image/*"
                                        class="absolute top-0 left-0 bottom-0 right-0 cursor-pointer opacity-0"
                                        @change="onAvatarChange" />
                                </button>
                                <div v-else
                                    class="absolute top-1 right-0 flex flex-col gap-2 opacity-0 group-hover/avatar:opacity-100">
                                    <button
                                        class="w-7 h-7 flex items-center justify-center bg-red-500/80 text-white rounded-full"
                                        @click="resetAvatarImage">
                                        <XMarkIcon class="h-5 w-5" />
                                    </button>
                                    <button
                                        class="w-7 h-7 flex items-center justify-center bg-emerald-500/80 text-white rounded-full"
                                        @click="submitAvatarImage">
                                        <CheckCircleIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </template>
                        </div>
                        <div class="flex flex-wrap justify-between items-center flex-1 p-4">
                            <div class="mb-2">
                                <h2 class="font-bold text-lg">{{ user.name }}</h2>
                                <p class="text-xs text-gray-500">{{followerCount}} follower(s)</p>
                            </div>

                            <div v-if="!isMyProfile">
                                <PrimaryButton v-if="!isCurrentUserFollower" @click="followUser">
                                    Follow User
                                </PrimaryButton>
                                <DangerButton v-else @click="followUser">
                                    Unfollow User
                                </DangerButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-t m-4 mt-0">
                <TabGroup>
                    <TabList class="flex bg-white dark:bg-slate-950 dark:text-white">
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followers" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followings" :selected="selected" />
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected" />
                        </Tab>
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                            <TabItem text="My profile" :selected="selected" />
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel>
                            <template v-if="posts">
                                <CreatePost v-if="isMyProfile" />
                                <PostList :posts="posts" class="flex-1"/>
                            </template>
                            <div v-else class="py-8 text-center">
                                You don't have permission to view these posts.
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div class="mb-3">
                                <TextInput :model-value="searchFollowersKeyword" placeholder="Type to search"
                                           class="w-full"/>
                            </div>
                            <div v-if="followers.length" class="grid sm:grid-cols-2 gap-3">
                                <UserListItem v-for="user of followers"
                                              :user="user"
                                              :key="user.id"
                                              class="shadow rounded-lg"/>
                            </div>
                            <div v-else class="text-center dark:text-gray-100 py-8">
                                User does not have followers.
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <div class="mb-3">
                                <TextInput :model-value="searchFollowingsKeyword" placeholder="Type to search"
                                           class="w-full"/>
                            </div>
                            <div v-if="followings.length" class="grid sm:grid-cols-2 gap-3">
                                <UserListItem v-for="user of followings"
                                              :user="user"
                                              :key="user.id"
                                              class="shadow rounded-lg"/>
                            </div>
                            <div v-else class="text-center dark:text-gray-100 py-8">
                                The user is not following to anybody
                            </div>
                        </TabPanel>
                        <TabPanel>
                            <TabPhotos :photos="photos" />
                        </TabPanel>
                        <TabPanel v-if="isMyProfile">
                            <Edit :must-verify-email="mustVerifyEmail" :status="status" />
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped></style>
