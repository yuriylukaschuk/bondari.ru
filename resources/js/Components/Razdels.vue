<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref, reactive, computed, onMounted, getCurrentInstance } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    dashboard_title: {
        type: String
    },
    lvl: {
        type: [Number, String]
    },
    parent_id: {
        type: Number
    },
    razdels: {
        type: Object
    },
    err: {
        type: String
    }
});

// Форма для загрузки нового раздела
const addForm = useForm({
    parent_id: props.parent_id,
    lvl: props.lvl,
    npp: null,
    title: null,
    description: null,
    keywords: null,
    url: null,
    feedback: 0,
    feedback_title: '',
});

// Форма для редактирования разделов
const editForm = useForm({
    razdels: props.razdels
});

const dashboard_title = ref(props.dashboard_title);
const razdelsCount = computed(() => Object.keys(props.razdels).length + 1);
const numbers = ref(Array.from({ length: razdelsCount.value }, (_, i) => i + 1));
const feedbackForm = ref([
    {'value' : 0, 'label' : 'Нет', },
    {'value' : 1, 'label' : 'Есть', }
]);

const submitUp = () => {
    router.get(route('razdels.list'), {
        parent_id: 0,
        lvl: 1,
    }, {
        preserveState: true
    });
}

// Добавление нового раздела
const submitAdd = () => {
    addForm.put(route('razdels.store', {
        parent_id: props.parent_id,
        lvl: props.lvl,
    }), {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            router.reload({ only: ['razdels'] });
        }
    });
};

// Обновление разделов
const submitUpdate = () => {
    editForm.post(route('razdels.update', {
        parent_id: props.parent_id,
        lvl: props.lvl,
    }), {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['razdels'] })
    });
};

// Редактирование раздела
const submitChange = (razdelId) => {
    router.get(route('razdels.list'), {
        razdel_id: razdelId
    }, {
        preserveState: true
    });
};
</script>

<template>
    <Head :title="dashboard_title" />

    <div class="upload-form">
        <!-- Форма добавления нового раздела -->
        <form @submit.prevent="submitAdd" class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium mb-4">Добавление нового раздела на {{ props.lvl }} уровень</h3>
            <div>
                <InputLabel for="npp" value="№ п/п" />
                <select
                    id="npp"
                    v-model="addForm.npp"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Номер раздела на текущем уровне</option>
                    <option
                        v-for="npp in numbers"
                        :key="npp"
                        :value="npp"
                        :selected="npp == razdelsCount"
                    >
                        {{ npp }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.npp" />
            </div>

            <div>
                <InputLabel for="title" value="Наименование раздела *" />
                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="addForm.title"
                    required
                />
                <InputError class="mt-1" :message="addForm.errors.title" />
            </div>
            <div>
                <InputLabel for="description" value="Описание" />
                <TextInput
                    id="description"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="addForm.description"
                />
                <InputError class="mt-1" :message="addForm.errors.description" />
            </div>
            <div>
                <InputLabel for="keywords" value="Ключевые слова" />
                <TextInput
                    id="keywords"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="addForm.keywords"
                />
                <InputError class="mt-1" :message="addForm.errors.keywords" />
            </div>
            <div>
                <InputLabel for="url" value="Адрес страницы" />
                <TextInput
                    id="url"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="addForm.url"
                />
                <InputError class="mt-1" :message="addForm.errors.url" />
            </div>
            <div>
                <InputLabel for="feedback" value="Наличие формы обратной связи" />
                <select
                    id="feedback"
                    v-model="addForm.feedback"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option disabled :value="null">Наличие формы обратной связи</option>
                    <option
                        v-for="feedback in feedbackForm"
                        :key="feedback.value"
                        :value="feedback.value"
                    >
                        {{ feedback.label }}
                    </option>
                </select>
                <InputError class="mt-1" :message="addForm.errors.feedback" />
            </div>
            <div>
                <InputLabel for="feedback_title" value="Призыв на форме обратной связи" />
                <TextInput
                    id="feedback_title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="addForm.feedback_title"
                />
                <InputError class="mt-1" :message="addForm.errors.feedback_title" />
            </div>
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    :class="{ 'opacity-25': addForm.processing }"
                    :disabled="addForm.processing"
                >
                    <span v-if="addForm.processing">Добавление...</span>
                    <span v-else>Добавить раздел</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Форма редактирования существующих фото -->
        <div v-if="razdelsCount" class="space-y-4">
            <form class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium mb-4">Редактирование разделов {{ props.lvl }}-го уровня</h3>
                <div
                    v-for="(razdel, index) in editForm.razdels"
                    :key="razdel.id"
                    class="p-6 hover:bg-gray-50"
                >
                    <div class="mt-2">
                        <InputLabel :for="`npp-${razdel.id}`" value="Раздел № " />
                        <select
                            :id="`npp-${razdel.id}`"
                            v-model="editForm.razdels[index].npp"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option disabled :value="null">Укажите номер п/п раздела на текущем уровне</option>
                            <option
                                v-for="npp in numbers"
                                :key="npp"
                                :value="npp"
                                :selected="npp === editForm.razdels[index].npp"
                            >
                                {{ npp }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`title-${razdel.id}`" value="Наименование раздела" />
                        <TextInput
                            :id="`title-${razdel.id}`"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="editForm.razdels[index].title"
                        />
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`description-${razdel.id}`" value="Описание" />
                        <TextInput
                            :id="`description-${razdel.id}`"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="editForm.razdels[index].description"
                        />
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`keywords-${razdel.id}`" value="Ключевые слова" />
                        <TextInput
                            :id="`keywords-${razdel.id}`"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="editForm.razdels[index].keywords"
                        />
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`url-${razdel.id}`" value="Адрес страницы" />
                        <TextInput
                            :id="`url-${razdel.id}`"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="editForm.razdels[index].url"
                        />
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`feedback-${razdel.id}`" value="Наличие формы обратной связи" />
                        <select
                            :id="`feedback-${razdel.id}`"
                            v-model="editForm.razdels[index].feedback"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-xs focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option disabled :value="null">Наличие формы обратной связи</option>
                            <option
                                v-for="feedback in feedbackForm"
                                :key="feedback.value"
                                :value="feedback.value"
                                :selected="feedback.value === editForm.razdels[index].feedback"
                            >
                                {{ feedback.label }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-2">
                        <InputLabel :for="`feedback_title-${razdel.id}`" value="Призыв на форме обратной связи" />
                        <TextInput
                            :id="`feedback_title-${razdel.id}`"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="editForm.razdels[index].feedback_title"
                        />
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                :id="`delete-${razdel.id}`"
                                v-model="editForm.razdels[index].del"
                                class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-600">Удалить раздел</span>
                        </label>
                        <button
                            type="button"
                            @click="submitChange(razdel.id)"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            :class="{ 'opacity-50': editForm.processing }"
                        >
                            Редактировать
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4">
                    <div v-if="props.lvl > 1">
                        <a
                            href="#"
                            class="btn px-4 py-2 text-sm font-medium text-white bg-blue-300 rounded-md hover:bg-blue-400 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-blue-200"
                            @click.prevent="submitUp"
                        >
                            На предыдущий уровень
                        </a>
                    </div>
                    <div class="text-right">
                        <PrimaryButton
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            :class="{ 'opacity-50': editForm.processing }"
                            :disabled="editForm.processing"
                            @click="submitUpdate"
                        >
                            <span v-if="editForm.processing">Сохранение...</span>
                            <span v-else>Сохранить изменения</span>
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>
        <div v-else class="p-4 text-center text-gray-500 bg-gray-100 rounded-md">
            Нет разделов для отображения
        </div>
    </div>
</template>

<style scoped>
.upload-form {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
}
</style>
