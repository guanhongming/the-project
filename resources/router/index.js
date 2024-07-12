import { createRouter, createWebHistory } from 'vue-router'
import Signup from '../component/Signup.vue'
import Login from '../component/Login.vue'
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/signup',
      name: 'Signup',
      component: Signup
    },
    {
      path: '/',
      name: 'Login',
      component: Login
    },

  ]
})

export default router
