<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    dashboard_title: {
        type: String,
        default: 'Управление фотографиями'
    },
    razdel_id: {
        type: [Number, String]
    },
    razdels: {
        type: [Object, Array],
        required: true,
    },
    photos: {
        type: [Object, Array],
        default: () => [],
    },
    err: {
        type: String
    },
});

// Форма для загрузки новых фото
const uploadForm = useForm({
    razdel_id: props.razdel_id || null,
    photo: null,
    title: '',
    description: '',
});

// Форма для редактирования существующих фото
const editForm = useForm({
    photos: props.photos
});

const dashboard_title = ref(props.dashboard_title);

const flat = (arr, childrenKey = 'children') =>
  Array.isArray(arr)
    ? arr.flatMap(({ [childrenKey]: c, ...n }) => [
        n,
        ...flat(c, childrenKey),
      ])
    : [];

const razdels = computed(() => flat(props.razdels));

function onFileChange(event) {
    uploadForm.photo = event.target.files[0];
}

const submitUpload = () => {
    if (!uploadForm.photo){
        alert('Обязательно укажите фото для добавления на сайт');
        return;
    }
    uploadForm.post(route('photos.upload'), {
        forceFormData: true,
        onSuccess: () => {
            uploadForm.reset('photo', 'title', 'description');
            // Обновляем список фото после загрузки
            // router.reload({ only: ['razdel_id','photos'] });
        }
    });
};

// Обработчик изменения раздела
const handleRazdelChange = () => {
    router.get(route('photos.index'), {
        razdel_id: uploadForm.razdel_id
    }, {
        preserveState: true
    });
};

const submitEdit = () => {
    editForm.put(route('photos.update'),{
        preserveScroll: true,
    })
};
</script>

<template>

    <Head :title="dashboard_title" />

    <div class="upload-form">
        <form @submit.prevent="submitUpload" class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium mb-4">Загрузка фотографий</h3>
            <!-- Выбор раздела -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Раздел (страница) сайта
                </label>
                <select
                    v-model="uploadForm.razdel_id"
                    @change="handleRazdelChange"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Укажите раздел сайта</option>
                    <option
                        v-for="razdel in razdels"
                        :key="razdel.id"
                        :value="razdel.id"
                    >
                        {{ razdel.title }}
                    </option>
                </select>
            </div>

            <div class="mt-4">
                <InputLabel for="photo" value="Выберите файл для загрузки" />
                <input
                    type="file"
                    id="photo"
                    @change="onFileChange"
                    class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100"
                >
            </div>

            <div class="mt-4">
                <InputLabel for="title" value="Наименование фотографии" />
                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="uploadForm.title"
                    autofocus
                    autocomplete="title"
                />
                <InputError class="mt-2" :message="uploadForm.errors.title" />
            </div>

            <div class="mt-4">
                <InputLabel for="description" value="Описание фотографии" />
                <TextInput
                    id="description"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="uploadForm.description"
                    autocomplete="description"
                />
                <InputError class="mt-2" :message="uploadForm.errors.description" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :class="{ 'opacity-25': uploadForm.processing }"
                    :disabled="uploadForm.processing"
                >
                    Загрузить
                </PrimaryButton>
            </div>
        </form>

        <!-- Форма редактирования существующих фото -->
        <form @submit.prevent="submitEdit" class="p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium mb-4">Редактирование фотографий</h3>
            <div v-if="editForm.photos.length" class="space-y-4">
                <div
                    v-for="(photo, index) in editForm.photos"
                    :key="photo.id"
                    class="flex items-start p-4 border rounded-md bg-white"
                >
                    <div class="shrink-0 mr-4">
                        <img
                            :src="photo.thumbnail"
                            :alt="photo.description"
                            class="object-cover w-32 h-32 rounded-md"
                        >
                    </div>
                    <div class="flex-1 space-y-2">
                        <div>
                            <InputLabel :for="'title-'+photo.id" value="Название" />
                            <TextInput
                                :id="'title-'+photo.id"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="editForm.photos[index].title"
                            />
                        </div>
                        <div>
                            <InputLabel :for="'desc-'+photo.id" value="Описание" />
                            <textarea
                                :id="'desc-'+photo.id"
                                v-model="editForm.photos[index].description"
                                rows="2"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                :id="'delete-' + photo.id"
                                v-model="editForm.photos[index].del"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded-sm focus:ring-indigo-500"
                            >
                            <label
                                :for="'delete-' + photo.id"
                                class="ml-2 text-sm text-gray-600"
                            >
                                Удалить фотографию
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="p-4 text-center text-gray-500 bg-gray-100 rounded-md">
                Нет фотографий в выбранном разделе
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    class="px-4 py-2"
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                >
                    <span v-if="editForm.processing">Сохранение...</span>
                    <span v-else>Сохранить изменения</span>
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<style scoped>
.upload-form {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
}
</style>
