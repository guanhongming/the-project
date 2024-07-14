<template>
      <div v-show="isLoading"class="loading-overlay">
  <div class="dot dot-1"></div>
  <div class="dot dot-2"></div>
  <div class="dot dot-3"></div>
</div>
 <SMSVerification :phone="formV.phoneNumber" :area="formV.areaCode" v-if="showSMSVerification" @verified="handleVerification"  @close="showSMSVerification=false"  />
 <div class="container">
    <MessageCard v-show="iserror" :onError="iserror" errorMessage="Passwords do not match." />

    <form name="signup-form" @submit.prevent="register" class="content">
        <h1>Welcome Aboard</h1>
      <div class="area-code-dropdown mb-3">
        <label for="areaCode" class="label">Area Code </label>
        <select v-model="formV.areaCode" id="areaCode" class="area-code-select" required>
          <option v-for="code in areaCodes" :key="code" :value="code">{{ code }}</option>
        </select>
      </div>

      <div class="form-group mb-3">
        <label for="phoneNumber" class="label">Phone Number </label>
        <input id="phoneNumber" v-model.lazy.trim="formV.phoneNumber" @change="VPhone" @type="text" class="form-control" required>
      </div>
      <div class="custom-hr"></div>
      <div class="form-group mb-3">
        <label for="username" class="label">Username </label>
        <input id="username" v-model.lazy.trim="formV.username" @change="VName" type="text" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label for="password" class="label">Password </label>
        <input id="password" v-model.lazy.trim="formV.password"  @change="VPassword"type="password" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label for="confirmPassword" class="label">Confirm Password</label>
        <input id="confirmPassword" v-model.lazy.trim="confirmPassword" @change="VConfirm"type="password" class="form-control" required>
      </div>

      <button class="btn btn-primary btn-block" type="submit">
        Sign Up
      </button>
    </form>

    <div class="message-container">
<div  v-for="(error, index) in errors" :key="index"  class="message-item">
      <MessageCard class="warn"
        :onError="true"
        :errorMessage="error"
      />
</div>

<div class="top message-item">
    <MessageCard :onError="false" hintText="Already have an account?" hintLink="/" hintLinkText="Login" />
</div>
</div>

  </div>
  </template>

  <script>
  import MessageCard from './Card.vue';
  import SMSVerification from './smsVer.vue';

  export default {
    name: 'Signup',
    data() {
      return {
        isLoading: false,
        formV: {
          areaCode: '',
          phoneNumber: '',
          username: '',
          password: ''
        },
        confirmPassword: '',
        errors: [],
        areaCodes: ['+44', '+91', '+81', '+86', '+49'] ,
        showSMSVerification: false

      }
    },
    components:{
        MessageCard,
        SMSVerification
    },
    computed: {

  },
    methods: {
        VConfirm(){
            const size="Passwords do not match";
            if(this.formV.password !== this.confirmPassword){

                if(!this.errors.includes(size))
                this.errors.push(size);
            }else{
                this.errors=this.errors.filter(error => error !== size);}
        },
        VPhone(){
            const size="Invalid phone number";
            if (this.formV.phoneNumber.length !==11 ) {
                if(!this.errors.includes(size))
                this.errors.push(size);
            }else{
                this.errors=this.errors.filter(error => error !== size);}
        },
        VName(){
            const size="Username requires at least 4 characters.";
            if (this.formV.username.length < 4 ) {
                if(!this.errors.includes(size))
                this.errors.push("Username requires at least 4 characters.");


            }else{
                this.errors=this.errors.filter(error => error !== "Username requires at least 4 characters.");
            }
            const specialCharRegex = /[!@#$%^&*(),.?":{}|<>]/;

        if (specialCharRegex.test(this.formV.username) ){
            if(!this.errors.includes('Username should not contain special characters.'))
            this.errors.push('Username should not contain special characters.');
        } else {
            this.errors=this.errors.filter(error => error !== "Username should not contain special characters.");
        }
        },
        VPassword(){
            if (this.formV.password.length < 8) {
                if(!this.errors.includes('Password requires at least 8 characters.'))
                this.errors.push("Password requires at least 8 characters.");

            }else{
                this.errors=this.errors.filter(error => error !== "Password requires at least 8 characters.");
            }
        },
    handleVerification() {
    this.showSMSVerification=false;

},
clearErrors(size){
    this.errors=this.errors.filter(error => error !== size);
},
async register() {
    //console.log(this.formV);
    if(this.errors.length===0){
      try {
        this.isLoading=true;
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

        const size="Error ("+error.response.data.message+")";
                if(!this.errors.includes(size)){
               this.errors.push(size);}
               setTimeout(() => {
          this.clearErrors(size);
        }, 5000);

      }finally{
        this.isLoading=false;
      }
    }}
  }}
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

  padding: 20px;
  box-sizing: border-box;
  margin-right: 10%;
  margin-left: 20%;

}

.content {
  max-width: 60%;
  width: 45%;
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
  margin: 20px;
  border-radius: 10px;
}

.btn-primary:hover {
  background-color: #3d3d3d;

}

.message-card {
    max-width: 3000px;
  padding: 30px;
  background-color: #f8f8f8;
  border-radius: 10px;
  box-shadow: 0 0 30px rgba(59, 59, 59, 0.082);
  text-align: center;
  margin-top: 10px;
  margin-bottom: 10px;
  color: #444444;
  font-size: 14px;
  box-sizing: border-box;

}




.area-code-dropdown {
  position: relative;
  width: 30%;

  margin-bottom: 10px;
}

.area-code-select {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  appearance: none;
  -webkit-appearance: none;
  background-color: #ffffff;
  cursor: pointer;
  outline: none;
}

.area-code-select option {
  padding: 10px;
}

.area-code-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}



/* Hover effect */

.area-code-dropdown:hover::after {
  color: #333333;
}

/* Focus effect */

.area-code-dropdown:focus-within::after {
  color: #007bff;
}

.custom-hr {
  height: 1px;
  background-color: #ccc;
  margin: 20px 0;
  position: relative;
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
  margin-bottom: 1px;
}
.top{
    margin-top: 40px;
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
