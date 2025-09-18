<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, Head, usePage } from '@inertiajs/vue3'
import MenuItem from '@/Components/MenuItem.vue';
import ModalPhoto from '@/Components/ModalPhoto.vue';

const props = defineProps({
    userdata: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    blocks: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
    feedback: {
        type: [Object, Array],
        required: true,
        default: () => ([])
    },
    menuItems: {
        type: [Object, Array],
        default: () => usePage().props.menuItems || []
    },
    meta: {
        type: [Object, Array],
        required: true,
        default: () => ({})
    },
})

const metaTitle = computed(() => props.meta?.title || 'Производство дубовых бочек и кадок')
const metaKeywords = computed(() => props.meta?.keywords || '')
const metaDescription = computed(() => props.meta?.description || '')

const isMobileMenuOpen = ref(false)
const isMobile = ref(false)
const currentImage = ref({});
const showModal = ref(false);
const userdata = computed(() => props.userdata)

const emailLink = computed(() => {
    return 'mailto:' + props.userdata.email;
});

const phoneLink = computed(() => {
    return 'tel:' + props.userdata.phoneShort;
});

const maxWrite = () => {
    window.location.href = props.userdata.max;
}

const phoneCall = () => {
        // Для мобильных устройств, чтобы открыть приложение для звонков
    window.location.href = `tel:${props.userdata.phoneShort}`;
};

const openModal = (photo) => {
    currentImage.value = photo;
    showModal.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    document.body.style.overflow = '';
};

function transformArray(obj){
    const keys = Object.keys(obj); // Получаем массив ключей: ['a', 'b', 'c']
    const result = keys.map(key => obj[key]); // Используем map для создания нового массива значений
    return result;    
}

// Функции для управления мобильным меню
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
console.log('isMobileMenuOpen 1 = ' + isMobileMenuOpen.value);
}

console.log('isMobileMenuOpen 2 = ' + isMobileMenuOpen.value);

const closeMobileMenu = () => {
    if (isMobile.value) {
        isMobileMenuOpen.value = false
    }
console.log('isMobileMenuOpen 3 = ' + isMobileMenuOpen.value);
}


// Проверка размера экрана
const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 992 // Bootstrap lg breakpoint
    if (!isMobile.value) {
        isMobileMenuOpen.value = false
    }
}

// Обработчики событий
onMounted(() => {
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
    window.addEventListener('click', handleOutsideClick)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
    window.removeEventListener('click', handleOutsideClick)
})

// Закрытие меню при клике вне его области
const handleOutsideClick = (event) => {
    const navbar = event.target.closest('.navbar')
    if (!navbar && isMobileMenuOpen.value) {
        closeMobileMenu()
    }
}

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

const getParagraphs = (content) => {
    const keys = Object.keys(content); // Получаем массив ключей: ['a', 'b', 'c']
    const result = keys.map(key => content[key]); // Используем map для создания нового массива значений
    return result;    
}
</script>

<template>
    <div>
        <Head>
          <title>{{ metaTitle }}</title>
          <meta name="keywords" :content="metaKeywords">
          <meta name="description" :content="metaDescription">
        </Head>
    </div>

    <!-- Header -->
    <header class="top-bar">
        <div class="container">
            <div class="d-md-none d-sm-inline-block font-bold text-amber-200">{{ userdata.orgShortName }}</div>
            <div class="d-none d-md-inline-block">
                <a :href="emailLink" class="text-white">
                    <span class="mr-2 text-white icon-envelope-open-o"></span>
                    <span v-html="userdata.email"></span>
                </a>
                <a :href="phoneLink" class="text-white">
                    <span class="mr-2 text-white icon-phone"></span>
                    <span v-html="userdata.phoneFull"></span>
                </a>
            </div>
            <div class="float-right">
                <div class="d-none d-md-inline-block">
                    <a :href="userdata.telegramBot" target="_blank" class="text-white">
                        <span class="mr-2 text-white icon-telegram"></span>
                        <span>Telegram</span>
                    </a>
                    <a :href="userdata.max" target="_blank" class="text-white">
                        <span class="mr-2 text-white icon-whatsapp"></span>
                        <span>Max</span>
                    </a>
                    <Link :href="route('login')">
                        <span class="ml-2 text-amber-500">Войти</span>
                    </Link>
                    </div>
                <nav class="navbar fixed-top">
                    <!-- Кнопка для мобильных устройств -->
                    <button 
                        v-if="isMobile"
                        class="navbar-toggler"
                        type="button" 
                        @click="toggleMobileMenu"
                        aria-controls="navbarNav"
                        :aria-expanded="isMobileMenuOpen"
                    >
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Главное меню -->
    <div class="d-none d-lg-block">
        <div class="container flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-amber-800">{{ userdata.orgShortName }}</a>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <!-- Основное меню -->
                <div
                    class="navbar-collapse"
                    :class="{ 'show': isMobileMenuOpen }"
                    id="navbarNav"
                >
                    <ul class="site-menu js-clone-nav ml-auto">
                        <MenuItem 
                            v-for="item in menuItems" 
                            :key="item.id"
                            :item="item" 
                            :level="1"
                            :is-mobile="isMobile"
                            @close-mobile-menu="closeMobileMenu"
                        />
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <slot name="MainPageTopSection" />

    <section class="py-2">
        <!-- Просмотр изображения в увеличенном масштабе -->
        <div class="container mx-auto py-8">
            <ModalPhoto
                v-if="showModal"
                :photo="currentImage"
                @close="closeModal"
            />
        </div>
        <div class="container mx-auto px-2">
            <!--<h2 class="text-3xl font-bold text-center text-amber-800 mb-12">Наша продукция</h2>-->
            <!-- Визуализация блока -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div v-for="block in blocks" :key="block.id" class="bg-white rounded-lg shadow-md p-2">
                    <!-- Блок с набором изображений -->
                    <div v-if="isImageSet(block)" class="image-set-container">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div 
                                v-for="component in block.components" 
                                :key="component.id"
                                @click="showLargeImage(component.photo.filename)"
                                :class="component.comp_input"
                                class="component-item relative group"
                            >
                                <img
                                    :src="component.photo.thumbnail"
                                    :alt="component.photo.description"
                                    :title="component.photo.title"
                                    @click="openModal(component.photo)"
                                    class="cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                >
                            </div>
                        </div>
                    </div>
                    <div v-else-if="hasImages(block)" class="image-block-container">
                        <div class="flex flex-col md:flex-row gap-6" :class="{
                            'md:flex-row-reverse': getImagePosition(block) === 'photo-right'
                        }">
                            <div class="md:w-1/3">
                                <div 
                                    v-for="component in transformArray(block.components).filter(c => isImageTag(c.tag_id))"
                                    :key="component.id"
                                    :class="component.comp_input"
                                    class="component-item relative group img-fluid"
                                >
                                    <img
                                        :src="component.photo.thumbnail"
                                        :alt="component.photo.description"
                                        :title="component.photo.title"
                                        @click="openModal(component.photo)"
                                        class="cursor-pointer overflow-hidden rounded-lg hover:shadow-md transition"
                                    >
                                </div>
                            </div>
                            <div class="md:w-2/3 space-y-4">
                                <div 
                                    v-for="component in transformArray(block.components).filter(c => !isImageTag(c.tag_id))"
                                    :key="component.id"
                                    :class="component.class"
                                    class="component-item relative group"
                                >
                                    <ul v-if="component.class === 'list-bulleted'" class="mt-1">
                                        <li v-for="text in getParagraphs(component.content)">
                                            {{ text }}
                                        </li>
                                    </ul>
                                    <ol v-else-if="component.class === 'list-numbered'">
                                        <li v-for="text in getParagraphs(component.content)">
                                            {{ text }}
                                        </li>
                                    </ol>
                                    <p v-else v-for="text in getParagraphs(component.content)" :class="component.position">
                                        {{ text }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-block-container">
                        <div class="space-y-4">
                            <div 
                                v-for="component in block.components"
                                :key="component.id"
                                :class="component.class"
                                class="component-item relative group"
                            >
                                <ul v-if="component.class === 'list-bulleted'" class="ul list-unstyled mt-1">
                                    <li v-for="text in getParagraphs(component.content)">
                                        {{ text }}
                                    </li>
                                </ul>
                                <ol v-else-if="component.class === 'list-numbered'">
                                    <li v-for="text in getParagraphs(component.content)">
                                        {{ text }}
                                    </li>
                                </ol>
                                <p v-else v-for="text in getParagraphs(component.content)" :class="component.position">
                                    {{ text }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <slot name="MainPageFeaturesSection"></slot>

    <!-- Contact Section -->
    <section v-if="props.feedback.present == 1" class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-amber-800 mb-6">{{ props.feedback.title }}</h2>
                <p class="text-xl text-gray-700 mb-8">
                    Свяжитесь с нами для консультации и расчета стоимости
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button
                        @click="phoneCall"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-lg transition-colors"
                    >
                        Позвонить нам
                    </button>
                    <button
                        @click="maxWrite"
                        target="_blank"
                        class="border-2 border-amber-600 text-amber-600 hover:bg-amber-600 hover:text-white px-8 py-3 rounded-lg transition-colors"
                    >
                        Написать в Max
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-amber-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ userdata.orgFullName }}</h3>
                    <p class="text-amber-200">Качественные дубовые бочки для вина и коньяка</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Контакты</h3>
                    <p class="text-amber-200"><a :href="phoneLink">{{ userdata.phoneFull }}</a></p>
                    <p class="text-amber-200"><a :href="emailLink">{{ userdata.email }}</a></p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Мы в соцсетях</h3>
                    <div class="flex space-x-4">
                        <a
                            :href="userdata.vk"
                            target="_blank"
                            class="text-amber-200 hover:text-white transition-colors"
                        >
                            Вконтакте
                        </a>
                        <a
                            :href="userdata.ok"
                            target="_blank"
                            class="text-amber-200 hover:text-white transition-colors"
                        >
                            Одноклассники
                        </a>
                        <a
                            :href="userdata.rutube"
                            target="_blank"
                            class="text-amber-200 hover:text-white transition-colors"
                        >
                            RuTube
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.navbar {
    padding: 0;
}

.navbar-toggler {
    margin-left: auto;
    border: none;
}

/* Для больших изображений может потребоваться управлять их размером */
img {
    max-width: 100%;
    height: auto;
    border: 2px solid #BDB76B;
    padding: 3px;
}

.text-left {
    text-align: left;
}
.text-center {
    text-align: center;
}
.text-right {
    text-align: right;
}
.text-justify {
    text-align: justify;
}

.title-main {
	font-size: 130%;
	font-weight: 500;
	font-family: "Oswald", sans-serif;
	text-transform: uppercase;
	color: #000 !important;
	line-height: 1.2;
}

.title-sub {
	font-size: 120%;
	font-weight: 500;
	font-family: "Oswald", sans-serif;
	text-transform: uppercase;
	margin-bottom: 0.5rem;
	line-height: 1.2;
}

.text-large{
	font-size: 110%;
	font-weight: 300;
	text-align: justify;
}

.text-small{
	margin-top: 0;
	margin-bottom: 1rem;
	text-align: justify;
	color: #5f5050;
}

.list-numbered{
	list-style-type: none;
	font-size: 16px;
	counter-reset: num;
	position: relative;
	margin: 0 0 0 60px;
	padding: 15px 0 5px 0;
	color: #5f5050;
}

/* Вертикальная линия */
.list-numbered:before {
	content: '';
	position: absolute;
	top: 15px;
	bottom: 15px;
	left: -30px;
	width: 1px;
	border-left: 1px solid #ef6780;
}

.list-numbered li{
	position: relative;
	margin: 0 0 0 0;
	padding: 0 0 10px 0;
	line-height: 1.4;
}

.list-numbered li:after {
	content: counter(num);
	counter-increment: num;
	display: inline-block;
	position: absolute;
	top: 0;
	left: -45px;
	width: 28px;
	height: 28px;
	line-height: 28px;
	background: #fff;
	color: #000;
	text-align: center;
	font-size: 18px;
	border-radius: 50%;
	border: 1px solid #ef6780;
}

/* Скрытие линии у последнего li */
.list-numbered li:last-child:before {
	content: '';
	display: inline-block;
	position: absolute;
	top: 0;
	bottom: 0;
	left: -38px;
	width: 28px;
	background: #fff;
}

.list-bulleted {
	padding-left: 0;
	list-style: none;
	margin-bottom: 50px;
}

.list-bulleted li {
	position: relative;
	color: #5f5050;
	padding-left: 35px;
	margin-bottom: 15px;
	line-height: 1.5;
}
.list-bulleted li:before {
	left: 0;
	font-size: 20px;
	font-family: "icomoon";
	content: "✓ ";
	position: absolute;
}

/* Адаптивные стили для мобильных устройств */
@media (max-width: 991.98px) {
    .navbar-collapse {
        background: white;
        padding: 1rem;
        border-radius: 0.25rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-top: 0.5rem;
    }
  
    .site-menu {
        flex-direction: column;
    }
}
</style>
