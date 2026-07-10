<script setup>
import NavBar from './components/NavBar.vue'
import AuthModal from './components/AuthModal.vue'
</script>

<template>
  <div class="min-h-screen bg-gradient-to-b from-[#1e1b3a] to-[#2c1a3a]">
    <NavBar />
    <main class="max-w-5xl mx-auto p-6">
      <RouterView />
    </main>
    <AuthModal />
  </div>
</template>
