<script setup>
import { onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSession } from '../lib/auth-client'
import { openAuthModal } from '../lib/authModal'

const route = useRoute()
const router = useRouter()
const session = useSession()

onMounted(() => {
  openAuthModal(route.query.mode === 'signup' ? 'signup' : 'login')
})

watch(
  () => session.value.data,
  (user) => {
    if (user) {
      router.replace(route.query.redirect?.toString() || '/')
    }
  }
)
</script>

<template>
  <div class="text-slate-300">Redirecting&hellip;</div>
</template>
