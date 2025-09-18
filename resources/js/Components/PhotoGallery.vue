<script setup>
import { ref } from 'vue';
import ModalGallery from '@/Components/ModalGallery.vue';

const props = defineProps({
    images: Array
});

const showModal = ref(false);
const currentImageIndex = ref(0);

const openModal = (index) => {
    currentImageIndex.value = index;
    showModal.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    document.body.style.overflow = '';
};
</script>

<template>
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-8">Галерея изображений</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
                v-for="(image, index) in images"
                :key="image.id"
                @click="openModal(index)"
                class="cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
            >
                <img
                    :src="image.thumbnail"
                    class="w-full h-48 object-cover"
                    :alt="image.title"
                    loading="lazy"
                >
                <div v-if="image.title" class="p-2 text-sm truncate">
                    {{ image.title }}
                </div>
            </div>
        </div>

        <ModalGallery
            v-if="showModal"
            :images="images"
            :initial-index="currentImageIndex"
            @close="closeModal"
        />
    </div>
</template>
