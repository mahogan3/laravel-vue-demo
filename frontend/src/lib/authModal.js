import { ref } from 'vue'

export const isAuthModalOpen = ref(false)
export const authModalMode = ref('login')

export function openAuthModal(mode = 'login') {
  authModalMode.value = mode
  isAuthModalOpen.value = true
}

export function closeAuthModal() {
  isAuthModalOpen.value = false
}
