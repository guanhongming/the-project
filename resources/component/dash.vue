<template>
    <div class="dashboard">
        <div class="user-info">
            <p class="welcome-text">{{ greeting }},  <span class="name">{{ userData.name }}</span></p>
            <p class="phone-text">@ <span class="phone">{{ userData.phone }}</span></p>
        </div>
        <div class="logout-container">
            <button class="logout-btn" @click="logout">Logout</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Dashboard',
    data() {
        return {
            userData: {
                name: '',
                phone: ''
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
    computed:{
        greeting() {
            const hour = new Date().getHours();
            if (hour >= 4 && hour < 12) {
                return 'Good morning';
            } else if (hour >= 12 && hour < 18) {
                return 'Good afternoon';
            } else {
                return 'Good evening';
            }
        }
    }
};
</script>

<style scoped>
.dashboard {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.user-info {
    text-align: center;
    margin-bottom: 20px;
}

.welcome-text {
    font-size: 50px;
    font-weight: bold;
    margin-bottom: 10px;
}

.phone-text {
    font-style: italic;
    color: #666;
}

.logout-container {
    text-align: center;
}

.logout-btn {
    background-color: #a82121;
    color: white;
    border: none;
    padding: 10px 20px;
    margin-top: 70px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
}

.logout-btn:hover {
    background-color: #c93131;
}
</style>
