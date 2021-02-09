import axios from 'axios';

let webService = axios.create({
    baseURL: 'http://10.10.11.83:8000/api/'
});

export default webService;