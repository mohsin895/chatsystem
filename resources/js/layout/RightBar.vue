<template>
    

       
  <div class="right-nav w-52 text-white  mt-5 " >
 
  <ul>
  

<li class="h-10 py-2 border-b-2 border-gray-500" :class="{ 'bg-rightActiveColor': $route.name === 'AdminDashboard', 'hover:bg-rightHoverColor': $route.name !== 'AdminDashboard' }">
    <span class="ml-4">
        <router-link :to="{ name: 'AdminDashboard' }">Dashboard</router-link>
    </span>
</li>

<li class="h-10 py-2 border-b-2 border-gray-500" :class="{ 'bg-rightActiveColor': $route.name === 'TicketList', 'hover:bg-rightHoverColor': $route.name !== 'TicketList' }">
    <span class="ml-4">
        <router-link :to="{ name: 'TicketList' }">Ticket</router-link>
    </span>
</li>

<li class="h-10 py-2 border-b-2 border-gray-500" :class="{ 'bg-rightActiveColor': $route.name === 'Message', 'hover:bg-rightHoverColor': $route.name !== 'Message' }">
    <span class="ml-4">
        <router-link :to="{ name: 'Message' }">Message</router-link>
    </span>
</li>

<li id="menu-item-78" class="h-10 py-2 border-b-2 border-gray-500 menu-item menu-item-type-post_type menu-item-object-page menu-item-78" >
    <span class="ml-4">
        <a href="#" @click.prevent="logout()">Logout</a>
      </span>
    
                  </li>


  </ul>

 
  </div>
       
   
</template>
<script setup>

import { useRouter } from "vue-router";


const router = useRouter();


const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("authInfo");
  router.push({ name: "login" });
};


</script>
<style scoped>
.active-li {
    background-color: blue; /* Change background color to highlight */
}
.page-header {
    width: 100%;
    padding: 0 20px 0 20px;
    margin: 0;
    border: 0px;
    padding: 0px;
    box-shadow: none;
    height: 46px;
    min-height: 46px;
    filter: none;
    background-image: none;
}


    </style>
