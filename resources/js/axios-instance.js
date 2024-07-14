// axios-instance.js
import axios from 'axios';

const axiosInstance = axios.create();

axiosInstance.interceptors.request.use(async config => {
    const scrf = await axios.get('/csrf');
    const newCsrfToken = scrf.data.csrf_token;
    config.headers.common['X-CSRF-TOKEN'] = newCsrfToken;
    return config;
});
export default axiosInstance;
