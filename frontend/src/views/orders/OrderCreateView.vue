<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { listCustomers } from '../../api/customers'
import { listProducts } from '../../api/products'
import { createOrder } from '../../api/orders'
import { formatCurrency } from '../../utils/currency'
import { useSession } from '../../lib/auth-client'

const router = useRouter()
const session = useSession()
const isAdmin = computed(() => session.value.data?.user?.role === 'admin')

const customers = ref([])
const products = ref([])
const customerId = ref('')
const lines = ref([{ productId: '', quantity: 1 }])
const errors = ref({})
const saving = ref(false)

onMounted(async () => {
  products.value = await listProducts()
  if (isAdmin.value) {
    customers.value = await listCustomers()
  }
})

function addLine() {
  lines.value.push({ productId: '', quantity: 1 })
}

function removeLine(index) {
  lines.value.splice(index, 1)
}

function productPrice(productId) {
  return products.value.find((p) => p.id === Number(productId))?.price ?? 0
}

const total = computed(() =>
  lines.value.reduce((sum, line) => sum + productPrice(line.productId) * (line.quantity || 0), 0)
)

const canSubmit = computed(
  () => (!isAdmin.value || customerId.value !== '') && lines.value.some((l) => l.productId)
)

async function save() {
  saving.value = true
  errors.value = {}
  try {
    const order = await createOrder({
      ...(isAdmin.value ? { customer_id: customerId.value } : {}),
      items: lines.value
        .filter((l) => l.productId)
        .map((l) => ({ product_id: l.productId, quantity: l.quantity })),
    })
    router.push(`/orders/${order.id}`)
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
  <div class="max-w-2xl space-y-4">
    <h1 class="title-gradient text-2xl font-semibold">New Order</h1>

    <form class="space-y-6" @submit.prevent="save">
      <div v-if="isAdmin">
        <label class="title-gradient block text-sm font-semibold">Customer <span class="text-brand-rose">*</span></label>
        <select v-model="customerId" class="mt-1 w-full border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900">
          <option value="" disabled>Select a customer</option>
          <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <p v-if="errors.customer_id" class="text-brand-rose text-sm mt-1">{{ errors.customer_id[0] }}</p>
      </div>

      <div class="space-y-3">
        <label class="title-gradient block text-sm font-semibold">Items <span class="text-brand-rose">*</span></label>
        <p v-if="errors.items" class="text-brand-rose text-sm">{{ errors.items[0] }}</p>

        <div v-for="(line, index) in lines" :key="index" class="flex items-center gap-3">
          <select v-model="line.productId" class="flex-1 border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900">
            <option value="" disabled>Select a product</option>
            <option v-for="p in products" :key="p.id" :value="p.id">
              {{ p.name }} ({{ formatCurrency(p.price) }})
            </option>
          </select>
          <input
            v-model.number="line.quantity"
            type="number"
            min="1"
            class="w-20 border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900"
          />
          <button
            type="button"
            class="text-brand-rose transition-colors hover:text-brand-rose-dark text-sm"
            :disabled="lines.length === 1"
            @click="removeLine(index)"
          >
            Remove
          </button>
        </div>

        <button type="button" class="text-brand-purple transition-colors hover:text-brand-orange text-sm" @click="addLine">
          + Add item
        </button>
      </div>

      <p class="text-lg font-medium text-slate-100">Total: {{ formatCurrency(total) }}</p>

      <div class="flex gap-3">
        <button
          type="submit"
          :disabled="saving || !canSubmit"
          class="brand-gradient-bg text-white px-4 py-1.5 rounded transition-opacity hover:opacity-85 disabled:opacity-50"
        >
          Place Order
        </button>
        <RouterLink to="/orders" class="px-4 py-1.5 rounded border border-slate-500 text-slate-200 hover:bg-white/10">
          Cancel
        </RouterLink>
      </div>
    </form>
  </div>
</template>
