<template>

    <div>
        <MessageCard v-show="iserror"
        :onError=iserror
      errorMessage="Passwords do not match."
    />
    <SMSVerification v-if="showSMSVerification" @verified="handleVerification"  @close="showSMSVerification=false"  />
    <form name="signup-form" @submit.prevent="handleSignup">
      <div class="mb-3">
        <label for="areaCode">Area Code: </label>
        <select v-model="formV.areaCode" id="areaCode">
          <option v-for="code in areaCodes" :key="code" :value="code">{{ code }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="phoneNumber">Phone Number: </label>
        <input id="phoneNumber" v-model="formV.phoneNumber" type="text" />
      </div>
      <div class="mb-3">
        <label for="username">Username: </label>
        <input id="username" v-model="formV.username" type="text" />
      </div>
      <div class="mb-3">
        <label for="password">Password: </label>
        <input id="password" v-model="formV.password" type="password" />
      </div>
      <div class="mb-3">
      <label for="confirmPassword">Confirm Password:</label>
      <input id="confirmPassword" v-model="confirmPassword" type="password" />
    </div>

      <button class="btn btn-outline-dark" type="submit">
        Sign Up
      </button>
    </form>
    <MessageCard
      :onError="false"
      hintText="Already have an account?"
      hintLink="/"
      hintLinkText="Login"
      
    />

</div>
  </template>

  <script>
  import MessageCard from './Card.vue';
  import SMSVerification from './smsVer.vue';

  export default {
    name: 'Signup',
    data() {
      return {
        formV: {
          areaCode: '',
          phoneNumber: '',
          username: '',
          password: ''
        },
        confirmPassword: '',
        iserror:false,
        areaCodes: ['+1', '+44', '+91', '+81', '+86', '+49'] ,
        showSMSVerification: false

      }
    },
    components:{
        MessageCard,
        SMSVerification
    },
    computed: {
    passwordsMatch() {
      return this.formV.password === this.confirmPassword;
    }
  },
    methods: {
      handleSignup() {
        if(this.passwordsMatch){
            this.iserror=false;
        console.log('Area Code:', this.formV.areaCode);
        console.log('Phone Number:', this.formV.phoneNumber);
        console.log('Username:', this.formV.username);
        console.log('Password:', this.formV.password);
        this.showSMSVerification=true;
      }else{
        this.iserror=true;
      }
    },
    handleVerification() {
    this.showSMSVerification=false;

}
  }}
  </script>

  <style scoped>
  </style>
