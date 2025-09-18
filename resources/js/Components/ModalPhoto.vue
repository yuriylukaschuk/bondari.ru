<script setup>
import { computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    photo: {
        type: Object,
        required: true
    },
});

const emit = defineEmits(['close']);

const currentImage = computed(() => {
    return props.photo || {};
});

const handleKeydown = (e) => {
    switch (e.key) {
        case 'Escape':
            emit('close');
            break;
        case 'ArrowLeft':
            prevImage();
            break;
        case 'ArrowRight':
            nextImage();
            break;
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90">
        <div class="relative w-full max-w-6xl max-h-screen">
            <!-- Кнопка закрытия -->
            <button
                @click="$emit('close')"
                class="absolute top-4 right-4 z-10 text-white p-2 rounded-full hover:bg-gray-200 transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Основное изображение -->
            <div class="flex justify-center items-center h-full top-0 cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition">
                <transition name="fade" mode="out-in">
                    <img
                        :key="currentImage.id"
                        :src="currentImage.filename"
                        class="max-h-[90vh] max-w-full object-contain"
                        :alt="currentImage.title"
                    />
                </transition>
            </div>

            <!-- Описание -->
            <div v-if="currentImage.title || currentImage.description"
                 class="absolute top-20 left-0 right-0 text-center text-white px-4">
                <h2 v-if="currentImage.title" class="text-xl font-bold">{{ currentImage.title }}</h2>
                <p v-if="currentImage.description" class="text-sm opacity-80">{{ currentImage.description }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Кастомный скролл для миниатюр */
::-webkit-scrollbar {
    height: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
