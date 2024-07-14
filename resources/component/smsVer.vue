<template>
  <div v-if="showPopup" class="sms-verification-popup">
    <h2 class="">Verify Your Phone Number</h2>
    <form @submit.prevent="sendOTP" class="content">
        <div class="area-code-dropdown mb-3">
        <label for="areaCode"  class="label">Area Code </label>
        <select v-model="areaCode" id="areaCode" class="area-code-select" required>
          <option v-for="code in areaCodes" :key="code" :value="code">{{ code }}</option>
        </select>
      </div>
      <div class="form-group mb-3">
        <label for="phoneNumber" class="label">Phone Number</label>
        <input class="form-control" id="phoneNumber" v-model="phoneNumber" type="text" />
      </div>

      <button class="btn btn-primary" type="submit" :disabled="!phoneNumber.trim()">Verify Phone Number</button>
    </form>

    <div v-if="showOTPForm">
      <form @submit.prevent="verifyOTP" class="content">
        <div class="mb-3 form-group">
          <label for="otp" class="label">Enter OTP</label>
          <input class="form-control" id="otp" v-model="otp" type="text" />
        </div>
        <button class="btn btn-primary" type="submit" :disabled="!otp.trim()">Verify OTP</button>
      </form>
    </div>
  <div>
    <button class="btn btn-secondary" @click="$emit('close');">Close</button>
  </div>
  </div>
</template>

<script>
export default {
  name: 'SMSVerification',
  props: {
    phone:{
        type:String,
        default:undefined
    },
    area:{
        type:String,
        default:undefined
    }
  },
  data() {
    return {
      otp: '',
      phoneNumber:'',
      areaCode:'',
      showPopup: true,
      areaCodes: ['+44', '+91', '+81', '+86', '+49'] ,
      showOTPForm: false,

    };
  },
  created() {
    console.log(this.phone);
    if (this.area) {
      this.areaCode = this.area;
    }
    if (this.phone) {
      this.phoneNumber = this.phone;
    }
  },
  methods: {
    async sendOTP() {
        try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const response = await axios.post('/otp', {phone:this.phoneNumber,area:this.areaCode});//csrf issue
        if (response.data.message === "OTP sent") {
            this.showOTPForm=true;
        }

      } catch (error) {

        console.error('Registration failed:(1)', error.response.data.errors);
        // Handle validation errors here
      }
    },
    async verifyOTP() {
        try {
            const scrf = await axios.get('/csrf');
            //console.log(scrf);
            const newCsrfToken = scrf.data.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

        const response = await axios.post('/verify', {otp:this.otp});//csrf issue
        console.log(response);
        if (response.status === 200) {
            this.$router.push({ name: 'Dash' });
        }

      } catch (error) {

        console.error('Registration failed:', response);
        // Handle validation errors here
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

/* Apply the font to specific elements */
*, *::before, *::after {
  font-family: 'Roboto', sans-serif;
}
.sms-verification-popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  background-color: white;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.199);
  z-index: 1000;
  border-radius: 8px;

  overflow: hidden;
}

.sms-verification-popup h3 {
  color: #333; /* Black accent color */
}

.sms-verification-popup label {
  color: #333;
  /* Black accent color */
}

.sms-verification-popup input[type="text"],
.sms-verification-popup select {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.sms-verification-popup button {
  background-color: #333; /* Black accent color */
  color: white;
  border: none;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  border-radius: 4px;
  cursor: pointer;
  margin: 10px;
}

.sms-verification-popup button:hover {
  background-color: #555; /* Darker black accent color */
}

.sms-verification-popup .btn-secondary {
  background-color: #a82121; /* Gray for secondary button */
  color: white;
}

.sms-verification-popup .btn-secondary:hover {
  background-color: #c93131; /* Darker gray for secondary button hover */
}


/* Styles for the area code dropdown */

.area-code-dropdown {
  position: relative;
  width: 40%; /* Adjust width as needed */

  margin: 10px;
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
.label {
  display: block;
  text-align: left;
  font-weight: bold;
  margin-bottom: 10px;
  font-size: 18px;
  color: #575656;
}
.form-group {
  margin: 10px;
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
.content {
  max-width: 90%;
  width: 90%;


}
</style>
