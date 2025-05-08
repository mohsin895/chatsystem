<template>
    <div class="layout">
      <nav-bar />
      <div class="erp-body flex min-h-[90vh] pt-11">
        <div class="right-side hidden lg:block bg-rightBarColor" >
          <header-page />
        </div>
        <div class="left-side bg-bodyColor  w-full">
          <RouterView />
        </div>
      </div>
      <footer-page />
    </div>
  </template>
  
  <script setup>
  import HeaderPage from '@/layout/RightBar.vue';
  import FooterPage from '@/layout/Footer.vue';
  import NavBar from '@/layout/Navbar.vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter(); // Correctly call useRouter
  
  const token = localStorage.getItem("token");
  if (!token) {
    router.push({ name: 'login' }).catch(err => {
      console.error(err);
    });
  }
  </script>
  
  <style scoped>
  /* Your styles here */
  </style>
  