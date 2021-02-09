import axios from 'axios';

axios.interceptors.request.use(
    config => {
        const authToken = localStorage.getItem("admin-token");
        if (authToken) {
            config.headers.Authorization = `Bearer ${authToken}`;
        }
        return config;
    },
    err => Promise.reject(err)
);

let webService = axios.create({
    baseURL: 'http://10.10.11.83:8000/api/'
});

export default webService;