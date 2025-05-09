import { createRouter, createWebHistory } from 'vue-router'
import Layout from '../layout/mainLayout.vue'
import CustomerLayout from '@/layout/CustomerLayout.vue'
import Heropage from '../views/Login.vue'
import NotFound from '../views/Notfound.vue'
import TicketList from '../views/Admin/Ticket/Ticket.vue'
import CustomerTicketList from '../views/Customer/Ticket/Ticket.vue'
import AdminDashboard from '../views/Admin/Dashboard.vue'
import CustomerDashboard  from '../views/Customer/Dashboard.vue'
import CustomerTicketView from '../views/Customer/Ticket/View.vue'
import TicketView from '../views/Admin/Ticket/View.vue'
import Message from '@/views/Admin/Message.vue'


const routes  = [
    {
       path:'/',
       name:'login',
       component: Heropage
    },
    {
      path: '/admin',
      name: 'home',
      component: Layout,
      children:[
        {
          path:'dashboard',
          name:'AdminDashboard',
          component: AdminDashboard
        },

        {
          path:'ticket/list',
          name:'TicketList',
          component: TicketList
        },
        {
          path:'ticket/:dataId',
          name:'TicketView',
          component: TicketView
        },
        {
          path:'message/list',
          name:'Message',
          component: Message
        },
       
      ]
    },
    {
      path: '/customer',
      name: 'CustomerLayout',
      component: CustomerLayout,
      children:[
  
        {
          path:'dashboard',
          name:'CustomerDashboard',
          component: CustomerDashboard
        },
        {
          path:'ticket/list',
          name:'CustomerTicketList',
          component: CustomerTicketList
        },
        {
          path:'ticket/:dataId',
          name:'CustomerTicketView',
          component: CustomerTicketView
        },
       
      ]
    },
   
    { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
  ]

  


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})
export default router
