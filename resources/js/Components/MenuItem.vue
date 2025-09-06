<template>
  <li :class="[
    'menu-item', 
    { 
      'has-children': hasChildren, 
      'mobile-open': isOpen && isMobile,
      'desktop-item': !isMobile
    }
  ]">
    <!-- Ссылка или кнопка для пунктов с подменю -->
    <component
      :is="hasChildren && isMobile ? 'button' : 'a'"
      :href="hasChildren && isMobile ? null : getItemUrl(item)"
      class="nav-link"
      :class="{
        'active': isActive,
        'has-arrow': hasChildren,
        'mobile-toggle': hasChildren && isMobile
      }"
      @click="handleItemClick"
    >
      {{ item.title }}
      <span 
        v-if="hasChildren && isMobile" 
        class="dropdown-arrow"
        :class="{ 'rotated': isOpen }"
      >
        ▼
      </span>
    </component>

    <!-- Подменю -->
    <transition
      :name="isMobile ? 'slide-down' : 'fade'"
      @enter="onDropdownEnter"
      @after-enter="onDropdownAfterEnter"
      @leave="onDropdownLeave"
    >
      <ul 
        v-if="hasChildren && (isOpen || !isMobile)" 
        class="dropdown"
        :class="{
          'arrow-top': level === 1 && !isMobile,
          'mobile-dropdown': isMobile,
          'desktop-dropdown': !isMobile
        }"
      >
        <MenuItem 
          v-for="child in item.children" 
          :key="child.id" 
          :item="child" 
          :level="level + 1"
          :is-mobile="isMobile"
          :parent-open="isOpen"
          @close-mobile-menu="$emit('close-mobile-menu')"
        />
      </ul>
    </transition>
  </li>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 1
  },
  isMobile: {
    type: Boolean,
    default: false
  },
  parentOpen: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close-mobile-menu'])

const isOpen = ref(false)
const currentPath = computed(() => window.location.pathname)

const hasChildren = computed(() => 
  props.item.children && props.item.children.length > 0
)

const isActive = computed(() => {
  return currentPath.value === `/${props.item.url}` || 
         currentPath.value.startsWith(`/${props.item.url}/`)
})

const getItemUrl = (item) => {
    return `/${item.url}`
}

const handleItemClick = (event) => {
  if (props.isMobile && hasChildren.value) {
    event.preventDefault()
    isOpen.value = !isOpen.value
  } else {
    emit('close-mobile-menu')
  }
}

// Автоматическое закрытие подменю при изменении родительского состояния
watch(() => props.parentOpen, (newVal) => {
  if (!newVal && props.isMobile) {
    isOpen.value = false
  }
})

// Анимации для dropdown
const onDropdownEnter = (el) => {
  if (props.isMobile) {
    el.style.height = '0'
    el.style.overflow = 'hidden'
    requestAnimationFrame(() => {
      el.style.height = el.scrollHeight + 'px'
    })
  }
}

const onDropdownAfterEnter = (el) => {
  if (props.isMobile) {
    el.style.height = 'auto'
    el.style.overflow = 'visible'
  }
}

const onDropdownLeave = (el) => {
  if (props.isMobile) {
    el.style.height = el.scrollHeight + 'px'
    el.style.overflow = 'hidden'
    requestAnimationFrame(() => {
      el.style.height = '0'
    })
  }
}
</script>

<style scoped>
/* Базовые стили */
.menu-item {
  position: relative;
  list-style: none;
}

.nav-link {
  text-decoration: none !important;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  color: #000 !important;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: #ff8b00 !important;
}

.nav-link.active {
  color: #ff8b00 !important;
}

/* Стили для десктопной версии */
.desktop-item .has-children > .nav-link {
    padding-right: 2rem;
}

.desktop-item .has-children > .nav-link::after {
  content: "▾";
  position: absolute;
  right: 1rem;
  transition: transform 0.3s ease;
}

.desktop-dropdown {
  visibility: hidden;
  opacity: 0;
  position: absolute;
  min-width: 200px;
  background: white;
  border: 1px solid #eee;
  border-radius: 0.25rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  z-index: 1000;
}

.desktop-item:hover > .desktop-dropdown {
    visibility: visible;
    opacity: 1;
}

.menu-item .desktop-item{
    display: block;
    min-width: 200px;
    left:50px;
    background: white;
}

.arrow-top {
  margin-top: 0.5rem;
}

/* Стили для мобильной версии */
.mobile-toggle {
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  cursor: pointer;
}

.mobile-dropdown {
  height: 0;
  overflow: hidden;
  transition: height 0.3s ease;
  padding-left: 1rem;
}

.mobile-open > .mobile-dropdown {
  height: auto;
}

.dropdown-arrow {
  transition: transform 0.3s ease;
  font-size: 0.8rem;
}

.rotated {
  transform: rotate(180deg);
}

/* Анимации */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: height 0.3s ease;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Медиа-запросы для адаптивности */
@media (max-width: 991.98px) {
  .menu-item {
    width: 100%;
  }
  
  .nav-link {
    padding: 1rem;
    border-bottom: 1px solid #eee;
  }
  
  .desktop-dropdown {
    position: static;
    visibility: visible;
    opacity: 1;
    box-shadow: none;
    border: none;
    margin-left: 1rem;
  }
}

@media (min-width: 992px) {
  .menu-item {
    display: inline-block;
  }
  
  .mobile-dropdown {
    height: auto !important;
    overflow: visible !important;
  }
}
</style>
