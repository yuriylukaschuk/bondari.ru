<script setup>
import { onMounted, ref, watch, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Array],
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);
const textarea = ref(null);

// Преобразуем массив в строку для textarea
const textValue = computed({
    get: () => {
        if (Array.isArray(props.modelValue)) {
            // Если пришел массив - объединяем элементы через перенос строки
          return props.modelValue.join('\n');
        }
        // Если пришла строка - возвращаем как есть
        return props.modelValue;
    },
    set: (value) => {
        // При изменении текста разбиваем строку на массив
        const lines = value.split('\n').filter(line => line.trim() !== '');
        emit('update:modelValue', lines);
    }
});

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea
        class="h-48 rounded-md border-gray-300 shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
        v-model="textValue"
        ref="textarea"
    />
</template>
