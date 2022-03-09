import axios from 'axios';

export default axios.create({
    baseURL: 'http://pharm.test/api/v1/',
    headers: {'content-type': 'application/json'},
    withCredentials: true
});
