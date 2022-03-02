import axios from 'axios';

export default axios.create({
    baseURL: 'https://api.pharm.test/v1/',
    headers: {'content-type': 'application/json'},
    withCredentials: true
});
