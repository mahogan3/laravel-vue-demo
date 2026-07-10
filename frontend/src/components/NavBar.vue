<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { authClient, useSession } from '../lib/auth-client'
import { openAuthModal } from '../lib/authModal'

const route = useRoute()
const router = useRouter()
const session = useSession()

const user = computed(() => session.value.data?.user ?? null)
const isAdmin = computed(() => user.value?.role === 'admin')

const navLinks = computed(() => [
  { to: '/products', label: 'Products' },
  ...(isAdmin.value ? [{ to: '/customers', label: 'Customers' }] : []),
  ...(user.value ? [{ to: '/orders', label: 'Orders' }] : []),
])

function isActive(to) {
  return route.path === to || route.path.startsWith(`${to}/`)
}

async function logout() {
  await authClient.signOut()
  router.push('/')
}
</script>

<template>
  <nav class="bg-slate-800 text-white px-6 py-3 flex items-center gap-6">
    <RouterLink to="/" class="flex items-center gap-2 font-semibold text-lg">
      <svg viewBox="0 0 32 32" class="w-7 h-7 shrink-0" aria-hidden="true">
        <defs>
          <linearGradient id="logo-sky" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#312e81" />
            <stop offset="100%" stop-color="#581c53" />
          </linearGradient>
          <linearGradient id="logo-sunset" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" style="stop-color: var(--color-brand-yellow)" />
            <stop offset="45%" style="stop-color: var(--color-brand-orange)" />
            <stop offset="75%" style="stop-color: var(--color-brand-rose)" />
            <stop offset="100%" style="stop-color: var(--color-brand-purple)" />
          </linearGradient>
        </defs>
        <rect x="1" y="1" width="30" height="30" rx="8" fill="url(#logo-sky)" />
        <circle cx="20" cy="13" r="4" fill="url(#logo-sunset)" opacity="0.9" />
        <path d="M8 21 L14 11 L18 17 L21 12 L24 21 Z" fill="url(#logo-sunset)" />
      </svg>
      <span class="title-gradient">Zion Industries</span>
    </RouterLink>
    <RouterLink
      v-for="link in navLinks"
      :key="link.to"
      :to="link.to"
      class="transition-colors"
      :class="
        isActive(link.to)
          ? 'font-semibold text-brand-yellow'
          : 'text-brand-purple hover:text-brand-orange'
      "
    >
      {{ link.label }}
    </RouterLink>

    <div class="ml-auto flex items-center gap-3 text-sm">
      <template v-if="user">
        <span class="text-slate-300">
          {{ user.email }}
          <span v-if="isAdmin" class="text-brand-yellow font-medium">(admin)</span>
        </span>
        <button
          class="brand-gradient-bg text-white px-3 py-1.5 rounded text-sm font-medium transition-opacity hover:opacity-85"
          @click="logout"
        >
          Sign Out
        </button>
      </template>
      <button
        v-else
        class="brand-gradient-bg text-white px-3 py-1.5 rounded text-sm font-medium transition-opacity hover:opacity-85"
        @click="openAuthModal('login')"
      >
        Sign In
      </button>
    </div>
  </nav>
</template>
