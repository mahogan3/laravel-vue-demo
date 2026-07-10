<script setup></script>

<template>
  <div class="space-y-8">
    <div class="space-y-1">
      <h1 class="title-gradient text-3xl font-semibold">Zion Industries</h1>
      <p class="text-brand-purple text-sm font-medium uppercase tracking-wide">Laravel + Vue Demo</p>
    </div>

    <p class="text-slate-300 max-w-2xl">
      A simple app that demonstrates Vue UI components, a PHP API layer, and data persistence using
      SQLite.
    </p>

    <div class="grid sm:grid-cols-3 gap-4">
      <div class="rounded-lg border border-purple-400/30 bg-white/5 p-4 space-y-1">
        <h2 class="title-gradient font-semibold">Catalog &amp; customers</h2>
        <p class="text-slate-300 text-sm">Manage a product catalog and customer list with full CRUD.</p>
      </div>
      <div class="rounded-lg border border-purple-400/30 bg-white/5 p-4 space-y-1">
        <h2 class="title-gradient font-semibold">Orders</h2>
        <p class="text-slate-300 text-sm">
          Place an order for a customer against one or more products, and track its status through
          its lifecycle.
        </p>
      </div>
      <div class="rounded-lg border border-purple-400/30 bg-white/5 p-4 space-y-1">
        <h2 class="title-gradient font-semibold">Roles</h2>
        <p class="text-slate-300 text-sm">
          Email/password authentication, with admin vs. customer access.
        </p>
      </div>
    </div>
  </div>
</template>
