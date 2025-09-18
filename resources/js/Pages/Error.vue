<script setup>
import { Head } from '@inertiajs/vue3';
import Layout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    status: Number,
    message: String,
});
</script>

<template>
    <Layout>
        <Head :title="`Ошибка ${status}`" />

        <div class="flex flex-col items-center justify-center min-h-screen py-12">
            <h1 class="text-4xl font-bold text-red-600 mb-4">
                Ошибка {{ status }}
            </h1>
            <p class="text-lg text-gray-600">
                {{ message }}
            </p>
            <Link
                href="/"
                class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-sm hover:bg-blue-700"
            >
                На главную
            </Link>
        </div>
    </Layout>
</template>
