<script setup>
import TextInput from "@/Components/TextInput.vue";
import FollowingItem from "@/Components/app/FollowingItem.vue";
import {ref} from "vue";

const searchKeyword = ref('')

</script>

<template>
    <TextInput :model-value="searchKeyword" placeholder="Type to search" class="w-full mt-3"/>
    <div class="mt-3 h-[200px] lg:flex-1 overflow-auto">
        <div v-if="false" class="text-gray-400 text-center p-3">
            You don't have friends yet.
        </div>
        <div v-else>
            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="John Doe"
            />

            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="Sarah Connor"
            />

            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="Paul Hireal"
            />

            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="George Must"
            />

            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="Wilfried Anderson"
            />

            <FollowingItem
                image="http://localhost:8000/storage/909-100x100.jpg"
                title="Elon Musk"
            />
        </div>
    </div>
</template>

<style scoped>

</style>
