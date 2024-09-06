<script setup>
import {computed, ref} from 'vue'
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue'
import {Head, useForm, usePage} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {XMarkIcon, CheckCircleIcon} from "@heroicons/vue/24/solid";

const imagesForm = useForm({
    cover:null,
    avatar:null,
})

const showNotification = ref(true)

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
    user: {
        type: Object
    }
});

const authUser = usePage().props.auth.user;
const isMyProfile = computed(() => authUser && authUser.id === props.user.id)

const coverImageSrc = ref('')

function onCoverChange (event) {
    imagesForm.cover = event.target.files[0]
    if (imagesForm.cover) {
        const reader = new FileReader()
        reader.onload = () => {
            coverImageSrc.value = reader.result
        }
        reader.readAsDataURL(imagesForm.cover)
    }
}

function cancelCoverImage () {
    coverImageSrc.value = null
    imagesForm.cover = null
}

function submitCoverImage () {
    imagesForm.post(route('profile.updateImage'), {
        onSuccess: () => {
            cancelCoverImage()
            setTimeout(()=>{
                showNotification.value = false
            }, 3000)
        }
    })
}

</script>

<template>
    <Head title="Profile"  />

    <AuthenticatedLayout>
        <div class="max-w-[768px] mx-auto h-full overflow-auto">
            <div
                v-show="showNotification && props.notification === 'cover-image-update'"
                class="my-2 py-2 px-3 font-medium text-sm bg-emerald-500 text-white"
            >
                Your cover image has been updated
            </div>
            <div
                v-if="errors.cover"
                class="my-2 py-2 px-3 font-medium text-sm bg-red-400 text-white"
            >
                {{ errors.cover }}
            </div>
            <div class="relative bg-white group">
                <img :src="coverImageSrc || props.user.cover_url || '/img/default_cover.jpg'"
                    class="w-full h-[200px] object-cover" alt="cover-image">
                <div class="absolute top-2 right-2">
                    <button v-if="!coverImageSrc" class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center opacity-0 group-hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-3 h-3 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/>
                        </svg>

                        Update Cover image
                        <input type="file" accept="image/*" class="absolute top-0 left-0 bottom-0 right-0 cursor-pointer opacity-0"
                               @change="onCoverChange"
                        />
                    </button>
                    <div v-else class="flex gap-2 bg-white p-2 opacity-0 group-hover:opacity-100">
                        <button
                            class="bg-gray-50 hover:bg-gray-100 text-gray-800 py-1 px-2 text-xs flex items-center"
                            @click="cancelCoverImage"
                        >
                            <XMarkIcon class="h-3 w-3 mr-1"/>
                            Cancel
                        </button>
                        <button class="bg-gray-800 hover:bg-gray-900 text-gray-100 py-1 px-2 text-xs flex items-center"
                                @click="submitCoverImage"
                        >
                            <CheckCircleIcon class="h-3 w-3 mr-1"/>
                            Submit
                        </button>
                    </div>
                </div>
                <div class="flex">
                    <img src="http://localhost:8000/storage/avatar.jpg"
                         class="ml-[48px] w-[128px] h-[128px] -mt-[64px]" alt="avatar-image">
                    <div class="flex justify-between items-center flex-1 p-4">
                        <h2 class="font-bold text-lg">{{ props.user.name }}</h2>
                        <PrimaryButton v-if="isMyProfile">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-4 h-4 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
                            </svg>
                            Edit Profile
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <div class="border-t">
                <TabGroup>
                    <TabList class="flex bg-white">
                        <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
                            <TabItem text="About" :selected="selected"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Posts" :selected="selected"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followers" :selected="selected"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Followings" :selected="selected"/>
                        </Tab>
                        <Tab v-slot="{ selected }" as="template">
                            <TabItem text="Photos" :selected="selected"/>
                        </Tab>
                    </TabList>

                    <TabPanels class="mt-2">
                        <TabPanel v-if="isMyProfile" >
                            <Edit :must-verify-email="mustVerifyEmail" :status="status"/>
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Posts
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Followers
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Followings
                        </TabPanel>
                        <TabPanel class="bg-white p-3 shadow">
                            Photos
                        </TabPanel>
                    </TabPanels>
                </TabGroup>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
