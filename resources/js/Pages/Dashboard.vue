<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Razdels from '@/Components/Razdels.vue';
import Photos from '@/Components/Photos.vue';
import Tags from '@/Components/Tags.vue';
import Blocks from '@/Components/Blocks.vue';
import Components from '@/Components/Components.vue';
import Register from '@/Components/Register.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    dashboard : {
        type: String
    },
    dashboard_title: {
        type: String
    },
    lvl: {
        type: Number
    },
    parent_id: {
        type: Number
    },
    razdel_id: {
        type: Number
    },
    razdels: {
        type: Object,
        default: () => [],
    },
    photos: {
        type: [Object, Array],
        default: () => [],
    },
    tags: {
        type: [Object, Array],
        default: () => [],
    },
    block_id: {
        type: Number
    },
    blocks: {
        type: [Object, Array],
        default: () => [],
    },
    positions: {
        type: [Object, Array],
        default: () => [],
    },
    components: {
        type: [Object, Array],
        default: () => [],
    },
    err: {
        type: String
    }
});

const dashboard_title = ref(props.dashboard_title);
const dashboard = ref(props.dashboard);

</script>

<template>
    <Head :title="dashboard_title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ dashboard_title }}
            </h2>
        </template>

        <div class="py-2">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xs sm:rounded-lg">
                    <div v-if="dashboard === 'register'" class="p-6 text-gray-900">
                        <Register :components="components" :dashboard_title="dashboard_title" :err="err" :key="components" />
                    </div>
                    <div v-if="dashboard === 'razdels'" class="p-6 text-gray-900">
                        <Razdels :lvl="lvl" :parent_id="parent_id" :razdels="razdels" :dashboard_title="dashboard_title" :err="err" :key="razdels" />
                    </div>
                    <div v-else-if="dashboard === 'photos'" class="p-6 text-gray-900">
                        <Photos :photos="photos" :razdel_id="razdel_id" :razdels="razdels" :dashboard_title="dashboard_title" :err="err" :key="photos" />
                    </div>
                    <div v-else-if="dashboard === 'tags'" class="p-6 text-gray-900">
                        <Tags :tags="tags" :dashboard_title="dashboard_title" :err="err" :key="tags" />
                    </div>
                    <div v-else-if="dashboard === 'blocks'" class="p-6 text-gray-900">
                        <Blocks :razdel_id="razdel_id" :razdels="razdels" :blocks="blocks" :tags="tags" :positions="positions" :dashboard_title="dashboard_title" :err="err" :key="blocks" />
                    </div>
                    <div v-else-if="dashboard === 'components'" class="p-6 text-gray-900">
                        <Components :components="components" :razdel_id="razdel_id" :razdels="razdels" :block_id="block_id" :blocks="blocks" :tags="tags" :positions="positions" :photos="photos" :dashboard_title="dashboard_title" :err="err" :key="components" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
