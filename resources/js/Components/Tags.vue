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
        default: 'Управление компонентами для блоков'
    },
    tags: {
        type: [Object, Array],
        default: () => []
    },
    err: {
        type: String
    }
});

// Форма для загрузки нового тега
const addForm = useForm({
    name: '',
    class: '',
});

// Форма для редактирования тегов
const editForm = useForm({
    tags: props.tags
});

const tagsCount = computed(() => Object.keys(props.tags).length + 1);

// Добавление нового тега
const submitAdd = () => {
    addForm.put(route('tags.create'), {
        preserveScroll: true,
        onSuccess: () => {
            addForm.reset();
            router.reload({ only: ['tags'] });
        }
    });
};

// Обновление тегов
const submitEdit = () => {
    editForm.post(route('tags.update'), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['tags'] });
        }
    });
};

// Удаление тега
const deleteTag = (tagId) => {
    if (confirm('Вы уверены, что хотите удалить этот тег?')) {
        router.delete(route('tags.destroy', { tag_id: tagId }), {
            preserveScroll: true,
            onSuccess: () => router.reload({ only: ['tags'] })
        });
    }
};

</script>

<template>
    <Head :title="dashboard_title" />

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Форма добавления нового тега -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
            <h2 class="text-lg font-medium mb-4">Добавить новый компонент</h2>
            <form @submit.prevent="submitAdd" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="name" value="Наименование *" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="addForm.name"
                            required
                        />
                        <InputError class="mt-1" :message="addForm.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="class" value="Класс" />
                        <TextInput
                            id="class"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="addForm.class"
                            required
                        />
                        <InputError class="mt-1" :message="addForm.errors.class" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <PrimaryButton
                        :class="{ 'opacity-50': addForm.processing }"
                        :disabled="addForm.processing"
                    >
                        <span v-if="addForm.processing">Добавление...</span>
                        <span v-else>Добавить тег</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- Список тегов для редактирования -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium">Редактирование компонентов</h2>
            </div>

            <div v-if="tagsCount" class="divide-y divide-gray-200">
                <form @submit.prevent="submitEdit">
                    <div v-for="(tag, index) in editForm.tags" :key="tag.id" class="p-2 hover:bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div>
                                <InputLabel v-if="index == 1" :for="`name-${tag.id}`" value="Наименование" />
                                <TextInput
                                    :id="`name-${tag.id}`"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="editForm.tags[index].name"
                                />
                            </div>

                            <div>
                                <InputLabel v-if="index == 1" :for="`class-${tag.id}`" value="Класс" />
                                <TextInput
                                    :id="`class-${tag.id}`"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="editForm.tags[index].class"
                                />
                            </div>

                            <div class="inline-flex items-center">
                                <label>
                                    <input
                                        type="checkbox"
                                        :id="`delete-${tag.id}`"
                                        v-model="editForm.tags[index].del"
                                        class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:border-indigo-300 focus:ring-3 focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">Удалить компонент</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <PrimaryButton
                            :class="{ 'opacity-50': editForm.processing }"
                            :disabled="editForm.processing"
                        >
                            <span v-if="editForm.processing">Сохранение...</span>
                            <span v-else>Сохранить изменения</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <div v-else class="p-6 text-center text-gray-500">
                Нет тегов для отображения
            </div>
        </div>
    </div>
</template>
