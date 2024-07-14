import { createRouter, createWebHistory } from 'vue-router'
import Signup from '../component/Signup.vue'
import Login from '../component/Login.vue'
import Dash from '../component/dash.vue'

import axios from 'axios';

async function authGuard(to, from, next) {
  try {
    axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const response = await axios.get('/pull');
    //console.log(response.status == 200);
    if (response.status === 200) {

        localStorage.setItem('userData', JSON.stringify(response.data));
        if(to.path==='/dash'){

            next();
        }else{

          next('/dash');
        }
      }
  } catch (error) {
    if (to.path === '/dash') {
        console.log("login")
      next('/');
    } else {
      next();
    }
  }
}



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/signup',
      name: 'Signup',
      component: Signup,
      meta: { title: 'Sign Up' },
      beforeEnter: authGuard
    },
    {
      path: '/',
      name: 'Login',
      component: Login,
      meta: { title: 'Login' },
      beforeEnter: authGuard
    },
    {
        path: '/dash',
        name: 'Dash',
        component: Dash,
        meta: { title: 'Welcome' },
        beforeEnter: authGuard
      },

  ]
})
router.beforeEach((to, from, next) => {
    document.title = to.meta.title || 'Custom-Login-Page';
    next();
});
export default router
