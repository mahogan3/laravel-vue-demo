<script setup>
import { ref, computed, nextTick, watch } from 'vue'
import { authClient } from '../lib/auth-client'
import { isAuthModalOpen, authModalMode, closeAuthModal } from '../lib/authModal'
import { isValidEmail } from '../utils/email'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const submitting = ref(false)
const showPassword = ref(false)
const emailTouched = ref(false)

const emailError = computed(() => {
  if (!emailTouched.value || email.value.trim().length === 0) return ''
  return isValidEmail(email.value) ? '' : 'Please enter a valid email address.'
})

const nameInput = ref(null)
const emailInput = ref(null)

async function focusPrimaryField() {
  await nextTick()
  ;(authModalMode.value === 'signup' ? nameInput.value : emailInput.value)?.focus()
}

watch(isAuthModalOpen, (open) => {
  if (open) {
    name.value = ''
    email.value = ''
    password.value = ''
    error.value = ''
    showPassword.value = false
    emailTouched.value = false
    focusPrimaryField()
  }
})

function switchMode(mode) {
  authModalMode.value = mode
  error.value = ''
  focusPrimaryField()
}

async function submit() {
  emailTouched.value = true
  if (!isValidEmail(email.value)) return

  submitting.value = true
  error.value = ''

  const { error: authError } =
    authModalMode.value === 'login'
      ? await authClient.signIn.email({ email: email.value, password: password.value })
      : await authClient.signUp.email({ name: name.value, email: email.value, password: password.value })

  submitting.value = false

  if (authError) {
    error.value = authError.message ?? 'Something went wrong. Please try again.'
    return
  }

  closeAuthModal()
}
</script>

<template>
  <div
    v-if="isAuthModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    @click.self="closeAuthModal"
  >
    <div class="w-full max-w-sm rounded-lg bg-[#241a3d] border border-purple-400/40 p-6 space-y-4 shadow-xl">
      <div class="flex items-center justify-between">
        <h2 class="title-gradient text-xl font-semibold">
          {{ authModalMode === 'login' ? 'Sign In' : 'Create Account' }}
        </h2>
        <button class="text-slate-400 hover:text-slate-200 text-xl leading-none" @click="closeAuthModal">&times;</button>
      </div>

      <form class="space-y-4" @submit.prevent="submit">
        <div v-if="authModalMode === 'signup'">
          <label class="title-gradient block text-sm font-semibold">Name</label>
          <input
            ref="nameInput"
            v-model="name"
            type="text"
            required
            class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"
          />
        </div>

        <div>
          <label class="title-gradient block text-sm font-semibold">Email</label>
          <input
            ref="emailInput"
            v-model="email"
            type="email"
            required
            class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"
            @blur="emailTouched = true"
          />
          <p v-if="emailError" class="text-brand-rose text-sm mt-1">{{ emailError }}</p>
        </div>

        <div>
          <label class="title-gradient block text-sm font-semibold">Password</label>
          <div class="relative mt-1">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              minlength="8"
              class="w-full border border-purple-400 rounded px-3 py-1.5 pr-16 bg-purple-200 text-slate-900"
            />
            <button
              type="button"
              class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-slate-600 hover:text-slate-900"
              @click="showPassword = !showPassword"
            >
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>
        </div>

        <p v-if="error" class="text-brand-rose text-sm">{{ error }}</p>

        <button
          type="submit"
          :disabled="submitting"
          class="brand-gradient-bg w-full text-white px-4 py-1.5 rounded transition-opacity hover:opacity-85 disabled:opacity-50"
        >
          {{ authModalMode === 'login' ? 'Sign In' : 'Create Account' }}
        </button>
      </form>

      <p class="text-sm text-slate-300">
        <template v-if="authModalMode === 'login'">
          Don't have an account?
          <button class="text-brand-orange hover:text-brand-yellow" @click="switchMode('signup')">Sign up</button>
        </template>
        <template v-else>
          Already have an account?
          <button class="text-brand-orange hover:text-brand-yellow" @click="switchMode('login')">Sign in</button>
        </template>
      </p>
    </div>
  </div>
</template>
