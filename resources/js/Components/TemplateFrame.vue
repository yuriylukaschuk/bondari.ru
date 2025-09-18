<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    component: {
        type: String,
        requered: true,
    },
});

const component = ref(props.component);

// console.log(component.value);

</script>

<template>
    <div v-html="component"></div>
</template>
