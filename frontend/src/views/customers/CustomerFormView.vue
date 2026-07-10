<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getCustomer, createCustomer, updateCustomer } from '../../api/customers'
import { formatUsPhone } from '../../utils/phone'
import { isValidEmail } from '../../utils/email'

const props = defineProps({
  id: { type: String, default: null },
})

const router = useRouter()
const isEdit = computed(() => props.id != null)

const form = ref({ name: '', email: '', phone: '', address: '' })
const errors = ref({})
const saving = ref(false)
const emailTouched = ref(false)

const emailError = computed(() => {
  if (!emailTouched.value || form.value.email.trim().length === 0) return ''
  return isValidEmail(form.value.email) ? '' : 'Please enter a valid email address.'
})

const canSubmit = computed(
  () =>
    form.value.name.trim().length > 0 &&
    isValidEmail(form.value.email) &&
    form.value.phone.trim().length > 0 &&
    form.value.address.trim().length > 0
)

onMounted(async () => {
  if (isEdit.value) {
    const customer = await getCustomer(props.id)
    form.value = {
      name: customer.name,
      email: customer.email,
      phone: formatUsPhone(customer.phone ?? ''),
      address: customer.address ?? '',
    }
  }
})

function onPhoneInput(event) {
  form.value.phone = formatUsPhone(event.target.value)
}

async function save() {
  saving.value = true
  errors.value = {}
  try {
    if (isEdit.value) {
      await updateCustomer(props.id, form.value)
    } else {
      await createCustomer(form.value)
    }
    router.push('/customers')
  } catch (e) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors
    } else {
      throw e
    }
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="max-w-lg space-y-4">
    <h1 class="title-gradient text-2xl font-semibold">
      {{ isEdit ? 'Edit Customer' : 'New Customer' }}
    </h1>

    <form class="space-y-4" @submit.prevent="save">
      <div>
        <label class="title-gradient block text-sm font-semibold">Name <span class="text-brand-rose">*</span></label>
        <input v-model="form.name" type="text" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900" />
        <p v-if="errors.name" class="text-brand-rose text-sm mt-1">{{ errors.name[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">Email <span class="text-brand-rose">*</span></label>
        <input
          v-model="form.email"
          type="email"
          class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"
          @blur="emailTouched = true"
        />
        <p v-if="emailError" class="text-brand-rose text-sm mt-1">{{ emailError }}</p>
        <p v-else-if="errors.email" class="text-brand-rose text-sm mt-1">{{ errors.email[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">Phone <span class="text-brand-rose">*</span></label>
        <input
          :value="form.phone"
          type="tel"
          inputmode="numeric"
          placeholder="+1 (555) 123-4567"
          maxlength="17"
          class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"
          @input="onPhoneInput"
        />
        <p v-if="errors.phone" class="text-brand-rose text-sm mt-1">{{ errors.phone[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">Address <span class="text-brand-rose">*</span></label>
        <input v-model="form.address" type="text" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900" />
        <p v-if="errors.address" class="text-brand-rose text-sm mt-1">{{ errors.address[0] }}</p>
      </div>

      <div class="flex gap-3">
        <button
          type="submit"
          :disabled="saving || !canSubmit"
          class="brand-gradient-bg text-white px-4 py-1.5 rounded transition-opacity hover:opacity-85 disabled:opacity-50"
        >
          Save
        </button>
        <RouterLink to="/customers" class="px-4 py-1.5 rounded border border-slate-500 text-slate-200 hover:bg-white/10">
          Cancel
        </RouterLink>
      </div>
    </form>
  </div>
</template>
