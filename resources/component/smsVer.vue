<template>
  <div v-if="showPopup" class="sms-verification-popup">
    <h3>Verify Phone Number</h3>
    <form @submit.prevent="sendOTP">
      <div class="mb-3">
        <label for="phoneNumber">Enter your phone number:</label>
        <input id="phoneNumber" v-model.trim="phoneNumber" type="text" />
      </div>
      <button class="btn btn-primary" type="submit" :disabled="!phoneNumber.trim()">Verify Phone Number</button>
    </form>
    
    <div v-if="showOTPForm">
      <form @submit.prevent="verifyOTP">
        <div class="mb-3">
          <label for="otp">Enter OTP:</label>
          <input id="otp" v-model.trim="otp" type="text" />
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
  data() {
    return {
      phoneNumber: '',
      otp: '',
      showPopup: true,
      showOTPForm: false
    };
  },
  methods: {
    sendOTP() {
      // Send API request to send OTP to phoneNumber
      // Example: Mocking API call
      setTimeout(() => {
        this.showOTPForm = true;
      }, 1000); // Simulating delay for demo
    },
    verifyOTP() {
      // Send API request to verify OTP
      // Example: Mocking OTP verification
      setTimeout(() => {
        alert('OTP Verified Successfully!');
        this.showPopup = false;
        this.showOTPForm = false;
        // Optionally emit an event to notify parent component (Login or Signup)
      }, 1000); // Simulating delay for demo
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
