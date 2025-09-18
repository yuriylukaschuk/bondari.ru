<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModalGallery from '@/Components/ModalGallery.vue';

import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    dashboard_title: {
        type: String
    },
    razdel_id: {
        type: Number
    },
    razdels: {
        type: [Object, Array],
        required: true,
    },
    block_id: {
        type: Number
    },
    blocks: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    tags: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    positions: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    components: {
        type: [Object, Array],
        required: true,
    },
    photos: {
        type: Array,
        required: true,
    },
    err: {
        type: String
    },
});

const selectForm = useForm({
    razdel_id: props.razdel_id || null,
    block_id: props.block_id || null,
});

// Форма для редактирования компонент
const editForm = useForm({
    razdel_id: props.razdel_id || null,
    block_id: props.block_id || null,
    components: props.components
});

const showModal = ref(false);
const currentImageIndex = ref(1);
const componentId = ref(0);
const imageId = ref(0);
const block = props.blocks[props.block_id];
const block_npp = computed(() => props.block_id ? block.npp : 0);
const block_positions = computed(() => props.block_id ? block.positions : 0);
const block_images = computed(() => props.block_id ? block.images : 0);

function transformArray(obj){
    const keys = Object.keys(obj); // Получаем массив ключей: ['a', 'b', 'c']
    const result = keys.map(key => obj[key]); // Используем map для создания нового массива значений
    return result;    
}
const components = computed(() => transformArray(props.components));

const openModal = (index, component_id, image_id) => {
    currentImageIndex.value = index;
    componentId.value = component_id;
    imageId.value = image_id;
    showModal.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    document.body.style.overflow = '';
};

const flat = (arr, childrenKey = 'children') =>
  Array.isArray(arr)
    ? arr.flatMap(({ [childrenKey]: c, ...n }) => [
        n,
        ...flat(c, childrenKey),
      ])
    : [];

const razdelsList = computed(() => flat(props.razdels));

// Обработчик изменения раздела
const handleSelectChange = (razdel_id, block_id) => {
    router.get(route('components.index'), {
        razdel_id, block_id
    }, {
        preserveState: true
    });
};

const submitEdit = () => {
    editForm.post(route('components.update', {
        razdel_id: props.razdel_id,
        block_id: props.block_id
    }), {
        preserveScroll: true,
    });
};

// Вспомогательные функции
const getTagName = (tagId) => {
    return props.tags[tagId]?.name || 'Неизвестный тег';
};

const getPositionName = (positionId) => {
    return props.positions[positionId]?.name || 'Позиция не указана';
}

const getTagClass = (tagId) => {
    return props.tags[tagId]?.comp_input || '';
};

const isImageTag = (tagId) => {
    return [7, 8].includes(tagId); // Фотография или ряд фотографий
};

const hasImages = (block) => {
    return block.images > 0;
};

const isImageSet = (block) => {
    return block.imageset === 1;
};

const getImagePosition = (block) => {
    return block.image_position;
};

const isTextInput = (tagName) => {
    return ['title-main', 'title-sub'].includes(tagName)
}

const isTextAreaInput = (tagName) => {
    return ['text-large', 'text-small', 'list-numbered', 'list-bulleted'].includes(tagName)
}
</script>

<template>
    <Head :title="dashboard_title" />

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Форма добавления нового тега -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium mb-4">Выберите необходимый раздел (страницу сайта) и блок</h2>
            <form>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Раздел (страница) сайта
                        </label>
                        <select
                            v-model="selectForm.razdel_id"
                            @change="handleSelectChange(selectForm.razdel_id, 0)"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option disabled :value="null">Укажите раздел сайта</option>
                            <option
                                v-for="razdel in razdelsList"
                                :key="razdel.id"
                                :value="razdel.id"
                            >
                                {{ razdel.title }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <InputLabel for="blocks" value="Номер блока на странице" />
                        <select
                            id="blocks"
                            v-model="selectForm.block_id"
                            @change="handleSelectChange(selectForm.razdel_id, selectForm.block_id)"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option disabled :value="null">Укажите номер блока на странице</option>
                            <option
                                v-for="block in blocks"
                                :key="block.id"
                                :value="block.id"
                            >
                                {{ block.npp }}
                            </option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Форма редактирования существующих блоков -->
    <form @submit.prevent="submitEdit" class="p-4 bg-gray-50 rounded-lg">
        <div class="container mx-auto p-4 max-w-6xl">
            <h3 class="text-lg font-medium mb-4">Заполните компоненты содержимым</h3>
            <!-- Форма редактирования блоков -->
            <div v-if="block_id" class="space-y-6">
                <!-- Заголовок блока -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Блок #{{ block_npp }} (ID: {{ block_id }})
                    </h3>
                    <span class="text-sm text-gray-500">
                        Компонентов: {{ block_positions }}, 
                        Изображений: {{ block_images }}
                    </span>
                </div>

                <!-- Визуализация блока -->
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-4">
                    <!-- Блок с набором изображений -->
                    <div v-if="isImageSet(block)" class="image-set-container">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div 
                                v-for="component in components" 
                                :key="component.id"
                                class="component-item relative group"
                                :class="getTagClass(component.tag_id)"
                            >
                                <div
                                    v-if="component.image_id == 0"
                                    @click="openModal(currentImageIndex, component.id, component.image_id)"
                                    class="component-content cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                >
                                    <span class="text-sm font-medium">
                                        {{ getTagName(component.tag_id) }}
                                    </span>
                                </div>
                                <div
                                    v-else
                                    class="component-content"
                                >
                                    <p class="text-xs text-gray-600 mt-1">
                                        <img
                                            :src="component.image"
                                            :alt="component.description"
                                            @click="openModal(currentImageIndex, component.id, component.image_id)"
                                            class="cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                        >
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="hasImages(block)" class="image-block-container">
                        <div class="flex flex-col md:flex-row gap-6" :class="{
                            'md:flex-row-reverse': getImagePosition(block) === 'photo-right'
                        }">
                            <div class="md:w-1/3">
                                <div 
                                    v-for="component in components.filter(c => isImageTag(c.tag_id))"
                                    :key="component.id"
                                    class="component-item relative group mb-4"
                                    :class="getTagClass(component.tag_id)"
                                >
                                    <div
                                        v-if="component.image_id == 0"
                                        @click="openModal(currentImageIndex, component.id, component.image_id)"
                                        class="component-content cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                    >
                                        <span class="text-sm font-medium">
                                            {{ getTagName(component.tag_id) }}
                                            ({{ getPositionName(component.position_id) }})
                                        </span>
                                    </div>
                                    <div
                                        v-else
                                        class="component-content"
                                    >
                                        <p class="text-xs text-gray-600 mt-1">
                                            <img
                                                :src="component.image"
                                                :alt="component.description"
                                                @click="openModal(currentImageIndex, component.id, component.image_id)"
                                                class="cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                            >
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-4">
                                <div 
                                    v-for="component in components.filter(c => !isImageTag(c.tag_id))"
                                    :key="component.id"
                                    class="component-item relative group"
                                    :class="getTagClass(component.tag_id)"
                                >
                                    <div v-if="isTextInput(component.tag_class)" class="component-content">
                                        <span class="text-sm font-medium">
                                            {{ getTagName(component.tag_id) }}
                                            ({{ getPositionName(component.position_id) }})
                                        </span>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <TextInput
                                                :id="component.tag_id"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="editForm.components[component.id].text"
                                            />
                                        </p>
                                    </div>
                                    <div v-else-if="isTextAreaInput(component.tag_class)" class="component-content">
                                        <span class="text-sm font-medium">
                                            {{ getTagName(component.tag_id) }}
                                            ({{ getPositionName(component.position_id) }})
                                        </span>
                                        <TextareaInput
                                            :id="component.tag_id"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="editForm.components[component.id].text"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-block-container">
                        <div class="space-y-4">
                            <div 
                                v-for="component in components"
                                :key="component.id"
                                class="component-item relative group"
                                :class="getTagClass(component.tag_id)"
                            >
                                <div v-if="isTextInput(component.tag_class)" class="component-content">
                                    <span class="text-sm font-medium">
                                        {{ getTagName(component.tag_id) }}
                                        ({{ getPositionName(component.position_id) }})
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <TextInput
                                            :id="component.tag_id"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="editForm.components[component.id].text"
                                        />
                                    </p>
                                </div>
                                <div v-else-if="isTextAreaInput(component.tag_class)" class="component-content">
                                    <span class="text-sm font-medium">
                                        {{ getTagName(component.tag_id) }}
                                        ({{ getPositionName(component.position_id) }})
                                    </span>
                                    <TextareaInput
                                        :id="component.tag_id"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="editForm.components[component.id].text"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Кнопка сохранения блока -->
                <div class="mt-4 flex justify-end">
                    <PrimaryButton
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        :class="{ 'opacity-50': editForm.processing }"
                        :disabled="editForm.processing"
                    >
                        <span v-if="editForm.processing">Удаление...</span>
                        <span v-else>Сохранить изменения</span>
                    </PrimaryButton>
                </div>
                <!-- Галерея изображений -->
                <div class="container mx-auto py-8">
                    <ModalGallery
                        v-if="showModal"
                        :initial-index="currentImageIndex"
                        :photos="props.photos"
                        :razdel-id="selectForm.razdel_id"
                        :block-id="selectForm.block_id"
                        :component-id="componentId"
                        :image-id="imageId"
                        @close="closeModal"
                    />
                </div>
            </div>
        </div>
    </form>
</template>

<style scoped>
.container {
  max-width: 1200px;
}

.upload-form {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
}

.component-item {
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  transition: all 0.2s ease;
}

.component-item:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
/* Стили для различных типов компонентов */

.title-main {
  @apply text-2xl font-bold bg-blue-50 p-4;
}

.title-sub {
  @apply text-xl font-semibold bg-blue-100 p-4;
}

.text-large {
  @apply text-lg bg-gray-50 p-4;
}

.text-small {
  @apply text-base bg-gray-100 p-4;
}

.list-numbered {
  @apply bg-orange-50 p-4 list-decimal list-inside;
}

.list-bulleted {
  @apply bg-orange-100 p-4 list-disc list-inside;
}

.photo {
  @apply bg-gray-200 p-4 flex items-center justify-center;
  min-height: 200px;
  background-image: url("/images/noimage.png");
  background-size: cover;
  background-position: center;
}

.photo-set {
  @apply bg-gray-300 p-4 flex items-center justify-center;
  min-height: 150px;
  background-image: url("/images/noimage.png");
  background-size: cover;
  background-position: center;
}

.image-set-container {
  @apply from-purple-50 to-pink-50 p-4 rounded-lg;
}

.image-block-container {
  @apply from-green-50 to-blue-50 p-4 rounded-lg;
}

.text-block-container {
  @apply from-yellow-50 to-orange-50 p-4 rounded-lg;
}
</style>
