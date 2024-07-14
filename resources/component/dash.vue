<template>
    <div>
        <p>Name: {{ userData.name }}</p>
        <p>Phone: {{ userData.phone }}</p>


    </div>
    <div>
        <button @click="logout">Logout</button>
    </div>
</template>
<script>
import axios from 'axios';
export default {
name: 'dash',
  data() {
    return {
      userData:{
        name:'',
        phone:''
      },
    };
  },
  mounted() {
    this.userData = localStorage.getItem('userData');
    if (this.userData) {
      this.userData = JSON.parse(this.userData);
    }

  },



  methods: {

    async logout() {
      try {
        const scrf = await axios.get('/csrf');
            //console.log(scrf);
            const newCsrfToken = scrf.data.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', newCsrfToken);
        await axios.post('/logout');
        localStorage.removeItem('userData');
        delete axios.defaults.headers.common['X-CSRF-TOKEN'];
        this.$router.push('/');
      } catch (error) {
        console.error('Logout failed:', error);
      }
    }
  },


};
</script>
