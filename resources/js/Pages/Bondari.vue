<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage()
const userdata = computed(() => page.props.userdata)

const props = defineProps({
    blocks: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    feedback: {
        type: [Object, Array],
        required: true,
        default: () => ([])
    },
    menuItems: {
        type: [Object, Array],
        default: () => usePage().props.menuItems || []
    },
    meta: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppLayout :blocks="blocks" :menuItems="menuItems" :feedback="feedback" :meta="meta" :userdata="userdata" />
    </div>
</template>
