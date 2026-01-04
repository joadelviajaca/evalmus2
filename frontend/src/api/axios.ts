import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/api',
  headers: {
    Accept: 'application/json',
  },
});

export default api;
