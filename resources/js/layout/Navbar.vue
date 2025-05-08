
<template>
  <div class="bg-black page-header fixed top-0 left-0 w-full  text-white h-16 z-50">
    <div class="header-body text-white flex justify-between items-center text-center" >
   
      <div class="menu-btn" @click="toggleSidebar">
  <icon name="align-justify" size="30px" class="text-white" style="margin-right: 8px" />
</div>
   
      <div class="ml-20">Logo</div>
      <div class="mr-10">
        <div class="wrapper">
          <ul class="main-menu">
           

            <li id="menu-item-15" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-15" >
           
              <ul class="sub-menu">
               
                <li id="menu-item-78" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-78" >
                  <a href="#" @click.prevent="logout()">Logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>

   
  </div>
</template>

<script setup>
import { ref,onMounted,onBeforeUnmount } from 'vue';
import { useRouter } from "vue-router";


const router = useRouter();


const isSidebarOpen = ref(false);
const activeSubMenu = ref(null);


const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleSubMenu = (menuName) => {
  activeSubMenu.value = activeSubMenu.value === menuName ? null : menuName;
};


const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("authInfo");
  router.push({ name: "login" });
};


</script>


<style scoped>
@media print {
           
           .bg-black.page-header.fixed {
           display: none;
   }

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
.right-nav {
  margin-top: 46px;
}
.wrapper {
  width: 100%;

  text-align: center;
}
.main-menu {
  text-align: left;
  display: inline-block;
}
.main-menu a,
.main-menu a:hover {
  text-decoration: none;
}
.main-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}
.main-menu li {
  display: block;
  padding: 0;
  position: relative;
  vertical-align: top;
}

.main-menu > li > a {
  display: block;
  padding: 0.75em 1.25em;
}
.main-menu ul.sub-menu {
  position: relative;
  padding: 0;
}
.main-menu ul.sub-menu li {
  display: block;
}
.main-menu ul.sub-menu a {
  background-color: #666;
  color: #fff;
  padding: 0.75em 1.25em;
  display: block;
}

.main-menu li {
  display: inline-block;
}
.main-menu > li > a {
  display: block;
}
.main-menu > li > a:hover {
  background-color: #f0f0f0;
  color: #039;
}

.main-menu > li:hover ul.sub-menu,
.main-menu > li:focus ul.sub-menu {
  visibility: visible;
  opacity: 1;
}
.main-menu > li.menu-item-has-children {
  padding-right: 1.5em;
  overflow: hidden;
}
.main-menu > li.menu-item-has-children a {
  padding: 0.75em 0.7em 0.7em 1.25em;
}
.main-menu > li.menu-item-has-children:hover a {
  background-color: #666;
  color: #fff;
}

.main-menu > li.menu-item-has-children:after {
  content: "";
  position: absolute;
  right: 0.5em;
  top: 1.2em;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 6px 5px 0 5px;
  border-color: #007bff transparent transparent;
}

.main-menu > li.menu-item-has-children:hover,
.main-menu > li.menu-item-has-children:focus {
  overflow: visible;
  background-color: #666;
}

.main-menu ul.sub-menu {
  width: 12em;
  position: absolute; /*top:100%;*/
  top: 0;
  z-index: 900;
  padding-top: 3em;
}
.main-menu ul.sub-menu li {
  opacity: 0;
}

.main-menu > li.menu-item-has-children:hover ul.sub-menu,
.main-menu > li.menu-item-has-children:focus ul.sub-menu {
  top: 100%;
  padding-top: 0;
}

.main-menu > li.menu-item-has-children:hover ul.sub-menu li,
.main-menu > li.menu-item-has-children:focus ul.sub-menu li {
  opacity: 1;
}

.main-menu ul.sub-menu li {
  display: block;
}
.main-menu ul.sub-menu a {
  display: block;
}
.main-menu ul.sub-menu a:hover {
  background-color: #999;
}

a,
.sub-menu,
.menu-item {
  -webkit-transition: all 0.3s ease-out;
  -moz-transition: all 0.3s ease-out;
  -ms-transition: all 0.3s ease-out;
  -o-transition: all 0.3s ease-out;
  transition: all 0.3s ease-out;
}

*{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
      }

     

      .side-bar{
        background: #1b1a1b;
        backdrop-filter: blur(15px);
        width: 250px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: -250px;
        overflow-y: auto;
        transition: 0.6s ease;
        transition-property: left;
      }
      .side-bar::-webkit-scrollbar {
        width: 0px;
      }

      .side-bar.active{
        left: 0;
      }
      h1{
        text-align: center;
        font-weight: 500;
        font-size: 25px;
        padding-bottom: 13px;
        font-family: sans-serif;
        letter-spacing: 2px;
      }

      .side-bar .menu{
        width: 100%;
        margin-top: 30px;
      }

      .side-bar .menu .item{
        position: relative;
        cursor: pointer;
      }

      .side-bar .menu .item a{
        color: #fff;
        font-size: 16px;
        text-decoration: none;
        display: block;
        padding: 5px 30px;
        line-height: 45px;
      }

      .side-bar .menu .item a:hover{
        background: #33363a;
        transition: 0.3s ease;
      }

      .side-bar .menu .item i{
        margin-right: 15px;
      }

      .side-bar .menu .item a .dropdown{
        position: absolute;
        right: 0;
        margin: 5px;
        transition: 0.3s ease;
      }

      .side-bar .menu .item .sub-menu{
        background: #262627;
        display: none;
      }

      .side-bar .menu .item .sub-menu a{
        padding-left: 80px;
      }

      .rotate{
        transform: rotate(90deg);
      }

      .close-btn{
        position: absolute;
        color: #fff;
        font-size: 23px;
        right: 0px;
        margin: 15px;
        cursor: pointer;
      }

      .menu-btn{
        position: absolute;
        color: rgb(0, 0, 0);
        font-size: 35px;
        margin: 25px;
        cursor: pointer;
      }

      .main{
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 50px;
      }

      .main h1{
        color: rgba(255, 255, 255, 0.8);
        font-size: 60px;
        text-align: center;
        line-height: 80px;
      }

      @media (max-width: 900px){
        .main h1{
          font-size: 40px;
          line-height: 60px;
        }
      }
      @media (min-width: 1024px){
        .menu-btn{
         display: none;
        }
      }
      img{
        width: 60px;
      
        border-radius: 50%;
        margin-left: 70px;
        border: 3px solid #b4b8b9;
      }
      header{
        background: #33363a;
        padding-top: 5px;
        padding-bottom: 8px;
      }

      .side-bar.active {
  left: 0;
}

</style>