<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    photos: {
        type: Array,
        required: true
    },
    initialIndex: {
        type: Number,
        default: 0
    },
    razdelId: {
        type: Number,
        default: 0
    },
    blockId: {
        type: Number,
        default: 0
    },
    componentId: {
        type: Number,
        default: 0
    },
    imageId: {
        type: Number,
        default: 0
    },
});

const emit = defineEmits(['close']);

const currentIndex = ref(props.initialIndex);

const currentImage = computed(() => {
    return props.photos[currentIndex.value] || {};
});

const nextImage = () => {
    if (currentIndex.value < props.photos.length - 1) {
        currentIndex.value++;
    }
};

const prevImage = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    }
};

const goToImage = (index) => {
    currentIndex.value = index;
};

function updateComponent(photo_id){
    router.post(route('components.store'), {
        razdel_id: props.razdelId,
        block_id: props.blockId,
        component_id: props.componentId,
        image_id: props.imageId,
        photo_id
    });
}

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
                class="absolute top-4 right-4 z-10 text-white p-2 rounded-full hover:bg-gray-800 transition"
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
                        @click="updateComponent(currentImage.id)"
                    />
                </transition>
            </div>

            <!-- Навигация -->
            <button
                v-if="currentIndex > 0"
                @click="prevImage"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white p-3 bg-gray-800 rounded-full hover:bg-gray-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <button
                v-if="currentIndex < photos.length - 1"
                @click="nextImage"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white p-3 bg-gray-800 rounded-full hover:bg-gray-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Индикатор -->
            <div class="absolute bottom-4 left-0 right-0 text-center text-white text-lg">
                {{ currentIndex + 1 }} / {{ photos.length }}
            </div>

            <!-- Миниатюры -->
            <div class="absolute bottom-0 left-0 right-0 flex justify-center gap-2 px-4 overflow-x-auto py-2">
                <div
                    v-for="(photo, index) in photos"
                    :key="photo.id"
                    @click="goToImage(index)"
                    class="w-16 h-16 cursor-pointer border-2 transition"
                    :class="{
                        'border-blue-500': index === currentIndex,
                        'border-transparent': index !== currentIndex
                    }"
                >
                    <img
                        :src="photo.thumbnail"
                        class="w-full h-full object-cover"
                        :alt="photo.title"
                    />
                </div>
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
