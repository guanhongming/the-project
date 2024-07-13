import { createRouter, createWebHistory } from 'vue-router'
import Signup from '../component/Signup.vue'
import Login from '../component/Login.vue'
import Dash from '../component/dash.vue'
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
    {
        path: '/dash',
        name: 'Dash',
        component: Dash
      },

  ]
})

export default router
