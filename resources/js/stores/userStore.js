import axios from 'axios'
import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null, // Brukerdata
        roles: [], // Roller
        permissions: [], // Tillatelser
        token: null, // Token
    }),
    actions: {
        setUser(userData) {
            console.log(userData);

            this.user = userData.user;
            this.roles = userData.roles || [];
            this.permissions = userData.permissions || [];
            this.token = userData.token || null;

            // Lagre til localStorage
            localStorage.setItem('user', JSON.stringify(userData.user || null));
            localStorage.setItem('roles', JSON.stringify(userData.roles || []));
            localStorage.setItem('permissions', JSON.stringify(userData.permissions || []));
            localStorage.setItem('token', userData.token || null);

            // Oppdater Axios Authorization-header hvis token finnes
            if (this.token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            }
        },
        clearUser() {
            this.user = null;
            this.roles = [];
            this.permissions = [];
            this.token = null;

            // Fjern fra localStorage
            localStorage.removeItem('user');
            localStorage.removeItem('roles');
            localStorage.removeItem('permissions');
            localStorage.removeItem('token');

            // Fjern Authorization-header
            delete axios.defaults.headers.common['Authorization'];
        },
        hydrateUser() {
            const user = safeParse(localStorage.getItem('user'));
            const roles = safeParse(localStorage.getItem('roles'));
            const permissions = safeParse(localStorage.getItem('permissions'));
            const token = localStorage.getItem('token');

            this.user = user || null;
            this.roles = roles || [];
            this.permissions = permissions || [];
            this.token = token || null;

            // Sett Authorization-header hvis token finnes
            if (this.token) {
                axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            }
        }
    },
    getters: {
        isLoggedIn: (state) => !!state.token, // True hvis token finnes (bruker er logget inn)
        hasRole: (state) => (role) => state.roles.includes(role),
        hasPermission: (state) => (permission) => state.permissions.includes(permission),
    },
});

function safeParse(data) {
    try {
        return JSON.parse(data);
    } catch (e) {
        console.warn('Ugyldig JSON i localStorage:', data);
        return null;
    }
}

