<script setup>
import InputTextarea from "@/Components/InputTextarea.vue";
import Checkbox from "@/Components/Checkbox.vue";
import TextInput from "@/Components/TextInput.vue";

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

        <InputTextarea v-model="form.about" class="w-full"/>

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
