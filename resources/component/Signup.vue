<template>
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
        <input id="phoneNumber" v-model="formV.phoneNumber" type="text" class="form-control" required>
      </div>
      <div class="custom-hr"></div>
      <div class="form-group mb-3">
        <label for="username" class="label">Username </label>
        <input id="username" v-model="formV.username" type="text" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label for="password" class="label">Password </label>
        <input id="password" v-model="formV.password" type="password" class="form-control" required>
      </div>

      <div class="form-group mb-3">
        <label for="confirmPassword" class="label">Confirm Password</label>
        <input id="confirmPassword" v-model="confirmPassword" type="password" class="form-control" required>
      </div>

      <button class="btn btn-primary btn-block" type="submit">
        Sign Up
      </button>
    </form>

    <MessageCard :onError="false" hintText="Already have an account?" hintLink="/" hintLinkText="Login" />
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

@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

/* Apply the font to specific elements */
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
  margin-right: 10%; /* Add margin on the right side */
  margin-left: 20%; /* Add margin on the left side */

}

.content {
  max-width: 60%;
  width: 45%;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 8px;

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
  width: calc(100% ); /* Adjusted width to account for padding */
  padding: 12px;
  font-size: 16px;

  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 10px;
  box-sizing: border-box; /* Ensure padding is included in width */
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
  background-color: #f0efef; /* Updated background color */
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
  margin-top: 20px; /* Add margin to top */
  margin-bottom: 20px;
  color: #444444; /* Updated text color */
  font-size: 14px; /* Adjusted font size */
  box-sizing: border-box;
  margin: auto;
}


/* Styles for the area code dropdown */

.area-code-dropdown {
  position: relative;
  width: 30%; /* Adjust width as needed */

  margin-bottom: 10px;
}

.area-code-select {
  width: 100%;
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  appearance: none; /* Remove default appearance */
  -webkit-appearance: none; /* For older versions of Chrome/Safari */
  background-color: #ffffff;
  cursor: pointer;
  outline: none; /* Remove outline on focus */
}

.area-code-select option {
  padding: 10px;
}

.area-code-select:focus {
  border-color: #007bff; /* Example focus color */
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Example focus shadow */
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
  height: 1px; /* Adjust thickness as needed */
  background-color: #ccc; /* Color of the horizontal rule */
  margin: 20px 0; /* Adjust vertical margin as needed */
  position: relative;
}


  </style>
