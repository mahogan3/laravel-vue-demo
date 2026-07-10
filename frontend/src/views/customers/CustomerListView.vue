<script setup>
import { ref, onMounted } from 'vue'
import { listCustomers, deleteCustomer } from '../../api/customers'
import PencilIcon from '../../components/icons/PencilIcon.vue'
import TrashIcon from '../../components/icons/TrashIcon.vue'

const customers = ref([])
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    customers.value = await listCustomers()
  } catch (e) {
    error.value = 'Could not load customers.'
  } finally {
    loading.value = false
  }
}

async function remove(customer) {
  if (!confirm(`Delete "${customer.name}"?`)) return
  try {
    await deleteCustomer(customer.id)
    await load()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Could not delete this customer.')
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="title-gradient text-2xl font-semibold">Customers</h1>
      <RouterLink
        to="/customers/create"
        class="brand-gradient-bg text-white px-3 py-1.5 rounded text-sm font-medium transition-opacity hover:opacity-85"
      >
        + New Customer
      </RouterLink>
    </div>

    <p v-if="loading" class="text-slate-300">Loading...</p>
    <p v-else-if="error" class="text-brand-rose">{{ error }}</p>

    <table v-else class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-700 text-brand-orange uppercase text-xs tracking-wide">
          <th class="py-2">Name</th>
          <th class="py-2">Email</th>
          <th class="py-2">Phone</th>
          <th class="py-2"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="customer in customers" :key="customer.id" class="border-b border-slate-700/50">
          <td class="py-2 text-slate-100">{{ customer.name }}</td>
          <td class="py-2 text-slate-400">{{ customer.email }}</td>
          <td class="py-2 text-slate-400">{{ customer.phone ?? '—' }}</td>
          <td class="py-2 text-right space-x-3">
            <RouterLink
              :to="`/customers/${customer.id}/edit`"
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
              @click="remove(customer)"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
