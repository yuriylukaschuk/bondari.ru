<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref, reactive, computed, onMounted } from 'vue';
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
        type: Object,
        required: true,
    },
    err: {
        type: String
    },
});

// Форма для загрузки нового тега
const addForm = useForm({
    razdel_id: props.razdel_id || null,
    tag_id: null,
    block: null,
    number: null,
    photo_count: null,
    position_id: null
});


// Форма для редактирования тегов
const editForm = useForm({
    blocks: props.blocks
});

const flat = (arr, childrenKey = 'children') =>
  Array.isArray(arr)
    ? arr.flatMap(({ [childrenKey]: c, ...n }) => [
        n,
        ...flat(c, childrenKey),
      ])
    : [];

const PhotoCount = ref(10);
const blocks = reactive(props.blocks);
const razdelsList = computed(() => flat(props.razdels));
const blocksCount = computed(() => Object.keys(props.blocks).length + 1);
const hasBlocks = computed(() => blocksCount.value > 0);

// Вычисляемое свойство для количества позиций выбранного блока
const positionsCount = computed(() => {
    if (!addForm.block) return 0;
    // Преобразуем объект в массив и ищем блок
    const selectedBlock = Object.values(props.blocks).find(block => block.npp === addForm.block);
    return selectedBlock?.positions + 1 || 1;
});

// Вычисляемое свойство для массива позиций (1, 2, 3...)
const components = computed(() => {
    return Array.from({ length: positionsCount.value }, (_, i) => i + 1);
});

// Функция обработки изменения блока
function changeBlock() {
  // Сбрасываем выбранную позицию при изменении блока
  addForm.number = null;
  // Можно добавить дополнительную логику здесь
  // console.log(`Выбран блок ${addForm.block}, доступно позиций: ${positionsCount.value}`);
}

// Обработчик изменения раздела
const handleRazdelChange = () => {
    router.get(route('blocks.index'), {
        razdel_id: addForm.razdel_id
    }, {
        preserveState: true
    });
};

const submitAdd = () => {
    if (!addForm.tag_id){
        alert('Обязательно укажите компонент для размещения');
        return;
    }
    if (!addForm.block){
        alert('Обязательно укажите номер блока');
        return;
    }
    if (!addForm.number){
        alert('Обязательно укажите порядковый номер компонента в блоке');
        return;
    }
    if (addForm.tag_id == 8 && !addForm.photo_count){
        alert('Обязательно укажите количество фотографий в сете');
        return;
    }
    if (addForm.tag_id != 8 && !addForm.position_id){
        alert('Обязательно укажите расположение элемента в блоке');
        return;
    }
    addForm.post(route('blocks.create'), {
        preserveScroll: true, // Опционально: сохранить позицию скролла
        onSuccess: () => {
            // Действия после успешной отправки
            addForm.reset();
        },
        onError: (errors) => {
            // Обработка ошибок валидации
            console.error(errors);
        }
    });
};

const deleteComponent = (razdel_id, block_id, component_id) => {
    if (confirm('Вы уверены, что хотите удалить этот компонент?')) {
        router.put(route('components.delete'), {
            razdel_id,
            block_id,
            component_id
        }, {
            preserveScroll: true,
            // onSuccess: () => router.reload({ only: ['blocks'] })
        });
    }
}

const deleteBlock = (razdel_id, block_id, npp) => {
    if (confirm('Вы уверены, что хотите удалить этот блок?')) {
        router.post(route('blocks.destroy'), {
            razdel_id,
            block_id,
            npp
        }, {
            preserveScroll: true,
            // onSuccess: () => router.reload({ only: ['blocks'] })
        });
    }
}

// Вспомогательные функции
const getTagName = (tagId) => {
    return props.tags[tagId]?.name || 'Неизвестный тег';
};

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
</script>

<template>
    <Head :title="dashboard_title" />
    <div class="upload-form">
        <form @submit.prevent="submitAdd" class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium mb-4">Добавление блоков на страницу</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Раздел (страница) сайта
                </label>
                <select
                    v-model="addForm.razdel_id"
                    @change="handleRazdelChange"
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
                    v-model="addForm.block"
                    @change="changeBlock"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Укажите номер блока на странице</option>
                    <option
                        v-for="npp in blocksCount"
                        :key="npp"
                        :value="npp"
                    >
                        {{ npp }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.block" />
            </div>

            <div class="mt-2">
                <InputLabel for="number" value="Номер по порядку компонента в блоке" />
                <select
                    id="number"
                    v-model="addForm.number"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Укажите номер компонента в блоке</option>
                    <option
                        v-for="npp in components"
                        :key="npp"
                        :value="npp"
                    >
                        {{ npp }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.number" />
            </div>

            <div class="mt-2">
                <InputLabel for="tag_id" value="Компонент" />
                <select
                    id="tag_id"
                    v-model="addForm.tag_id"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Выберите компонент</option>
                    <option
                        v-for="tag in tags"
                        :key="tag.id"
                        :value="tag.id"
                    >
                        {{ tag.name }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.tag_id" />
            </div>

            <div v-if="addForm.tag_id == 8" class="mt-2">
                <InputLabel for="photo_count" value="Количество фотографий в блоке" />
                <select
                    id="photo_count"
                    v-model="addForm.photo_count"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Укажите номер компонента в блоке</option>
                    <option
                        v-for="count in PhotoCount"
                        :key="count"
                        :value="count"
                    >
                        {{ count }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.photo_count" />
            </div>

            <div v-else class="mt-2">
                <InputLabel for="position_id" value="Расположение компонента в блоке" />
                <select
                    id="position_id"
                    v-model="addForm.position_id"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Выберите компонент</option>
                    <option
                        v-for="position in positions"
                        :key="position.id"
                        :value="position.id"
                    >
                        {{ position.name }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.position_id" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :class="{ 'opacity-25': addForm.processing }"
                    :disabled="addForm.processing"
                >
                    Добавить
                </PrimaryButton>
            </div>

            <div v-if="err" class="text-red-700 font-bold m-2">
                {{ err }}
            </div>

        </form>
    </div>

    <!-- Форма редактирования существующих блоков -->
    <div class="container mx-auto p-4 max-w-6xl">
        <h3 class="text-lg font-medium mb-4">Редактирование блоков страницы</h3>
        <!-- Форма редактирования блоков -->
        <div v-if="hasBlocks" class="space-y-6">
            <div v-for="block in blocks" :key="block.id" class="bg-white rounded-lg shadow-md p-6">
                <!-- Заголовок блока -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Блок #{{ block.npp }} (ID: {{ block.id }})
                    </h3>
                    <span class="text-sm text-gray-500">
                        Компонентов: {{ block.components.length }}, 
                        Изображений: {{ block.images }}
                    </span>
                </div>

                <!-- Визуализация блока -->
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-4">
                    <!-- Блок с набором изображений -->
                    <div v-if="isImageSet(block)" class="image-set-container">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div 
                                v-for="component in block.components" 
                                :key="component.id"
                                class="component-item relative group"
                                :class="getTagClass(component.tag_id)"
                            >
                                <div class="component-content">
                                    <span class="text-sm font-medium">
                                        {{ getTagName(component.tag_id) }}
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1" v-if="component.element">
                                        {{ component.element }}
                                    </p>
                                </div>
                                <button 
                                    @click="deleteComponent(block.razdel_id, block.id, component.id)"
                                    class="absolute top-2 right-2 opacity-30 group-hover:opacity-100 transition-opacity bg-red-500 text-white p-1 rounded text-xs"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Блок с одним изображением и позиционированием -->
                    <div v-else-if="hasImages(block)" class="image-block-container">
                        <div class="flex flex-col md:flex-row gap-6" :class="{
                            'md:flex-row-reverse': getImagePosition(block) === 'photo-right'
                        }">
                            <!-- Изображение -->
                            <div class="md:w-1/3">
                                <div 
                                    v-for="component in block.components.filter(c => isImageTag(c.tag_id))"
                                    :key="component.id"
                                    class="component-item relative group mb-4"
                                    :class="getTagClass(component.tag_id)"
                                >
                                    <div class="component-content">
                                        <span class="text-sm font-medium">
                                            {{ getTagName(component.tag_id) }}
                                            (№ п/п - {{ component.number }})
                                        </span>
                                    </div>
                                    <button 
                                        @click="deleteComponent(block.razdel_id, block.id, component.id)"
                                        class="absolute top-2 right-2 opacity-30 group-hover:opacity-100 transition-opacity bg-red-500 text-white p-1 rounded text-xs"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>

                            <!-- Текстовые компоненты -->
                            <div class="md:w-2/3 space-y-4">
                                <div 
                                    v-for="component in block.components.filter(c => !isImageTag(c.tag_id))"
                                    :key="component.id"
                                    class="component-item relative group"
                                    :class="getTagClass(component.tag_id)"
                                >
                                    <div class="component-content">
                                        <span class="text-sm font-medium">
                                            {{ getTagName(component.tag_id) }}
                                            (№ п/п - {{ component.number }})
                                        </span>
                                        <p class="text-xs text-gray-600 mt-1" v-if="component.element">
                                            {{ component.element }}
                                        </p>
                                    </div>
                                    <button 
                                        @click="deleteComponent(block.razdel_id, block.id, component.id)"
                                        class="absolute top-2 right-2 opacity-30 group-hover:opacity-100 transition-opacity bg-red-500 text-white p-1 rounded text-xs"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Текстовый блок -->
                    <div v-else class="text-block-container">
                        <div class="space-y-4">
                            <div 
                                v-for="component in block.components"
                                :key="component.id"
                                class="component-item relative group"
                                :class="getTagClass(component.tag_id)"
                            >
                                <div class="component-content">
                                    <span class="text-sm font-medium">
                                        {{ getTagName(component.tag_id) }}
                                        (№ п/п - {{ component.number }})
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1" v-if="component.element">
                                        {{ component.element }}
                                    </p>
                                </div>
                                <button 
                                    @click="deleteComponent(block.razdel_id, block.id, component.id)"
                                    class="absolute top-2 right-2 opacity-30 group-hover:opacity-100 transition-opacity bg-red-500 text-white p-1 rounded text-xs"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопка удаления блока -->
                <div class="mt-4 flex justify-end">
                    <PrimaryButton
                        @click="deleteBlock(block.razdel_id, block.id, block.npp)"
                        class="bg-red-600 hover:bg-red-700"
                        :class="{ 'opacity-50': editForm.processing }"
                        :disabled="editForm.processing"
                    >
                        <span v-if="editForm.processing">Удаление...</span>
                        <span v-else>Удалить блок</span>
                    </PrimaryButton>
                </div>
            </div>
        </div>

        <!-- Сообщение об отсутствии блоков -->
        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500 text-lg">Нет блоков в выбранном разделе</p>
        </div>
    </div>
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
