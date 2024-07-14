<template>
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
          this.cooldownActive = false; // Reset cooldown state
        }
      }, 1000); // Update timer every second
    },
    async sendOTP() {
        this.error=false;
        if (this.cooldownActive) return;

// Set cooldown state
this.cooldownActive = true;
this.cooldownTime = this.cooldownDuration;

// Start cooldown timer
this.startCooldownTimer();
        try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        const response = await axios.post('/otp', {phone:this.phoneNumber,area:this.areaCode});//csrf issue
        if (response.data.message === "OTP sent") {
            this.showOTPForm=true;
        }

      } catch (error) {
        this.message=error.response.data.message;
        this.error=true;


      }
    },
    async verifyOTP() {
        this.error=false;
        try {
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
.warn {
  background-color: #ffcccc; /* Light red background */
  border: 1px solid #ff0000; /* Red border */
  padding: 10px;
  margin-top: 10px;
  border-radius: 4px;
  font-size: 16px;
  color: #ff0000; /* Red text color */
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
  background-color: #000000; /* Black accent color */
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
  background-color: #3d3d3d; /* Darker black accent color */
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
