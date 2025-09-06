<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <!-- Кнопка для мобильных устройств -->
    <button 
      class="navbar-toggler" 
      type="button" 
      @click="toggleMobileMenu"
      aria-controls="navbarNav"
      :aria-expanded="isMobileMenuOpen"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Основное меню -->
    <div 
      class="collapse navbar-collapse" 
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
</template>

<script setup>
import { defineProps, ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import MenuItem from './MenuItem.vue'

const props = defineProps({
    menuItems: {
        type: [Object, Array],
        default: () => usePage().props.menuItems || []
    },
})

const isMobileMenuOpen = ref(false)
const isMobile = ref(false)

// Функции для управления мобильным меню
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  if (isMobile.value) {
    isMobileMenuOpen.value = false
  }
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
</script>

<style scoped>
.navbar {
  padding: 0;
}

.navbar-toggler {
  margin-left: auto;
  border: none;
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
