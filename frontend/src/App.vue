<template>
  <div id="app">
    <Navbar v-if="!$route.meta.hideNavbar" />
    
    <main class="container mt-4">
      <router-view /> 
    </main>
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue';
</script>

<style>
body {
  background-color: #f8f9fa; 
}
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>