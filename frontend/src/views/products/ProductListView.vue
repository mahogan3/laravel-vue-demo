<script setup>
import { ref, onMounted, computed } from 'vue'
import { listProducts, deleteProduct } from '../../api/products'
import { formatCurrency } from '../../utils/currency'
import { useSession } from '../../lib/auth-client'
import PencilIcon from '../../components/icons/PencilIcon.vue'
import TrashIcon from '../../components/icons/TrashIcon.vue'

const session = useSession()
const isAdmin = computed(() => session.value.data?.user?.role === 'admin')

const products = ref([])
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    products.value = await listProducts()
  } catch (e) {
    error.value = 'Could not load products.'
  } finally {
    loading.value = false
  }
}

async function remove(product) {
  if (!confirm(`Delete "${product.name}"?`)) return
  try {
    await deleteProduct(product.id)
    await load()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Could not delete this product.')
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="title-gradient text-2xl font-semibold">Products</h1>
      <RouterLink
        v-if="isAdmin"
        to="/products/create"
        class="brand-gradient-bg text-white px-3 py-1.5 rounded text-sm font-medium transition-opacity hover:opacity-85"
      >
        + New Product
      </RouterLink>
    </div>

    <p v-if="loading" class="text-slate-300">Loading...</p>
    <p v-else-if="error" class="text-brand-rose">{{ error }}</p>

    <table v-else class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-700 text-brand-orange uppercase text-xs tracking-wide">
          <th class="py-2">Name</th>
          <th class="py-2">SKU</th>
          <th class="py-2 text-right">Price</th>
          <th v-if="isAdmin" class="py-2"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id" class="border-b border-slate-700/50">
          <td class="py-2 text-slate-100">{{ product.name }}</td>
          <td class="py-2 text-slate-400">{{ product.sku ?? '—' }}</td>
          <td class="py-2 text-slate-100 text-right">{{ formatCurrency(product.price) }}</td>
          <td v-if="isAdmin" class="py-2 text-right space-x-3">
            <RouterLink
              :to="`/products/${product.id}/edit`"
              class="inline-flex align-middle text-brand-purple transition-colors hover:text-brand-orange"
              aria-label="Edit"
              title="Edit"
            >
              <PencilIcon class="w-4 h-4" />
            </RouterLink>
            <button
              class="inline-flex align-middle text-brand-rose transition-colors hover:text-brand-rose-dark"
              aria-label="Delete"
              title="Delete"
              @click="remove(product)"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
