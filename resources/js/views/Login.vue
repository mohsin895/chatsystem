<template>
  <div class="container mx-auto">
    <div class="font-bold text-black mt-40">
      <div class="erp-login">
        <div class="login-page bg-white w-96" >
          
          
          
          <div class="flex items-left justify-left space-x-4 mt-5">
            <span v-bind:class="[ activetab === '1' ? 'active-margin' : '' ]">
                <h5 
                v-on:click="activetab='1'" v-bind:class="[ activetab === '1' ? 'active' : '' ]"
                class="p-2 cursor-pointer">
                Login
                </h5>
           </span>
           <span v-bind:class="[ activetab === '2' ? 'active-margin' : '' ]">
                <h5 
                v-on:click="activetab='2'" v-bind:class="[ activetab === '2' ? 'active' : '' ]"
                style="margin-left: 10px;" class="p-2 cursor-pointer">
                Registartion
                </h5>
              </span>
           
          </div>
          <form @submit.prevent="loginMethod(1)" class="login-border p-4" v-if="activetab==1">
            <div class="erp-form ">
              <div class="flex">
                <label for="username">Email:</label>
                <input type="email" id="email" v-model="email" class="ml-2 h-9 pl-2 text-black border focus:outline-none focus:ring focus:border-loginFocus" />
              </div>
              
              <div class="mt-4 flex">
                <label for="password">Password:</label>
                <input type="password" id="password" v-model="password" class="ml-2 h-9 pl-2 text-black border focus:outline-none focus:ring focus:border-loginFocus" />
              </div>
            </div>
            <div class="text-center">
              <button class="bg-orange-500 mx-auto w-36 h-10 text-white cursor-pointer" type="submit">Login</button>
            </div>
          </form>
          <form @submit.prevent="regMethod(2)" class="border p-4" v-if="activetab==2">
            <div class="erp-form ">
              <div class="flex">
                <label for="username" style="width: 60px;">Name:</label>
                <input type="text" id="name" v-model="name" class="ml-2 h-9 pl-2 text-black border focus:outline-none focus:ring focus:border-loginFocus" />
              </div>
              <div class="flex mt-4">
                <label for="email" style="width: 60px;">Email:</label>
                <input type="email" id="email" v-model="email" class="ml-2 h-9 pl-2 text-black border focus:outline-none focus:ring focus:border-loginFocus" />
              </div>
             
              <div class="mt-4 flex">
                <label for="password">Password:</label>
                <input type="password" id="password" v-model="password" class="ml-2 h-9 pl-2 text-black border focus:outline-none focus:ring focus:border-loginFocus" />
              </div>
            </div>
            <div class="text-center">
              <button class="bg-orange-500 mx-auto w-36 h-10 text-white cursor-pointer" type="submit">Registration</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, inject, ref } from 'vue';
import { useToast } from 'vue-toastification'; 
import { useRouter } from 'vue-router';
import axios from 'axios';


const baseUrl = inject('$baseUrl');
const toast = useToast();
const router = useRouter();
const activetab=ref('1');



const email = ref('');
const password = ref('');
const name = ref('');



const loginMethod = async () => {
  try {
    const formData = new FormData();
 
    formData.append("email", email.value);
    formData.append("password", password.value);
    

    const res = await axios.post("/login", formData,{
      withCredentials: true 
    });
    let msg = res.data.message;
    let errMsg = res.data.errMsg;

    if (res.data.status) {
      toast.success(msg);
      const token = res.data.token;
      localStorage.setItem('token', token);
      let authInfo = res.data.dataInfo;
      window.localStorage.setItem('authInfo', JSON.stringify(authInfo));
      if(authInfo.role == 1){
        router.push({ name: "AdminDashboard" });
      }
      
      if(authInfo.role == 2){
        router.push({ name: "CustomerDashboard" });
      }
       
      
     

    } else {
      toast.error(errMsg);
    }
  } catch (error) {
    console.error("Error occurred during login:", error);
  }
};
const regMethod = async () => {
  try {
    const formData = new FormData();
 
    formData.append("name", name.value);
    formData.append("email", email.value);
    formData.append("password", password.value);

    const res = await axios.post("/registration", formData);
    let msg = res.data.message;
    let errMsg = res.data.errMsg;

    if (res.data.status) {
      toast.success(msg);
      // const token = res.data.token;
      // localStorage.setItem('token', token);
      // if(dataId == 2){
      //   router.push({ name: "Dashboard" });
      //   let staffInfo = res.data.staffInfo;
      //   window.localStorage.setItem('staffInfo', JSON.stringify(staffInfo));
      
      // }
      // if(dataId == 1){
      //   router.push({ name: "DashboardEmployee" });
      // }
      name.value ="";
      email.value ="";
      activetab.value=1


    } else {
      toast.error(errMsg);
    }
  } catch (error) {
    console.error("Error occurred during login:", error);
  }
};

</script>

<style scoped>
.login-page {
  padding: 10px;
  margin: 60px auto;

  border-top: solid 2px #FB4506; 
}
.login-page img {
  height: 45px;
  width: 140px;
}
.erp-form {
  padding: 20px;
}
.active{
  background: #fff;
  padding: 7px 15px 9px;
  border: solid 1px #eee;
    border-top: solid 2px #72c02c;
    border-bottom: none !important; /* Example border-bottom style */
}
.login-border{
  padding: 10px 16px;
  border: solid 1px #eee;
}
.active-margin{
  margin-bottom: -1px;
}
</style>
