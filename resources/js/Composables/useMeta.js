// composables/useMeta.js
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useMeta() {
    const { props } = usePage()
    
    const title = ref(props.meta?.title || 'Default Title')
    const keywords = ref(props.meta?.keywords || '')
    const description = ref(props.meta?.description || '')
    
    // Обновляем мета-теги при изменении props
    watch(() => props.meta, (newMeta) => {
        if (newMeta) {
            title.value = newMeta.title || title.value
            keywords.value = newMeta.keywords || keywords.value
            description.value = newMeta.description || description.value
            updateDocumentMeta()
        }
    }, { immediate: true })
    
    function updateDocumentMeta() {
        document.title = title.value
        
        // Обновляем или создаем meta-теги
        updateMetaTag('keywords', keywords.value)
        updateMetaTag('description', description.value)
    }
    
    function updateMetaTag(name, content) {
        if (!content) return
        
        let tag = document.querySelector(`meta[name="${name}"]`)
        if (!tag) {
            tag = document.createElement('meta')
            tag.setAttribute('name', name)
            document.head.appendChild(tag)
        }
        tag.setAttribute('content', content)
    }
    
    return {
        title,
        keywords,
        description,
    }
}
