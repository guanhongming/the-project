<template>
      <div v-show="isLoading"class="loading-overlay">
  <div class="dot dot-1"></div>
  <div class="dot dot-2"></div>
  <div class="dot dot-3"></div>
</div>
  <div v-if="showPopup" class="sms-verification-popup">
    <MessageCard v-show="error"class="warn"
        :onError="true"
        :errorMessage="message"
      />
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

      <button class="btn btn-primary" type="submit" :disabled="cooldownActive">Verify Phone Number <span v-if="cooldownActive"> ({{ cooldownTime }}s)</span></button>
    </form>

    <div v-if="showOTPForm">
      <form @submit.prevent="verifyOTP" class="content">
        <div class="mb-3 form-group">
          <label for="otp" class="label">Enter OTP</label>
          <input class="form-control" id="otp" v-model="otp" type="text" />
        </div>
        <button class="btn btn-primary" type="submit" >Verify OTP</button>
      </form>
    </div>
  <div>
    <button class="btn btn-secondary" @click="$emit('close');">Close</button>
  </div>
  </div>
</template>

<script>
import MessageCard from './Card.vue';
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
        isLoading: false,
      otp: '',
      phoneNumber:'',
      areaCode:'',
      showPopup: true,
      areaCodes: ['+44', '+91', '+81', '+86', '+49'] ,
      showOTPForm: false,
        message:'',
        cooldownActive: false,
      cooldownTime: 0,
      cooldownDuration: 60,
        error:false
    };
  },
  components:{
        MessageCard,
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
    startCooldownTimer() {
      const timer = setInterval(() => {
        if (this.cooldownTime > 0) {
          this.cooldownTime--;
        } else {
          clearInterval(timer);
          this.cooldownActive = false;
        }
      }, 1000);
    },
    async sendOTP() {
        this.error=false;
        if (this.cooldownActive) return;


this.cooldownActive = true;
this.cooldownTime = this.cooldownDuration;


this.startCooldownTimer();
        try {
            this.isLoading=true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const response = await axios.post('/otp', {phone:this.phoneNumber,area:this.areaCode});//csrf issue
        if (response.data.message === "OTP sent") {
            this.showOTPForm=true;
        }

      } catch (error) {
        this.message=error.response.data.message;
        this.error=true;


      }finally{
        this.isLoading=false;
      }
    },
    async verifyOTP() {
        this.error=false;
        try {
            this.isLoading=true;
            const scrf = await axios.get('/csrf');
            //console.log(scrf);
            const newCsrfToken = scrf.data.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

        const response = await axios.post('/verify', {otp:this.otp});//csrf issue

        if (response.status === 200) {
            this.$router.push({ name: 'Dash' });
        }

      } catch (error) {
        this.message=error.response.data.message;
        this.error=true;

      }finally{
        this.isLoading=false;
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');


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
.warn {
  background-color: #ffcccc;
  border: 1px solid #ff0000;
  padding: 10px;
  margin-top: 10px;
  border-radius: 4px;
  font-size: 16px;
  color: #ff0000;
}
.sms-verification-popup h3 {
  color: #333;
}

.sms-verification-popup label {
  color: #333;

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
  background-color: #000000;
  color: white;
  border: none;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  border-radius: 10px;
  cursor: pointer;
  margin: 10px;
}

.sms-verification-popup button:hover {
  background-color: #3d3d3d;
}

.sms-verification-popup .btn-secondary {
  background-color: #a82121;
  color: white;
}

.sms-verification-popup .btn-secondary:hover {
  background-color: #c93131;
}




.area-code-dropdown {
  position: relative;
  width: 40%;

  margin: 10px;
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
  width: calc(100% );
  padding: 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-bottom: 10px;
  box-sizing: border-box;
}
.content {
  max-width: 90%;
  width: 90%;

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
