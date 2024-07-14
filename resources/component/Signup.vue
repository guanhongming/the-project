<template>
 <SMSVerification :phone="formV.phoneNumber" :area="formV.areaCode" v-if="showSMSVerification" @verified="handleVerification"  @close="showSMSVerification=false"  />
    <div>
        <MessageCard v-show="iserror"
        :onError=iserror
      errorMessage="Passwords do not match."
    />

    <form name="signup-form" @submit.prevent="register">
      <div class="mb-3">
        <label for="areaCode">Area Code: </label>
        <select v-model="formV.areaCode" id="areaCode" required>
          <option v-for="code in areaCodes" :key="code" :value="code">{{ code }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="phoneNumber">Phone Number: </label>
        <input id="phoneNumber" v-model="formV.phoneNumber" type="text" required>
      </div>
      <div class="mb-3">
        <label for="username">Username: </label>
        <input id="username" v-model="formV.username" type="text" required/>
      </div>
      <div class="mb-3">
        <label for="password">Password: </label>
        <input id="password" v-model="formV.password" type="password" required/>
      </div>
      <div class="mb-3">
      <label for="confirmPassword">Confirm Password:</label>
      <input id="confirmPassword" v-model="confirmPassword" type="password" required/>
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
        areaCodes: ['+44', '+91', '+81', '+86', '+49'] ,
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

    handleVerification() {
    this.showSMSVerification=false;

},

async register() {
    console.log(this.formV);
    if(this.passwordsMatch){
      try {
        const scrf = await axios.get('/csrf');
            //console.log(scrf);
            const newCsrfToken = scrf.data.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const response = await axios.post('/register', this.formV);//csrf issue
        if (response.data.message === "OTP awaits") {
            this.showSMSVerification=true;
        }

      } catch (error) {

        console.error('Registration failed:', error.message);
        // Handle validation errors here
      }
    }}
  }}
  </script>

  <style scoped>
  </style>
