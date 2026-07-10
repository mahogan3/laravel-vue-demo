<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getProduct, createProduct, updateProduct } from '../../api/products'

const props = defineProps({
  id: { type: String, default: null },
})

const router = useRouter()
const isEdit = computed(() => props.id != null)

const form = ref({ name: '', sku: '', description: '', price: '' })
const errors = ref({})
const saving = ref(false)

const canSubmit = computed(
  () =>
    form.value.name.trim().length > 0 &&
    form.value.sku.trim().length > 0 &&
    form.value.price !== '' &&
    form.value.price !== null
)

onMounted(async () => {
  if (isEdit.value) {
    const product = await getProduct(props.id)
    form.value = {
      name: product.name,
      sku: product.sku ?? '',
      description: product.description ?? '',
      price: product.price,
    }
  }
})

async function save() {
  saving.value = true
  errors.value = {}
  try {
    if (isEdit.value) {
      await updateProduct(props.id, form.value)
    } else {
      await createProduct(form.value)
    }
    router.push('/products')
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
      {{ isEdit ? 'Edit Product' : 'New Product' }}
    </h1>

    <form class="space-y-4" @submit.prevent="save">
      <div>
        <label class="title-gradient block text-sm font-semibold">Name <span class="text-brand-rose">*</span></label>
        <input v-model="form.name" type="text" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900" />
        <p v-if="errors.name" class="text-brand-rose text-sm mt-1">{{ errors.name[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">SKU <span class="text-brand-rose">*</span></label>
        <input v-model="form.sku" type="text" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900" />
        <p v-if="errors.sku" class="text-brand-rose text-sm mt-1">{{ errors.sku[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">Description</label>
        <textarea v-model="form.description" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"></textarea>
        <p v-if="errors.description" class="text-brand-rose text-sm mt-1">{{ errors.description[0] }}</p>
      </div>

      <div>
        <label class="title-gradient block text-sm font-semibold">Price <span class="text-brand-rose">*</span></label>
        <input v-model="form.price" type="number" step="0.01" min="0" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900" />
        <p v-if="errors.price" class="text-brand-rose text-sm mt-1">{{ errors.price[0] }}</p>
      </div>

      <div class="flex gap-3">
        <button
          type="submit"
          :disabled="saving || !canSubmit"
          class="brand-gradient-bg text-white px-4 py-1.5 rounded transition-opacity hover:opacity-85 disabled:opacity-50"
        >
          Save
        </button>
        <RouterLink to="/products" class="px-4 py-1.5 rounded border border-slate-500 text-slate-200 hover:bg-white/10">
          Cancel
        </RouterLink>
      </div>
    </form>
  </div>
</template>
