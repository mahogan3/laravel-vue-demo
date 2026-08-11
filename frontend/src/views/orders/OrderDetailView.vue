<script setup>
import { ref, onMounted, computed } from 'vue'
import { getOrder, updateOrderStatus } from '../../api/orders'
import StatusBadge from '../../components/StatusBadge.vue'
import { formatCurrency } from '../../utils/currency'
import { useSession } from '../../lib/auth-client'

const props = defineProps({
  id: { type: String, required: true },
})

const session = useSession()
const isAdmin = computed(() => session.value.data?.user?.role === 'admin')

const order = ref(null)
const updating = ref(false)

async function load() {
  order.value = await getOrder(props.id)
}

async function changeStatus(event) {
  updating.value = true
  order.value = await updateOrderStatus(props.id, event.target.value)
  updating.value = false
}

onMounted(load)
</script>

<template>
  <div v-if="order" class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="title-gradient text-2xl font-semibold">Order #{{ order.id }}</h1>
      <StatusBadge :status="order.status" />
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
      <div>
        <p class="text-slate-400">Customer</p>
        <p class="font-medium text-slate-100">{{ order.customer.name }}</p>
        <p class="text-slate-400">{{ order.customer.email }}</p>
        <p class="text-slate-400">{{ order.customer.phone }}</p>
        <p class="text-slate-400">{{ order.customer.address }}</p>
      </div>
      <div>
        <p class="text-slate-400">Placed</p>
        <p class="font-medium text-slate-100">{{ new Date(order.created_at).toLocaleString() }}</p>
      </div>
    </div>

    <div v-if="isAdmin && order.available_statuses.length">
      <label class="title-gradient block text-sm font-semibold">Update status</label>
      <select
        value=""
        :disabled="updating"
        class="mt-1 border border-purple-400 rounded px-3 py-1.5 bg-purple-200 text-slate-900 capitalize"
        @change="changeStatus"
      >
        <option value="" disabled selected>Select status</option>
        <option v-for="s in order.available_statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
      </select>
    </div>

    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-700 text-brand-orange uppercase text-xs tracking-wide">
          <th class="py-2">Product</th>
          <th class="py-2 text-right">Qty</th>
          <th class="py-2 text-right">Unit Price</th>
          <th class="py-2 text-right">Line Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in order.items" :key="item.id" class="border-b border-slate-700/50">
          <td class="py-2 text-slate-100">{{ item.product.name }}</td>
          <td class="py-2 text-slate-100 text-right">{{ item.quantity }}</td>
          <td class="py-2 text-slate-100 text-right">{{ formatCurrency(item.unit_price) }}</td>
          <td class="py-2 text-slate-100 text-right">{{ formatCurrency(item.line_total) }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="py-2 text-right font-medium text-slate-100">Total</td>
          <td class="py-2 font-medium text-slate-100 text-right">{{ formatCurrency(order.total) }}</td>
        </tr>
      </tfoot>
    </table>

    <RouterLink to="/orders" class="text-brand-purple transition-colors hover:text-brand-orange text-sm">&larr; Back to orders</RouterLink>
  </div>
</template>
