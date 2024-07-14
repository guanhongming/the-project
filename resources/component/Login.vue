<template>
  <div v-show="isLoading"class="loading-overlay">
  <div class="dot dot-1"></div>
  <div class="dot dot-2"></div>
  <div class="dot dot-3"></div>
</div>
    <div class="container">
        <div class="content">
        <SMSVerification v-if="showSMSVerification" @verified="handleVerification" @close="showSMSVerification=false" />
    <form name="login-form" @submit.prevent="handleLogin" class="form">
        <h1>Welcome Back</h1>
        <div class="form-group">
      <div class="mb-3">
        <label for="username" class="label">Username </label>
        <input id="username" v-model.trim.lazy="formV.name"  placeholder="Enter your username" type="text" class="form-control"/>
      </div>
      <div class="mb-3">
        <label for="password" class="label">Password </label>
        <input id="password" v-model.trim.lazy="formV.password" placeholder="Enter your password"  type="password" class="form-control"/>
      </div>
      <button class="btn btn-primary btn-block" type="submit">
        Login
      </button>
    </div>
    </form>

</div>
<div class="message-container">
<div  v-for="(error, index) in errors" :key="index"  class="message-item">
      <MessageCard class="warn"
        :onError="true"
        :errorMessage="error"
      />
</div>
<div class="message-item">
<MessageCard
      :onError="false"
      hintText="Don't have an account?"
      hintLink="/signup"
      hintLinkText="Sign up"
    />
</div>
</div>
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
        isLoading: false,
        formV: {
          name: '',
          password: ''
        },
        showSMSVerification: false,
        errors: [],
      }
    },
    components:{
        MessageCard,
        SMSVerification
    },
    watch: {

  },
    methods: {
        errorTImeout(){
            setTimeout(() => {
          this.isError = false;
        }, 5000);
        },

        validate(){
            const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;
            if(this.formV.name.length < 4 || specialCharRegex.test(this.formV.name) || this.formV.password.length < 8){
                if(!this.errors.includes("Incorrect username or password"))
                this.errors.push("Incorrect username or password");
                return false;
            }
            return true;
        },
      async handleLogin() {
        if(this.validate()){
            this.errors=this.errors.filter(error => error !== "Incorrect username or password");
        try {
            this.isLoading = true;
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
        }else{
            if(!this.errors.includes("Incorrect username or password"))
                this.errors.push("Incorrect username or password");
        }

      }finally{
        this.isLoading = false;
      }}

        //this.showSMSVerification=true;

      },



    },

  }
  </script>

  <style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');


*, *::before, *::after {
  font-family: 'Roboto', sans-serif;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-image: url('~@/assets/background.jpg');
  background-size: cover;
  background-position: center;
  padding: 20px;
  box-sizing: border-box;
  margin-right: 10%;
  margin-left: 16%;
}

.content {
  max-width: 60%;
  width: 55%;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 0 50px rgba(0, 0, 0, 0.233);
  border-radius: 10px;
  text-align: center;
  overflow: hidden;

}
.label {
  display: block;
  text-align: left;
  font-weight: bold;
  margin-bottom: 10px;
  font-size: 18px;
  color: #575656;
}
.form-group {
  margin-bottom: 20px;
}

.form-control {
  width: calc(100% );
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 10px;
  box-sizing: border-box;
}


.btn-primary {
  background-color: #000000;
  border: none;
  color: #fff;
  padding: 12px 30px;
  font-size: 16px;
  cursor: pointer;
  margin-top: 20px;
  border-radius: 10px;
}

.btn-primary:hover {
  background-color: #3d3d3d;

}

.message-card {
    max-width: 300px;
  padding: 30px;
  background-color: #f8f8f8;
  border-radius: 10px;
  box-shadow: 0 0 30px rgba(59, 59, 59, 0.082);
  text-align: center;
  margin-top: 50px;
  margin-bottom: 20px;
  color: #444444;
  font-size: 14px;
  box-sizing: border-box;

  overflow: hidden;
}

.warn {
  background-color: #ffcccc;
  border: 1px solid #ff0000;
  padding: 10px;
  margin-top: 10px;
  border-radius: 4px;
  font-size: 16px;
  color: #ff0000;
}
.message-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin:auto;
}

.message-item {
  margin-bottom: 10px;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.5);
  display: flex;
  justify-content: center;

  transition: opacity 0.1s ease;
  align-items: center;
  z-index: 9999;
}

.loading-animation {
  display: flex;
  justify-content: center;
  align-items: center;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #303030;
  margin: 0 5px;
  animation: dot-pulse 1s infinite ease-in-out;
}

.dot-1 {
  animation-delay: 0s;
}

.dot-2 {
  animation-delay: 0.2s;
}

.dot-3 {
  animation-delay: 0.4s;
}

@keyframes dot-pulse {
  0%, 80%, 100% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  40% {
    transform: scale(1.2);
    opacity: 1;
  }
}


  </style>
