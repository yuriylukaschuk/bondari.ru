<script setup>
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';

const page = usePage()
const userdata = computed(() => page.props.userdata)

const props = defineProps({
    blocks: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    menuItems: {
        type: [Object, Array],
        default: () => usePage().props.menuItems || []
    },
    feedback: {
        type: [Object, Array],
        required: true,
        default: () => ([])
    },
    meta: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
})

const openFeedback = ref(false);

const openFeedbackWindow = () => {
    openFeedback.value = true;
}

// Форма для загрузки новых фото
const feedbackForm = useForm({
    name: '',
    email: '',
    message: '',
});

const feedbackSubmit = () => {
    feedbackForm.post(route('feedback.send'), {
        onSuccess: () => {
            openFeedback.value = false;
        }
    });
};

</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppLayout :blocks="blocks" :menuItems="menuItems" :feedback="feedback" :meta="meta" :userdata="userdata">

            <!-- MainPageTopSection -->
            <template v-slot:MainPageTopSection>
                <section class="relative bg-amber-50 py-20 img-bg-section">
                    <div class="container mx-auto px-4">
                        <div class="text-center">
                            <h1 class="text-4xl md:text-6xl font-bold text-amber-500 mb-6">
                                Бочки для вина и коньяка from Github
                            </h1>
                            <p class="text-xl text-amber-300 mb-8 max-w-3xl mx-auto">
                                Качественные дубовые бочки ручной работы для идеального созревания напитков
                            </p>
                            <button @click="openFeedbackWindow" class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-lg transition-colors">
                                Заказать консультацию
                            </button>
                        </div>
                    </div>
                </section>
            </template>

            <template v-if="openFeedback" v-slot:FeedbackForm>
                <Transition class="container mx-auto p-4 max-w-6xl">
                    <div class="form-container">
                        <h2 class="text-2xl font-bold mb-6 text-center">Заявка на консультацию</h2>
                        <form @submit.prevent="feedbackSubmit">
                            <div class="mb-3">
                                <label for="name" class="block text-amber-100 text-sm font-bold mb-2">Имя:</label>
                                <input
                                    v-model="feedbackForm.name"
                                    type="text"
                                    id="name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Введите ваше имя"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="block text-amber-100 text-sm font-bold mb-2">Email:</label>
                                <input
                                    v-model="feedbackForm.email"
                                    type="email"
                                    id="email"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Введите ваш email"
                                />
                            </div>

                            <div class="mb-3">
                                <label for="message" class="block text-amber-100 text-sm font-bold mb-2">Сообщение:</label>
                                <textarea
                                    v-model="feedbackForm.message"
                                    id="message"
                                    rows="4"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Введите ваше сообщение"
                                    required
                                ></textarea>
                            </div>

                            <div class="mb-3">
                                <button
                                    type="submit"
                                    class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-2 rounded-lg transition-colors"
                                >
                                    Отправить
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </template>

            <!-- MainPageFeaturesSection -->
            <template v-slot:MainPageFeaturesSection>
                <section class="py-20 bg-amber-50">
                    <div class="container mx-auto px-4">
                        <h2 class="text-3xl font-bold text-center text-amber-800 mb-12">Почему выбирают нас</h2>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-amber-800 mb-2">Качество</h3>
                                <p class="text-gray-600">Используем только отборный дуб и традиционные технологии</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-amber-800 mb-2">Опыт</h3>
                                <p class="text-gray-600">Более 20 лет создаем бочки для лучших виноделен</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-amber-800 mb-2">Индивидуальный подход</h3>
                                <p class="text-gray-600">Изготавливаем бочки по вашим требованиям и размерам</p>
                            </div>
                        </div>
                    </div>
                </section>
            </template>
        </AppLayout>
    </div>
</template>

<style>
/* Начальное состояние (при входе) */
.slide-fade-top-enter-from {
  transform: translateY(-100%); /* Смещение на всю высоту окна */
  opacity: 0;
}

/* Состояние во время анимации входа */
.slide-fade-top-enter-active {
  transition: all 3s ease; /* Плавная анимация в течение 0.5 сек */
}

/* Начальное состояние (при выходе) */
.slide-fade-top-leave-to {
  transform: translateY(-100%); /* Смещение на всю высоту окна */
  opacity: 0;
}

/* Состояние во время анимации выхода */
.slide-fade-top-leave-active {
  transition: all 3s ease; /* Плавная анимация в течение 0.5 сек */
}

/* Стиль для контейнера формы (необязательно) */
.form-container {
    position: absolute; /* Или absolute, в зависимости от вашего layout */
    top: 0;
    background-color: rgb(168, 114, 13);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 10; /* Чтобы форма была поверх другого контента */
    padding: 20px;
}
</style>
