<template>
    <div>
        <SMSVerification v-if="showSMSVerification" @verified="handleVerification" @close="showSMSVerification=false" />
    <form name="login-form" @submit.prevent="handleLogin">
      <div class="mb-3">
        <label for="username">Username: </label>
        <input id="username" v-model.trim.lazy="formV.name" type="text" />
      </div>
      <div class="mb-3">
        <label for="password">Password: </label>
        <input id="password" v-model.trim.lazy="formV.password" type="password" />
      </div>
      <button class="btn btn-outline-dark" type="submit">
        Login
      </button>
    </form>
    <MessageCard
      :onError="false"
      hintText="Don't have an account?"
      hintLink="/signup"
      hintLinkText="Sign up"
    />

</div>
  </template>

  <script>
  import MessageCard from './Card.vue';
  import SMSVerification from './smsVer.vue';
  //import axios from '../js/axios-instance';
  export default {
    name: 'Login',
    data() {
      return {
        formV: {
          name: '',
          password: ''
        },
        showSMSVerification: false
      }
    },
    components:{
        MessageCard,
        SMSVerification
    },
    methods: {

      async handleLogin() {
        try {
            const scrf = await axios.get('/csrf');
            //console.log(scrf);
            const newCsrfToken = scrf.data.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
           // console.log(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

           // console.log(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const response = await axios.post('/login', this.formV);//csrf issue
        if (response.data.message === "ok") {
            this.$router.push({ name: 'Dash' });
        }

      } catch (error) {
        if (error.response.data.message === "otp awaits") {
            this.showSMSVerification=true;
            console.log('ok');
        }

      }

        //this.showSMSVerification=true;

      },



    },

  }
  </script>

  <style scoped>

  </style>
