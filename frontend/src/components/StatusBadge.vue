<script setup>
defineProps({
  status: { type: String, required: true },
})

const colors = {
  pending: 'bg-brand-yellow text-slate-900',
  processing: 'bg-brand-orange text-white',
  shipped: 'bg-brand-rose text-white',
  completed: 'bg-brand-purple text-white',
  cancelled: 'bg-slate-600 text-slate-100',
}
</script>

<template>
  <span
    class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
    :class="colors[status] ?? 'bg-slate-100 text-slate-800'"
  >
    {{ status }}
  </span>
</template>
