<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    dashboard_title: {
        type: String
    },
    components: {
        type: Object,
        required: true,
    },
    err: {
        type: String
    },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: 2,
    permissions: []
});

const roles = reactive(props.components.roles);
const permissions = reactive(props.components.permissions);

const submit = () => {
    if (!form.permissions.length){
        alert('Обязательно укажите разрешение для работы пользователя');
        return;
    }
    if (form.password !== form.password_confirmation){
        alert('Пароли не совпадают');
        return;
    }
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Функция для проверки, выбрано ли разрешение
const isPermissionChecked = (permissionId) => {
    return form.permissions.includes(permissionId);
};
</script>

<style scoped>
.register-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.register-select {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #d1d5db;
    margin-top: 4px;
}
</style>

<template>
    <Head :title="dashboard_title" />

    <form @submit.prevent="submit" class="register-form">
        <div>
            <InputLabel for="roles" value="Роль" />

            <select id="roles" v-model="form.role_id" class="register-select">
                <option disabled value="0">Назначте роль пользователю</option>
                <option v-for="role in roles" :key="role.id" :value="role.id" :selected="form.role_id == role.id">
                    {{ role.name }}
                </option>
            </select>
        </div>

        <div class="mt-4">
            <InputLabel for="permissions" value="Выданные разрешения" />
            <div id="permissions" v-for="permission in permissions" :key="permission.id" class="flex items-center gap-2">
                <input
                    type="checkbox"
                    :id="'permission-' + permission.id"
                    :value="permission.id"
                    v-model="form.permissions"
                    :checked="isPermissionChecked(permission.id)"
                    class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500"
                >
                <label :for="'permission-' + permission.id">{{ permission.name }}</label>
            </div>
            <InputError class="mt-2" :message="form.errors.permissions" />
        </div>

        <div class="mt-4">
            <InputLabel for="name" value="Имя пользователя" />

            <TextInput
                id="name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="mt-4">
            <InputLabel for="email" value="E-mail" />
            <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                required
                autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="mt-4">
            <InputLabel for="password" value="Пароль" />
            <TextInput
                id="password"
                type="password"
                class="mt-1 block w-full"
                v-model="form.password"
                required
                autocomplete="new-password"
            />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
            <InputLabel
                for="password_confirmation"
                value="Повторить пароль"
            />
            <TextInput
                id="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
            />
            <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
            />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <PrimaryButton
                class="ms-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Зарегистрировать пользователя
            </PrimaryButton>
        </div>
    </form>
</template>
