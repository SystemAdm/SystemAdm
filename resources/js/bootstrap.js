import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//window.toastre = require('toastr');

// Sett opp axios for å inkludere token i headers
axios.interceptors.response.use(
    response => {
        // Hvis vi får en token i response, lagre den
        if (response.data?.token) {
            localStorage.setItem('token', response.data.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        }
        return response;
    },
    error => {
        return Promise.reject(error);
    }
);

// Sett token fra localStorage hvis den finnes
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}
