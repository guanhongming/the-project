<template>
    <div>
        <SMSVerification v-if="showSMSVerification" @verified="handleVerification" @close="showSMSVerification=false" />
    <form name="login-form" @submit.prevent="handleLogin">
      <div class="mb-3">
        <label for="username">Username: </label>
        <input id="username" v-model.trim.lazy="formV.username" type="text" />
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

  export default {
    name: 'Login',
    data() {
      return {
        formV: {
          username: '',
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
      handleLogin() {

        console.log('Username:', this.formV.username);
        console.log('Password:', this.formV.password);
        this.showSMSVerification=true;

      },
      handleVerification() {
    this.showSMSVerification=false;

}
    }
  }
  </script>

  <style scoped>

  </style>
