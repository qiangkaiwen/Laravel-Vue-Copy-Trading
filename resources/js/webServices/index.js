import axios from 'axios';

let webService = axios.create({
    baseURL: 'http://127.0.0.1:8000/api/auth'
});

export default webService;