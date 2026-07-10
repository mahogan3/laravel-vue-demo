<script setup>
import { ref, onMounted } from 'vue'
import { listOrders } from '../../api/orders'
import StatusBadge from '../../components/StatusBadge.vue'
import { formatCurrency } from '../../utils/currency'
import InfoIcon from '../../components/icons/InfoIcon.vue'

const orders = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    orders.value = await listOrders()
  } catch (e) {
    error.value = 'Could not load orders.'
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="title-gradient text-2xl font-semibold">Orders</h1>
      <RouterLink
        to="/orders/create"
        class="brand-gradient-bg text-white px-3 py-1.5 rounded text-sm font-medium transition-opacity hover:opacity-85"
      >
        + New Order
      </RouterLink>
    </div>

    <p v-if="loading" class="text-slate-300">Loading...</p>
    <p v-else-if="error" class="text-brand-rose">{{ error }}</p>

    <table v-else class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-700 text-brand-orange uppercase text-xs tracking-wide">
          <th class="py-2">Order</th>
          <th class="py-2">Customer</th>
          <th class="py-2">Status</th>
          <th class="py-2 text-right">Total</th>
          <th class="py-2"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id" class="border-b border-slate-700/50">
          <td class="py-2 text-slate-100">#{{ order.id }}</td>
          <td class="py-2 text-slate-100">{{ order.customer.name }}</td>
          <td class="py-2"><StatusBadge :status="order.status" /></td>
          <td class="py-2 text-slate-100 text-right">{{ formatCurrency(order.total) }}</td>
          <td class="py-2 text-right">
            <RouterLink
              :to="`/orders/${order.id}`"
              class="inline-flex align-middle text-brand-purple transition-colors hover:text-brand-orange"
              aria-label="Details"
              title="Details"
            >
              <InfoIcon class="w-4 h-4" />
            </RouterLink>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
