<template>
  <div v-if="showPopup" class="sms-verification-popup">
    <h3>Verify Phone Number</h3>
    <form @submit.prevent="sendOTP">
        <div class="mb-3">
        <label for="areaCode">Area Code: </label>
        <select v-model="areaCode" id="areaCode" required>
          <option v-for="code in areaCodes" :key="code" :value="code">{{ code }}</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="phoneNumber">Phone number:</label>
        <input id="phoneNumber" v-model="phoneNumber" type="text" />
      </div>

      <button class="btn btn-primary" type="submit" :disabled="!phoneNumber.trim()">Verify Phone Number</button>
    </form>

    <div v-if="showOTPForm">
      <form @submit.prevent="verifyOTP">
        <div class="mb-3">
          <label for="otp">Enter OTP:</label>
          <input id="otp" v-model="otp" type="text" />
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
      areaCodes: ['+1', '+44', '+91', '+81', '+86', '+49'] ,
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

        console.error('Registration failed:', error.response.data.errors);
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
.sms-verification-popup {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  background-color: white;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}
</style>
